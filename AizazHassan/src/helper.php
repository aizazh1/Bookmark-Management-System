<?php

function addMessage($msg) {
    $_SESSION["message"] = $msg ;
}

function displayMessage() {
    if (isset($_SESSION["message"])) {
        echo "<script> M.toast({html: '{$_SESSION["message"]}', classes: 'rounded', displayLength: 2000});</script>" ;
        unset($_SESSION["message"]) ;
    }
}

function authenticated() {
    return isset($_SESSION["user"]) ;
}
