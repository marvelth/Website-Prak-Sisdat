<?php

$server = "localhost";
$user = "root";
$password = "mysql";
$nama_database = "padjadjaran_express";

$conn = mysqli_connect($server, $user, $password, $nama_database);

if( !$conn ){
    die("Gagal terhubung dengan database: " . mysqli_connect_error());
}

?>