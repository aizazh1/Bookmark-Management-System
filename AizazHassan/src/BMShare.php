<?php
extract($_POST);
if (!isset($userSelected)) {
    addMessage("You need to select atleast one user!");
} else {
    require "db.php";
    try {
        foreach ($userSelected as $user) {
            $sql = "insert into share values (?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user, $bmId]);
        }
        addMessage("Shared Successfully");
    } catch (PDOException $ex) {
        addMessage("Syntax Error");
    }
}
header('Location: main');
