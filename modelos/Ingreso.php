<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Ingreso{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar($idproveedor,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_compra,$idarticulo,$cantidad,$precio_compra,$precio_venta){
	$sql="INSERT INTO ingreso (idproveedor,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_compra,estado) VALUES ('$idproveedor','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_compra','Aceptado')";
	//return ejecutarConsulta($sql);
	 $idingresonew=ejecutarConsulta_retornarID($sql);
	 $num_elementos=0;
	 $sw=true;
	 while ($num_elementos < count($idarticulo)) {

	 	$sql_detalle="INSERT INTO detalle_ingreso (idingreso,idarticulo,cantidad,precio_compra,precio_venta) VALUES('$idingresonew','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_compra[$num_elementos]','$precio_venta[$num_elementos]')";

	 	ejecutarConsulta($sql_detalle) or $sw=false;

	 	$num_elementos=$num_elementos+1;
	 }
	 return $sw;
}

public function editar($idingreso,$idproveedor,$idusuario,$tipo_comprobante,$serie_comprobante,
$num_comprobante,$fecha_hora,$impuesto,$total_compra,$idarticulo,$cantidad,$precio_compra,$precio_venta){
	 	$sql="UPDATE ingreso  SET idproveedor='$idproveedor',idusuario='$idusuario',
		tipo_comprobante='$tipo_comprobante' ,serie_comprobante='$serie_comprobante' ,
	 	num_comprobante='$num_comprobante' ,fecha_hora='$fecha_hora' ,impuesto='$impuesto' 
		,total_compra='$total_compra' ,estado='Aceptado' WHERE idingreso='$idingreso'";
	 ejecutarConsulta($sql);
	 $num_elementos=0;
	 $sw=true;
	 while ($num_elementos < count($idarticulo)) {
		$sql1="SELECT * FROM detalle_ingreso WHERE idingreso='$idingreso'AND 
		idarticulo='$idarticulo[$num_elementos]'";
		$fila1=ejecutarConsultaSimpleFila($sql1);
		if (isset($fila1['idarticulo']) && !empty($fila1['idarticulo'])) { 
		$idarticulo1 = $fila1['idarticulo'];
		$cantidad1 = $fila1['cantidad'];
		$preciocompra1 = $fila1['precio_compra'];
		$precioventa1 = $fila1['precio_venta'];
	  if($cantidad[$num_elementos]==$fila1['cantidad']){
		  $sql_actualizacion = "UPDATE detalle_ingreso 
		  SET cantidad = '$cantidad[$num_elementos]', 
			  precio_venta = '$precio_venta[$num_elementos]', 
			  precio_compra = '$precio_compra[$num_elementos]'		 
		  WHERE idingreso = '$idingreso' AND idarticulo = '$idarticulo[$num_elementos]'";
		   ejecutarConsulta($sql_actualizacion) or $sw=false;
	  }
	 if($cantidad[$num_elementos]>$fila1['cantidad']){
	 $sql2="SELECT articulo.stock   from articulo where idarticulo='$idarticulo[$num_elementos]'";
	 $fila2=ejecutarConsultaSimpleFila($sql2);
	  $nuevostock=$cantidad[$num_elementos]-$fila1['cantidad'];
	  $stockfinal=$fila2['stock']+$nuevostock;
      $sql3="UPDATE articulo SET stock = '$stockfinal' WHERE idarticulo='$idarticulo[$num_elementos]'";
        ejecutarConsulta($sql3);
        $sql_actualizacion2 = "UPDATE detalle_ingreso 
		SET cantidad = '$cantidad[$num_elementos]', 
			precio_venta = '$precio_venta[$num_elementos]', 
			precio_compra = '$precio_compra[$num_elementos]'		 
		WHERE idingreso = '$idingreso' AND idarticulo = '$idarticulo[$num_elementos]'";
          ejecutarConsulta($sql_actualizacion2) or $sw=false;

}
 if($cantidad[$num_elementos]<$fila1['cantidad'])
{
	$sql4="SELECT articulo.stock   from articulo where idarticulo='$idarticulo[$num_elementos]'";
	$fila4=ejecutarConsultaSimpleFila($sql4);
	 $nuevostock=$fila1['cantidad']-$cantidad[$num_elementos];
	 $stockfinal=$fila4['stock']-$nuevostock;
  $sql4="UPDATE articulo SET stock = '$stockfinal' WHERE idarticulo='$idarticulo[$num_elementos]'";
  ejecutarConsulta($sql4);
  $sql_actualizacion3 = "UPDATE detalle_ingreso 
  SET cantidad = '$cantidad[$num_elementos]', 
	  precio_venta = '$precio_venta[$num_elementos]', 
	  precio_compra = '$precio_compra[$num_elementos]'		 
  WHERE idingreso = '$idingreso' AND idarticulo = '$idarticulo[$num_elementos]'";
   ejecutarConsulta($sql_actualizacion3) or $sw=false;

}}
else{
	$sql_detalle="INSERT INTO detalle_ingreso (idingreso,idarticulo,cantidad,precio_compra,precio_venta)
		 VALUES('$idingreso','$idarticulo[$num_elementos]','$cantidad[$num_elementos]',
      '$precio_compra[$num_elementos]','$precio_venta[$num_elementos]')";
	ejecutarConsulta($sql_detalle) or $sw=false;
}
  $num_elementos=$num_elementos+1;
	 }
	 return $sw;
}
public function anular($idingreso){
	$sql="UPDATE ingreso SET estado='Anulado' WHERE idingreso='$idingreso'";
	return ejecutarConsulta($sql);
}


//metodo para mostrar registros
public function mostrar($idingreso){
	$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario, i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario WHERE idingreso='$idingreso'";
	return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalle($idingreso){
	$sql="SELECT di.idingreso,di.idarticulo,a.nombre,di.cantidad,di.precio_compra,di.precio_venta FROM detalle_ingreso di INNER JOIN articulo a ON di.idarticulo=a.idarticulo WHERE di.idingreso='$idingreso'";
	return ejecutarConsulta($sql);
}

//listar registros
public function listar(){
	$sql="SELECT i.idingreso,DATE(i.fecha_hora) as fecha,i.idproveedor,p.nombre as proveedor,u.idusuario,u.nombre as usuario, i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,i.total_compra,i.impuesto,i.estado FROM ingreso i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN usuario u ON i.idusuario=u.idusuario ORDER BY i.idingreso DESC";
	return ejecutarConsulta($sql);
}

}

 ?>
