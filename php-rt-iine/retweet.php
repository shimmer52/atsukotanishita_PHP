<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id'])) {
    //REQUEST['id]が数字かどうか確認する
    if(preg_match('/^[0-9]+$/',$_REQUEST['id'])){
        $id = $_REQUEST['id'];
    } else {
        header('Location: index.php'); exit();
    }

    //投稿を取得する
    $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
    $messages->execute(array($id));
    $message = $messages->fetch();

    //既にRTしているか調べる
    $posts_rt = $db->prepare('SELECT COUNT(*) AS cnt FROM posts WHERE  rt_member_id=? && rt_post_id=?');
    $posts_rt->execute(array($_SESSION['id'], $message['id']));
    $post_rt = $posts_rt->fetch();

    if($post_rt['cnt'] > 0){
        //RTを削除する
        $del = $db->prepare('DELETE FROM posts WHERE rt_member_id=? && rt_post_id=?');
        $del->execute(array($_SESSION['id'], $message['id']));
    } else {
        //リツイートとしてpostsに格納する
        $retweet = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, rt_member_id=?, rt_post_id=?, created=?');
        $retweet->execute(array(
            $message['member_id'],
            $message['message'],
            $message['reply_post_id'],
            $_SESSION['id'],
            $message['id'],
            $message['created']
        ));
    }

}

header('Location: index.php'); exit();
