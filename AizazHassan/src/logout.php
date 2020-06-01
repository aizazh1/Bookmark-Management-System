<?php

  unset($_SESSION["user"]);
  $_SESSION = [] ;

  session_destroy(); // delete session file in tmp folder
  setcookie("PHPSESSID", "", 1, "/") ;

  header("Location: home") ;