<?php

if ( $_SERVER["REQUEST_METHOD"] == "POST") {
    // var_dump($_POST) ;
    require "db.php" ;

    extract($_POST) ;
    try {
        // Validate email, name and password

        $sql = "insert into user ( name, email, password) values (?,?,?)" ;
        $stmt = $db->prepare($sql);
        $hash_password = password_hash($password, PASSWORD_DEFAULT) ;
        $stmt->execute([$name, $email, $hash_password]);

        addMessage("Registered");
        header("Location: loginForm") ;
        exit ;

    } catch(PDOException $ex) {
       addMessage("Insert Failed!") ;
       header("Location: registerForm");
       exit;
    }
}
