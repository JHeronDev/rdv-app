<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function est_connecte() {
    return isset($_SESSION['utilisateur_id']);
}

function est_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function rediriger_si_non_connecte() {
    if (!est_connecte()) {
        header('Location: login.php');
        exit();
    }
}

function rediriger_si_non_admin() {
    if (!est_admin()) {
        header('Location: ../login.php');
        exit();
    }
}
?>

