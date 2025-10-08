<?php
session_start();

if ($_POST && isset($_POST['captcha'])) {
    $_SESSION['captcha'] = $_POST['captcha'];
    echo "OK";
} else {
    echo "ERROR";
}
?>
