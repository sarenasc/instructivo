<?php


$serverName = "192.168.19.4"; // Nombre del servidor y instancia
$connectionInfo = array( "Database"=>"SistGestion", "UID"=>"sa", "PWD"=>"Robin@2021",'CharacterSet'=>'UTF-8');
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn ) {
    #echo "Conexión establecida.<br />";
}else{
    echo "Conexión no se pudo establecer.<br />";
    die( print_r( sqlsrv_errors(), true));
}
//coneccion para rescatar marcas
$serverName = "192.168.19.4"; // Nombre del servidor y instancia
$connectionInfo = array( "Database"=>"Facturador_ASanta_Almahue", "UID"=>"sa", "PWD"=>"Robin@2021",'CharacterSet'=>'UTF-8');
$conn2 = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn2 ) {
    #echo "Conexión establecida.<br />";
}else{
    echo "Conexión no se pudo establecer.<br />";
    die( print_r( sqlsrv_errors(), true));
}
//coneccion a DW_Almahue
$serverName = "192.168.19.4"; // Nombre del servidor y instancia
$connectionInfo = array( "Database"=>"DW_Almahue", "UID"=>"sa", "PWD"=>"Robin@2021",'CharacterSet'=>'UTF-8');
$conn3 = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn3 ) {
    #echo "Conexión establecida.<br />";
}else{
    echo "Conexión no se pudo establecer.<br />";
    die( print_r( sqlsrv_errors(), true));
}
