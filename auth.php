<?php
session_start();

// If not logged in, kick to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
