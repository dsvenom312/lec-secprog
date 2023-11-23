<?php
function checkSessionExpiration() {
    if (isset($_SESSION['login_time'])) {
        $session_duration = time() - $_SESSION['login_time'];
        $max_session_duration = 60 * 60; // 60 minutes in seconds

        // If session duration exceeds 12 hours, destroy the session
        if ($session_duration > $max_session_duration) {
            session_unset();
            session_destroy();
            header("Location: ../Pages/login.php");
            exit();
        } else {
            $_SESSION['login_time'] = time(); // Update login time for the current session
        }
    }
}