<?php
require_once 'auth_guard.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY date DESC");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>내 기록 목록</title>
</head>
<body>
<h2>📋 나의 기록들</h2>
<hr>

<a href="write_screen.php">✏️ 새 글 작성</a>
<br><br>

<?php if(count($posts)===0): ?>
    <p>작성된 기록이 없습니다.</p>
<?php else: ?>
    <ul>
        <?php foreach($posts as $p): ?>
            <li>
                <strong><?= $p['date'] ?></strong> | 
                <a href="post_view.php?id=<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['title'] ?: "[제목 없음]") ?>
                </a> 
                ⭐<?= $p['rating'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>
<a href="index.php">⬅ 메인으로</a>
</body>
</html>
