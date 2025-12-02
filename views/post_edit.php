<?php
require_once 'auth_guard.php';
require_once 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if(!$post) die("❌ 권한 없음");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 수정</title>
</head>
<body>
<h1>✏️ 게시글 수정</h1>

<form action="post_edit_process.php" method="POST">
    <input type="hidden" name="id" value="<?=$post['id']?>">

    <label>제목:</label><br>
    <input type="text" name="title" value="<?=htmlspecialchars($post['title'])?>" style="width:300px;"><br><br>

    <label>내용:</label><br>
    <textarea name="content" rows="6" cols="50"><?=htmlspecialchars($post['content'])?></textarea><br><br>

    <label>평점:</label><br>
    <input type="number" name="rating" min="1" max="5" value="<?=$post['rating']?>"><br><br>

    <button type="submit">💾 저장</button>
</form>

<br>
<a href="posts.php">⬅ 취소</a>
</body>
</html>
