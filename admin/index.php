<?php
session_start();
if (empty($_SESSION['admin_token'])) {
  header('Location: /admin/login.php');
} else {
  header('Location: /admin/dashboard.php');
}
exit;
