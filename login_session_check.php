<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // for checking if the user is logged in and if the session has expired
    if (isset($_SESSION["login_activity_time"]) && (time() - $_SESSION["login_activity_time"] > 60 * 60 * 24)) {
        session_unset(); // unset $_SESSION variable for the run-time
        session_destroy(); // destroy session data in storage
        header("Location:" . __DIR__ . "/login.php");
        exit();
    }

?> 