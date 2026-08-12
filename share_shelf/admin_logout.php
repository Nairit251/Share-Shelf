<?php
session_start();
unset($_SESSION['admin_id'], $_SESSION['admin_name']);
header("Location: alogin.php");
exit;
