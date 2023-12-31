var tabla;

//funcion que se ejecuta al inicio
function init(){
   mostrarform(false);
   listar();

   $("#formulario").on("submit",function(e){
   	guardaryeditar(e);
   });

   //cargamos los items al select cliente
   $.post("../ajax/venta.php?op=selectCliente", function(r){
   	$("#idcliente").html(r);
   	$('#idcliente').selectpicker('refresh');
   });

}

//funcion limpiar
function limpiar(){

	$("#idcliente").val("");
	$("#idventa").val("");
	$("#cliente").val("");
	$("#serie_comprobante").val("0001");
	$("#num_comprobante").val("");
	$("#impuesto").val("18");
	$("#total_venta").val("");
	$(".filas").remove();
	$("#total").html("0");

	//obtenemos la fecha actual
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha_hora").val(today);

	//marcamos el primer tipo_documento
	$("#tipo_comprobante").val("select");
	$("#tipo_comprobante").selectpicker('refresh');

}

//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();


	}else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}
function llenarcomprobante(tipo){
	console.log('ssss:'+tipo);
	$.post("../ajax/venta.php?op=codigo&tipo="+tipo, function(r){
		console.log("Respuesta del servidor:", r); 
		$("#num_comprobante").val(r);
	});
}

//funcion listar
function listar(){
	
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
		],
		"ajax":
		{
			url:'../ajax/venta.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":5,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
	limpiar();
}

function listarArticulos(){
	tabla=$('#tblarticulos').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [

		],
		"ajax": 
		{
			url:'../ajax/venta.php?op=listarArticulos',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":5,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e){
     e.preventDefault();//no se activara la accion predeterminada 
     //$("#btnGuardar").prop("disabled",true);
     var formData=new FormData($("#formulario")[0]);

     $.ajax({
     	url: "../ajax/venta.php?op=guardaryeditar",
     	type: "POST",
     	data: formData,
     	contentType: false,
     	processData: false,

     	success: function(datos){
     		bootbox.alert(datos);
     		mostrarform(false);

     		listar();
     	}
     });

     limpiar();
}

function mostrar(idventa){
	$.post("../ajax/venta.php?op=mostrar",{idventa : idventa},
		function(data,status)
		{
			data=JSON.parse(data);
			mostrarform(true);
 
			$("#idcliente").val(data.idcliente);
			$("#idcliente").selectpicker('refresh');
			$("#tipo_comprobante").val(data.tipo_comprobante);
			$("#tipo_comprobante").selectpicker('refresh');
			$("#serie_comprobante").val(data.serie_comprobante);
			$("#num_comprobante").val(data.num_comprobante);
			$("#fecha_hora").val(data.fecha);
			$("#impuesto").val(data.impuesto);
			$("#idventa").val(data.idventa);
			
			//ocultar y mostrar los botones
			$("#btnGuardar").hide();
			$("#btnCancelar").show();
			$("#btnAgregarArt").hide();
		});
	$.post("../ajax/venta.php?op=listarDetalle&id="+idventa,function(r){
		$("#detalles").html(r);
	});

}
//funcion para desactivar
function anular(idventa){
	bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result){
		if (result) {
			$.post("../ajax/venta.php?op=anular", {idventa : idventa}, function(e){
				bootbox.alert(e);
				listar();
				//tabla.ajax.reload();
			});
		}
	})
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var impuesto=18;
var cont=0;
var detalles=0;

$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto(){
    var tipo_comprobante = $("#tipo_comprobante option:selected").text();
    if (tipo_comprobante == 'Factura') {
        $("#impuesto").val(impuesto);
    } else if (tipo_comprobante == 'Boleta') {
        $("#impuesto").val(impuesto);
    } else {
        $("#impuesto").val("0");
    }
}


function agregarDetalle(idarticulo,articulo,precio_venta){
	var cantidad=1;
	var descuento=0;

	if (idarticulo!="") {
		var subtotal=cantidad*precio_venta;
		var fila='<tr class="filas" id="fila'+cont+'">'+
        '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')">X</button></td>'+
        '<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>'+
        '<td><input type="number" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>'+
        '<td><input type="text" name="precio_venta[]" id="precio_venta[]" value="'+precio_venta+'"></td>'+
        '<td><input type="textr" name="descuento[]" value="'+descuento+'"></td>'+
        '<td><span id="subtotal'+cont+'" name="subtotal">'+subtotal+'</span></td>'+
        '<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>'+
		'</tr>';
		cont++;
		detalles++;
		$('#detalles').append(fila);
		modificarSubtotales();

	}else{
		alert("error al ingresar el detalle, revisar las datos del articulo ");
	}
}
function editar(idventa) {
	$.post("../ajax/venta.php?op=mostrar", { idventa: idventa }, function (data, status) {
	  data = JSON.parse(data);
	  mostrarform(true);
	  console.log("editar");
  
	  // Llenar los campos de encabezado
	  $("#idcliente").val(data.idcliente);
	  $("#idcliente").selectpicker('refresh');
	  $("#tipo_comprobante").val(data.tipo_comprobante);
	  $("#tipo_comprobante").selectpicker('refresh');
	  $("#serie_comprobante").val(data.serie_comprobante);
	  $("#num_comprobante").val(data.num_comprobante);
	  $("#fecha_hora").val(data.fecha);
	  $("#impuesto").val(data.impuesto);
	  $("#idventa").val(data.idventa);
  
	  // Ocultar y mostrar los botones
	  $("#btnGuardar").show();
	  $("#btnCancelar").show();
	  $("#btnAgregarArt").show();
	});
  
	$.post("../ajax/venta.php?op=listarDetalleEditar&id=" + idventa, function (data, status) {
	  var detallesData = JSON.parse(data);
	  console.log(detallesData);
	  // Limpiar la tabla de detalles
  
	  // Recorrer los datos y construir las filas de la tabla detalle
	  detallesData.forEach(function (detalle, index) {
		var fila = '<tr class="filas" id="fila' + index + '">' +
		  '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + index + ')">X</button></td>' +
		  '<td><input type="hidden" name="idarticulo[]" value="' + detalle[0] + '">' + detalle[1] + '</td>' +
		  '<td><input type="number" name="cantidad[]" id="cantidad[]" value="' + detalle[2] + '"></td>' +
		  '<td><input type="text" name="precio_venta[]" id="precio_venta[]" value="' + detalle[3] + '"></td>' +
		  '<td><input type="text" name="descuento[]" value="' + detalle[4] + '"></td>' +
		  '<td><span id="subtotal' + index + '" name="subtotal">' + detalle[5] + '</span></td>' +
		  '<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>' +
		  '</tr>';
		$("#detalles").append(fila);
		detalles ++;
	  });
  
	  modificarSubtotales();
	});
  }
function modificarSubtotales(){
	var cant=document.getElementsByName("cantidad[]");
	var prev=document.getElementsByName("precio_venta[]");
	var desc=document.getElementsByName("descuento[]");
	var sub=document.getElementsByName("subtotal");


	for (var i = 0; i < cant.length; i++) {
		var inpV=cant[i];
		var inpP=prev[i];
		var inpS=sub[i];
		var des=desc[i];


		inpS.value=(inpV.value*inpP.value)-des.value;
		document.getElementsByName("subtotal")[i].innerHTML=inpS.value;
	}

	calcularTotales();
}

function calcularTotales(){
	var sub = document.getElementsByName("subtotal");
	var total=0.0;

	for (var i = 0; i < sub.length; i++) {
		total += document.getElementsByName("subtotal")[i].value;
	}
	$("#total").html("S/." + total);
	$("#total_venta").val(total);
	evaluar();
}

function evaluar(){

	if (detalles>0) 
	{
		$("#btnGuardar").show();
	}
	else
	{
		$("#btnGuardar").hide();
		cont=0;
	}
}

function eliminarDetalle(indice){
$("#fila"+indice).remove();
calcularTotales();
detalles=detalles-1;

}

init();