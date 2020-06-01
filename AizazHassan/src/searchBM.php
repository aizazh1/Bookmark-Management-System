<?php
require "db.php";

$key = $_GET["key"];

try {
    if ($categoryId == 0) {
        $bookmarks = $db->query("select user.id uid, bookmark.id bid, name, title, note, created, url, category
                                  from bookmark, user 
                                  where user.id = bookmark.owner and user.id = {$_SESSION["user"]["id"]} and
                                  name like '$key%'
                                  order by $sort desc")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $bookmarks = $db->query("select user.id uid, bookmark.id bid, name, title, note, created, url,category
                                  from bookmark, user 
                                  where user.id = bookmark.owner and user.id = {$_SESSION["user"]["id"]} 
                                  and category=$categoryId and name like '$key%'
                                  order by $sort desc")->fetchAll(PDO::FETCH_ASSOC);
    }
    // echo json_encode($bookmarks);
} catch (PDOException $ex) {
    // echo json_encode(["error" => "select query error"]);
}
