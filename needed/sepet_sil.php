<?php
session_start();

// Eğer bir ID gönderildiyse
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Eğer o ürün sepette varsa kaldır
    if (isset($_SESSION['sepet'][$id])) {
        unset($_SESSION['sepet'][$id]);
    }
}


header("Location: sepet.php");
exit();
?>