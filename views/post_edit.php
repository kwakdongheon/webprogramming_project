<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if(!$post) die("<script>alert('수정 권한이 없습니다.'); history.back();</script>");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
    <link rel="stylesheet" href="../public/css/calendar.css">
</head>
<body>
    <!-- 헤더 -->
    <header>
        <div class="logo">
            <a href="../index.php"><h1>📒 LifeLog</h1></a>
        </div>
        <div class="user-info">
            <span class="user-badge">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button class="btn btn-secondary" onclick="location.href='write_screen.php'">✏️ 기록하기</button>
            <button class="btn logout-btn" onclick="location.href='../logout.php'">로그아웃</button>
        </div>
    </header>
    <div class="page-center">
        <div class="content-card">
            <h1 style="text-align:center; color:var(--secondary);">✏️ 기록 수정하기</h1>

            <form action="../post_edit_process.php" method="POST">
                <input type="hidden" name="id" value="<?=$post['id']?>">

                <div class="form-group">
                    <label>제목</label>
                    <input type="text" name="title" value="<?=htmlspecialchars($post['title'])?>" required>
                </div>

                <div class="form-group">
                    <label>평점</label>
                    <select name="rating">
                        <option value="5" <?=$post['rating']==5 ? 'selected' : ''?>>⭐⭐⭐⭐⭐</option>
                        <option value="4" <?=$post['rating']==4 ? 'selected' : ''?>>⭐⭐⭐⭐</option>
                        <option value="3" <?=$post['rating']==3 ? 'selected' : ''?>>⭐⭐⭐</option>
                        <option value="2" <?=$post['rating']==2 ? 'selected' : ''?>>⭐⭐</option>
                        <option value="1" <?=$post['rating']==1 ? 'selected' : ''?>>⭐</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>내용</label>
                    <textarea name="content" rows="8" required><?=htmlspecialchars($post['content'])?></textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-cancel full-width" onclick="location.href='../index.php'">취소</button>
                    <button type="submit" class="btn full-width">💾 수정 완료</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>