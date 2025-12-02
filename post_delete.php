<?php
require_once 'auth_guard.php';
require_once 'db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

echo "<script>
        alert('삭제되었습니다.');
        window.location.href='posts.php';
      </script>";
?>
