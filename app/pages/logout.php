<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>"; 
session_destroy();
exit();
