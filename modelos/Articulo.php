<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Articulo{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar regiustro
public function insertar($idcategoria,$codigo,$nombre,$stock,$descripcion,$imagen){
	$sql="INSERT INTO articulo (idcategoria,codigo,nombre,stock,descripcion,imagen,condicion)
	 VALUES ('$idcategoria','$codigo','$nombre','$stock','$descripcion','$imagen','1')";
	return ejecutarConsulta($sql);
}

public function editar($idarticulo,$idcategoria,$codigo,$nombre,$stock,$descripcion,$imagen){
	$sql="UPDATE articulo SET idcategoria='$idcategoria',codigo='$codigo', nombre='$nombre',stock='$stock',descripcion='$descripcion',imagen='$imagen' 
	WHERE idarticulo='$idarticulo'";
	return ejecutarConsulta($sql);
}
public function desactivar($idarticulo){
	$sql="UPDATE articulo SET condicion='0' WHERE idarticulo='$idarticulo'";
	return ejecutarConsulta($sql);
}
public function activar($idarticulo){
	$sql="UPDATE articulo SET condicion='1' WHERE idarticulo='$idarticulo'";
	return ejecutarConsulta($sql);
}

//metodo para mostrar registros
public function mostrar($idarticulo){
	$sql="SELECT * FROM articulo WHERE idarticulo='$idarticulo'";
	return ejecutarConsultaSimpleFila($sql);
}

//listar registros 
public function listar(){
	// $sql="SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,
    // a.descripcion,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON
    //  a.idcategoria=c.idcategoria";
    $sql="SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,
    (SELECT precio_venta FROM detalle_ingreso WHERE idarticulo=a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) 
    AS precio_venta,
    (SELECT precio_compra FROM detalle_ingreso WHERE idarticulo=a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) 
    AS precio_compra,
    a.descripcion,a.imagen,a.condicion FROM articulo a 
    INNER JOIN categoria c ON a.idcategoria=c.idcategoria";
	return ejecutarConsulta($sql);
}

//listar registros activos
public function listarActivos(){
	$sql="SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,
    a.descripcion,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON
     a.idcategoria=c.idcategoria WHERE a.condicion='1'";
	return ejecutarConsulta($sql);
}

//implementar un metodo para listar los activos, su ultimo precio y el stock(vamos a unir con el ultimo registro de la tabla detalle_ingreso)
// public function listarActivosVenta($idusuario){
// 	$sql="SELECT a.idarticulo,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,(SELECT precio_venta FROM detalle_ingreso WHERE idarticulo=a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) AS precio_venta,a.descripcion,a.imagen,a.condicion FROM articulo a INNER JOIN categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1'";
// 	return ejecutarConsulta($sql);
// }
public function listarActivosVenta($idusuario) {
    // Primero, verifica si el usuario tiene acceso a todas las categorías
    $sql_verificar_acceso = "SELECT COUNT(*) as total FROM categoria_permiso WHERE idusuario = $idusuario";
    $resultado = ejecutarConsulta($sql_verificar_acceso);
    $row = mysqli_fetch_assoc($resultado);
    
    if ($row['total'] > 0) {
        // El usuario tiene acceso a al menos una categoría, ejecuta la consulta con restricción de categoría
        $sql = "SELECT a.idarticulo, a.idcategoria, c.nombre as categoria, a.codigo, a.nombre, a.stock, 
                (SELECT precio_venta FROM detalle_ingreso WHERE idarticulo = a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) AS precio_venta, 
                a.descripcion, a.imagen, a.condicion 
                FROM articulo a 
                INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
                INNER JOIN categoria_permiso cp ON c.idcategoria = cp.idcategoria 
                WHERE a.condicion = '1' AND cp.idusuario = $idusuario";
    } else {
        // El usuario no tiene acceso a ninguna categoría, ejecuta la consulta sin restricción de categoría
        $sql = "SELECT a.idarticulo, a.idcategoria, c.nombre as categoria, a.codigo, a.nombre, a.stock, 
                (SELECT precio_venta FROM detalle_ingreso WHERE idarticulo = a.idarticulo ORDER BY iddetalle_ingreso DESC LIMIT 0,1) AS precio_venta, 
                a.descripcion, a.imagen, a.condicion 
                FROM articulo a 
                INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
                WHERE a.condicion = '1'";
    }
    
    return ejecutarConsulta($sql);
}


}
 ?>
