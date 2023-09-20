<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Venta{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento){
	$sql="INSERT INTO venta (idcliente,idusuario,tipo_comprobante,serie_comprobante,num_comprobante,fecha_hora,impuesto,total_venta,estado) VALUES ('$idcliente','$idusuario','$tipo_comprobante','$serie_comprobante','$num_comprobante','$fecha_hora','$impuesto','$total_venta','Aceptado')";
	//return ejecutarConsulta($sql);
	 $idventanew=ejecutarConsulta_retornarID($sql);
	 $num_elementos=0;
	 $sw=true;
	 while ($num_elementos < count($idarticulo)) {

	 	$sql_detalle="INSERT INTO detalle_venta (idventa,idarticulo,cantidad,precio_venta,descuento) VALUES('$idventanew','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','$precio_venta[$num_elementos]','$descuento[$num_elementos]')";

	 	ejecutarConsulta($sql_detalle) or $sw=false;

	 	$num_elementos=$num_elementos+1;
	 }
	 return $sw;
}
public function editar($idventa,$idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,
$fecha_hora,$impuesto,$total_venta,$idarticulo,$cantidad,$precio_venta,$descuento){
	$sql="UPDATE venta SET idcliente='$idcliente',idusuario='$idusuario',tipo_comprobante='$tipo_comprobante',
	serie_comprobante='$serie_comprobante',num_comprobante='$num_comprobante',fecha_hora='$fecha_hora',
	impuesto='$impuesto',total_venta='$total_venta',estado='Aceptado' WHERE idventa='$idventa'";
	 ejecutarConsulta($sql);
	 $num_elementos=0;
	 $sw=true;
	 while ($num_elementos < count($idarticulo)) {
		$sql1="SELECT * FROM detalle_venta WHERE idventa='$idventa'AND 
		idarticulo='$idarticulo[$num_elementos]'";
		$fila1=ejecutarConsultaSimpleFila($sql1);
		if (isset($fila1['idarticulo']) && !empty($fila1['idarticulo'])) { 
		$idarticulo1 = $fila1['idarticulo'];
		$cantidad1 = $fila1['cantidad'];
		$precioventa1 = $fila1['precio_venta'];
	  if($cantidad[$num_elementos]==$fila1['cantidad']){
		  $sql_actualizacion = "UPDATE detalle_venta 
		  SET cantidad = '$cantidad[$num_elementos]', 
			  precio_venta = '$precio_venta[$num_elementos]', 
			  descuento = '$descuento[$num_elementos]' 
		  WHERE idventa = '$idventa' AND idarticulo = '$idarticulo[$num_elementos]'";
		   ejecutarConsulta($sql_actualizacion) or $sw=false;
	  }
	 if($cantidad[$num_elementos]>$fila1['cantidad']){
	 $sql2="SELECT articulo.stock   from articulo where idarticulo='$idarticulo[$num_elementos]'";
	 $fila2=ejecutarConsultaSimpleFila($sql2);
	  $nuevostock=$cantidad[$num_elementos]-$fila1['cantidad'];
	  $stockfinal=$fila2['stock']-$nuevostock;
      $sql3="UPDATE articulo SET stock = '$stockfinal' WHERE idarticulo='$idarticulo[$num_elementos]'";
        ejecutarConsulta($sql3);
        $sql_actualizacion2 = "UPDATE detalle_venta 
          SET cantidad = '$cantidad[$num_elementos]', 
	    precio_venta = '$precio_venta[$num_elementos]', 
	     descuento = '$descuento[$num_elementos]' 
          WHERE idventa = '$idventa' AND idarticulo = '$idarticulo[$num_elementos]'";
          ejecutarConsulta($sql_actualizacion2) or $sw=false;

}
 if($cantidad[$num_elementos]<$fila1['cantidad'])
{
	$sql4="SELECT articulo.stock   from articulo where idarticulo='$idarticulo[$num_elementos]'";
	$fila4=ejecutarConsultaSimpleFila($sql4);
	 $nuevostock=$fila1['cantidad']-$cantidad[$num_elementos];
	 $stockfinal=$fila4['stock']+$nuevostock;
  $sql4="UPDATE articulo SET stock = '$stockfinal' WHERE idarticulo='$idarticulo[$num_elementos]'";
  ejecutarConsulta($sql4);
  $sql_actualizacion3 = "UPDATE detalle_venta 
  SET cantidad = '$cantidad[$num_elementos]', 
	  precio_venta = '$precio_venta[$num_elementos]', 
	  descuento = '$descuento[$num_elementos]' 
  WHERE idventa = '$idventa' AND idarticulo = '$idarticulo[$num_elementos]'";
   ejecutarConsulta($sql_actualizacion3) or $sw=false;

}}
else{
	$sql_detalle="INSERT INTO detalle_venta (idventa,idarticulo,cantidad,precio_venta,descuento)
	 VALUES('$idventa','$idarticulo[$num_elementos]','$cantidad[$num_elementos]','
	 $precio_venta[$num_elementos]','$descuento[$num_elementos]')";
ejecutarConsulta($sql_detalle) or $sw=false;
}
  $num_elementos=$num_elementos+1;
	 }
	 return $sw;
}
public function anular($idventa){
	$sql="UPDATE venta SET estado='Anulado' WHERE idventa='$idventa'";
	return ejecutarConsulta($sql);
}


//implementar un metodopara mostrar los datos de unregistro a modificar
public function mostrar($idventa){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE idventa='$idventa'";
	return ejecutarConsultaSimpleFila($sql);
}

public function listarDetalle($idventa){
	$sql="SELECT dv.idventa,dv.idarticulo,a.nombre,dv.cantidad,dv.precio_venta,
	dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal FROM detalle_venta dv
	 INNER JOIN articulo a ON dv.idarticulo=a.idarticulo WHERE dv.idventa='$idventa'";
	return ejecutarConsulta($sql);
} 
 
//listar registros
public function listar(){
	$sql="SELECT v.idventa,DATE(v.fecha_hora) as fecha,v.idcliente,p.nombre as cliente,u.idusuario,u.nombre as usuario, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,v.total_venta,v.impuesto,v.estado FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario ORDER BY v.idventa DESC";
	return ejecutarConsulta($sql);
}
public function codigo($tipo) {
    $sql = "SELECT COUNT(*) AS cantidad FROM venta WHERE tipo_comprobante = '$tipo'";
    $resultado = ejecutarConsulta($sql);
    $row = mysqli_fetch_assoc($resultado);
    $cantidad = $row['cantidad'] + 1;

    // Formatear el número con tres dígitos, agregando ceros al principio si es necesario
    $nuevoCodigo = sprintf("%03d", $cantidad);

    return $nuevoCodigo;
}



public function ventacabecera($idventa){
	$sql= "SELECT v.idventa, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(v.fecha_hora) AS fecha, v.impuesto, v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
	return ejecutarConsulta($sql);
}
// public function ventacabecera($idventa){
// 	$sql = "SELECT v.idventa, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idusuario, u.nombre AS usuario, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE_FORMAT(v.fecha_hora, '%Y-%m-%d') AS fecha, v.impuesto, v.total_venta FROM venta v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN usuario u ON v.idusuario=u.idusuario WHERE v.idventa='$idventa'";
// 	return ejecutarConsulta($sql);
// }

public function ventadetalles($idventa){
	$sql="SELECT a.nombre AS articulo, a.codigo,d.idarticulo as idart, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal FROM detalle_venta d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idventa='$idventa'";
         return ejecutarConsulta($sql);
}
// public function ventadetalles($idventa){
// 	$sql = "SELECT a.nombre AS articulo, a.codigo, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal FROM detalle_venta d INNER JOIN articulo a ON d.idarticulo=a.idarticulo WHERE d.idventa='$idventa'";
//     return ejecutarConsulta($sql);
// }


}

 ?>
