<?php

$host = "localhost";
$db_name = "rdv_db";
$username = "root";
$password = "";

try {
    $connexion = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>