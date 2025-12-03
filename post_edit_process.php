<?php
require_once 'includes/auth_guard.php';
require_once 'includes/db.php';

$id = $_POST['id'];
$title = $_POST['title'];
$content = $_POST['content'];
$rating = $_POST['rating'];

$stmt = $pdo->prepare("UPDATE posts SET title=?, content=?, rating=? WHERE id=? AND user_id=?");
$stmt->execute([$title, $content, $rating, $id, $_SESSION['user_id']]);

echo "<script>
        alert('수정 완료!');
        window.location.href='post_view.php?id=$id';
      </script>";
?>
