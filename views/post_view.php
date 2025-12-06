<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $user_id]);
$post = $stmt->fetch();

if(!$post) die("<script>alert('게시글을 찾을 수 없습니다.'); history.back();</script>");

$img_stmt = $pdo->prepare("SELECT file_path FROM photos WHERE post_id = ?");
$img_stmt->execute([$id]);
$photos = $img_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>기록 보기</title>
    <link rel="stylesheet" href="../public/css/calendar.css">
</head>
<body>
    <!-- 헤더 -->
    <header>
        <div class="logo">
            <a href="../index.php">
                <img src="../public/images/logo.png" alt="LifeLog" class="logo-img">
                <span class="logo-title">LifeLog</span>
            </a>
        </div>
        <div class="user-info">
            <span class="user-badge">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button class="btn btn-secondary" onclick="location.href='write_screen.php'">✏️ 기록하기</button>
            <button class="btn logout-btn" onclick="location.href='../logout.php'">로그아웃</button>
        </div>
    </header>

    <div class="page-center">
        <div class="content-card">
            <!-- 헤더 -->
            <div style="display:flex; justify-content:space-between; border-bottom:2px dashed #eee; padding-bottom:15px; margin-bottom:20px;">
                <span class="user-badge" style="background:#FF7675; color:white;">📅 <?=$post['date']?></span>
                <span class="user-badge">📂 <?=$post['category']?></span>
            </div>

            <h1 style="margin-bottom:10px;"><?=htmlspecialchars($post['title'] ?: '무제')?></h1>
            
            <div style="margin-bottom:30px; color:#888;">
                <span style="color:#FDCB6E; font-size:1.2rem;"><?= str_repeat("⭐", $post['rating']) ?></span>
                <?php if(!empty($post['place_name'])): ?>
                    <span style="margin-left:10px;">📍 <?=htmlspecialchars($post['place_name'])?></span>
                <?php endif; ?>
            </div>

            <!-- 사진 갤러리 -->
            <?php if(count($photos) > 0): ?>
                <div class="gallery">
                    <?php foreach($photos as $photo): ?>
                        <div class="photo-frame">
                            <img src="../<?= htmlspecialchars($photo['file_path']) ?>" alt="Photo">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- 본문 -->
            <div class="card-content" style="margin-bottom:40px; min-height:100px;">
                <?=nl2br(htmlspecialchars($post['content']))?>
            </div>

            <!-- 버튼 -->
            <div style="text-align:center; display:flex; gap:10px; justify-content:center;">
                <button class="btn btn-cancel" onclick="location.href='../index.php'">🏠 메인으로</button>
                <a href="post_edit.php?id=<?=$post['id']?>" class="btn btn-secondary">수정</a>
                <a href="../post_delete.php?id=<?=$post['id']?>" class="btn btn-delete" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
            </div>
        </div>
    </div>

</body>
</html>