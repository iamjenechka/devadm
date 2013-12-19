<?php

session_name('devadm');
session_start();
session_destroy();

header("Location: index.php")
?>
