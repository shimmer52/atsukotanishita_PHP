<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    //REQUEST['id]が数字かどうか確認する
    if(preg_match('/^[0-9]+$/',$_REQUEST['id'])){
        $post_id = $_REQUEST['id'];
    } else {
        header('Location: index.php'); exit();
    }
    $member_id = $_SESSION['id'];

    //該当postに同じlike_member_idがあるかどうか調べる
    $likes = $db->prepare('SELECT COUNT(*) AS cnt FROM like_counts WHERE like_members_id=? && posts_id=?');
    $likes->execute(array($member_id, $post_id));
    $like = $likes->fetch();

    if ($like['cnt'] > 0) {
        //該当ポストのいいね記録を削除
        $del = $db->prepare('DELETE FROM like_counts WHERE like_members_id=? && posts_id=?');
        $del->execute(array($member_id, $post_id));
    } else {
        //該当ポストにいいね記録を残す
        $add = $db->prepare('INSERT INTO like_counts SET like_members_id=?, posts_id=?');
        $add->execute(array($member_id, $post_id));
    }
}

header('Location: index.php');
exit();
