<?php
// 获取message的记录条数
require 'mysqli.php';

$search = $_GET['search'] ?? '';
if (empty($search)) {

    //select table_rows FROM tables where table_name='message' and table_schema='test'
    if ($res = $mysqli->query("select table_rows FROM information_schema.tables where table_name='message' and table_schema='test'")) {
        echo $res->fetch_all()[0][0]; // 第一条记录的第一个字段
    } else {
        die('Faile to query mysql: ' . $mysqli->error);
    }
} else {
    $sql = sprintf("select count(id) from message where title like '%%%s%%' or content like '%%%s%%' order by id desc", $search, $search);
    if ($res = $mysqli->query($sql)) {
        $num = $res->fetch_all()[0][0];
        echo $num;
    } else {
        die('Faile to query mysql: ' . $mysqli->error);
    }
}
