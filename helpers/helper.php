<?php

function sanitize(string $value) {
    return htmlspecialchars(stripslashes(trim($value)));
}

function flashMessage($key, $message = null) {

    if (isset($message)) {
        $_SESSION['flash'][$key] = $message;
    }
    else {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        return $message;
    }
}

?>