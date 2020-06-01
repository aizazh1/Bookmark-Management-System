<?php

if(empty($_FILES['profile']['name'])){
    $invalid="No file uploaded";
}else{
    $filename=$_FILES['profile']['name'];
    $tmp_file=$_FILES['profile']['tmp_name'];
    $bytes=$_FILES['profile']['size'];

    $extension=strtolower(pathinfo($filename,PATHINFO_EXTENSION));
    $whitelist=['gif','jpg','png','jpeg','bmp','jfif'];

    if(!in_array($extension,$whitelist)){
        $invalid="ext";
    }

    if($bytes>1024*1024*2||$bytes==0){
        $invalid="size";
    }

    if ( !preg_match("/^[\w.]+$/u", $filename)) {
        $invalid = "name" ; 
    }

    $newName = sha1("ctis256" . uniqid()) . "_" . $filename ;

    if(!isset($invalid)&&move_uploaded_file($tmp_file,"upload/$newName")){
        $fileSuccess=true;
    }
}

