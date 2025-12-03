<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);
$post = $stmt->fetch();

if(!$post){
    die("❌ 게시글을 찾을 수 없거나 권한이 없습니다.");
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>게시글 보기</title>
</head>
<body>

<h2><?=htmlspecialchars($post['title'])?></h2>
<p><strong>날짜:</strong> <?=$post['date']?></p>
<p><strong>카테고리:</strong> <?=$post['category']?></p>
<p><strong>평점:</strong> ⭐<?=$post['rating']?></p>
<hr>
<p><?=nl2br(htmlspecialchars($post['content']))?></p>
<hr>

<a href="post_edit.php?id=<?=$post['id']?>">✏️ 수정</a> |
<a href="../post_delete.php?id=<?=$post['id']?>" onclick="return confirm('정말 삭제하시겠습니까?')">🗑 삭제</a> |
<a href="../posts.php">⬅ 목록</a>

</body>
</html>
