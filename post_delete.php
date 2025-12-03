<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('로그인이 필요합니다.'); window.location.href='views/login.php';</script>";
  exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

echo "<script>
        alert('삭제되었습니다.');
        window.location.href='posts.php';
      </script>";
?>
