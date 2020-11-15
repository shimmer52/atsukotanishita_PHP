<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    //REQUEST['id]が数字かどうか確認する
    if(preg_match('/^[0-9]+$/',$_REQUEST['id'])){
        $id = $_REQUEST['id'];
    } else {
        header('Location: index.php'); exit();
    }
    
    //投稿を検査する
    $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
    $messages->execute(array($id));
    $message = $messages->fetch();

    if ($message['member_id'] === $_SESSION['id']) {
        //削除する
        $del = $db->prepare('DELETE FROM posts WHERE id=?');
        $del->execute(array($id));
    }
}

header('Location: index.php');
exit();
