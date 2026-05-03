<?php
session_start();
if (isset($_SESSION['usuario'])) {
    session_destroy();
    header("Location:index.php?logout=1");  
    exit();
} else {
    header("Location:login_error.php");
    exit();
}
