<?php
session_start();
session_unset();
session_destroy();

// Mrejeshe mteja kwenye ukurasa wa Login
header("Location: login.html");
exit();
?>
