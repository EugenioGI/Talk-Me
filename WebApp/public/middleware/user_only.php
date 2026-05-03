<?php
require_once 'auth.php';

if ($_SESSION['rol'] !== 'usuario') {
    header("Location: login_error.php");
    exit();
}