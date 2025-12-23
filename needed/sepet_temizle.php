<?php
session_start();
unset($_SESSION['sepet']);
header("Location: index.php");