<?php

/* =========================
   MYSQL CONNECTION
========================= */

$host = "localhost";

$user = "root";

$password = "";

$database = "database1";

/* =========================
   CONNECT DATABASE
========================= */

$conn = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

/* =========================
   CHECK CONNECTION
========================= */

if(!$conn){

    die("Connection Failed : " . mysqli_connect_error());

}

/* =========================
   UTF8
========================= */

mysqli_set_charset($conn,"utf8");

?>