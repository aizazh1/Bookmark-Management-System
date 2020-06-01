<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require "db.php";
    require "file_upload.php";

    extract($_POST); //$name,$email,$password,$bday,$newName

    if (!isset($fileSuccess)) {
        // no file uploaded
        if (!isset($invalid)) {
            $newName = $_SESSION['user']['profile'];
        } else {
            if ($invalid == 'size') {
                addMessage("Size must be lower than 2MB!");
            } else if($invalid=='name'){
                addMessage("Filename invalid!");
            }else{
                addMessage("Wrong extension!");
            }
        }
    }

    if (empty($password)) {
        $password = $_SESSION['user']['password'];
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($bday)) {
        $bday = $_SESSION['user']['bday'];
    }

    if (!isset($invalid)) {
        $sql = "update user set name=?,email=?,password=?,bday=?,profile=? where id=?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$name, $email, $password, $bday, $newName, $_SESSION['user']['id']]);
            $updUser = $db->query("select * from user where id = {$_SESSION['user']['id']}")->fetch(PDO::FETCH_ASSOC);
            $_SESSION['user'] = [];
            $_SESSION['user'] = $updUser;
        } catch (PDOException $ex) {
            addMessage("Update User Failed!");
        }

        addMessage("User Updated!");
    }
}
header("Location: profile");
