<?php
session_start();

$id = intval($_GET['id']);
$delta = intval($_GET['delta']);

if (isset($_SESSION['keranjang'][$id])) {
    $_SESSION['keranjang'][$id]['qty'] += $delta;
    if ($_SESSION['keranjang'][$id]['qty'] <= 0) {
        unset($_SESSION['keranjang'][$id]);
    }
}
