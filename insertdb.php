<?php
require 'mysqli.php';
$title = htmlspecialchars(trim($_POST['title']));
$content = htmlspecialchars(trim($_POST['content']));
if(empty($content)){
    die(0);
}
$stmt = $mysqli->prepare("INSERT INTO message(`title`,`content`) VALUES(?,?)");
$stmt->bind_param("ss",$title,$content);
if($stmt->execute()){
    echo 1;
}else{
    echo 0;
}
