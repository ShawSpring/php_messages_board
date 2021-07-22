<?php
// 查询message数据库中的 记录(with limit)

require 'mysqli.php';

$num = intval($_GET['num']);
$page = intval($_GET['page']);
if($num<4||$page<1){
    $num = 4;
    $page =1;
}
$search = $_GET['search']??'';
if(!empty($search)){
    // sprintf 中转义字符不是 \  而是 %  
    $sql = sprintf("select id,title,content from message where title like '%%%s%%' or content like '%%%s%%' order by id desc limit %d,%d ",$search,$search,($page-1)*$num,$num);
}else{

    $sql = sprintf("select id,title,content from message order by id desc limit %d,%d ",($page-1)*$num,$num);
}
if($res = $mysqli->query($sql)){
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $arr['data'] = $rows;

    echo json_encode($rows);
}else{
    die("Failed to query mysql: ".$mysqli->error);
}

