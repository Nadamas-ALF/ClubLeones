<?php
$user = "ADMLEON";      // tu usuario de Oracle
$pass = "ADMLEON";   // tu contraseña
$conn = oci_connect($user, $pass, "localhost:1521/XE", "AL32UTF8");

if (!$conn) {
  $e = oci_error();
  echo "ERROR DE CONEXION: " . htmlentities($e['message']);
} else {
  echo "CONEXION EXITOSA A ORACLE";
  oci_close($conn);
}
?>