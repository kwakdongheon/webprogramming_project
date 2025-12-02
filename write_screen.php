<?php 
require_once 'auth_guard.php';
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>새 글 작성</title>
</head>
<body>
    <h1>✏️ 새 기록 작성</h1>
    <p>작성자: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    <hr>

    <form action="write_process.php" method="POST">
        
        <label>작성 날짜:</label><br>
        <input type="date" name="date" required value="<?= date('Y-m-d') ?>"><br><br>

        <label>제목 (선택):</label><br>
        <input type="text" name="title" placeholder="제목을 입력하세요" style="width:300px;"><br><br>

        <label>카테고리:</label><br>
        <select name="category" required>
            <option value="맛집">맛집</option>
            <option value="카페">카페</option>
            <option value="여행">여행</option>
            <option value="취미">취미</option>
            <option value="일상">일상</option>
        </select><br><br>

        <label>평점 (1~5):</label><br>
        <input type="number" name="rating" min="1" max="5" required><br><br>

        <label>내용:</label><br>
        <textarea name="content" rows="6" cols="50" required placeholder="오늘 있었던 일을 기록하세요"></textarea><br><br>

        <button type="submit">💾 저장</button>
    </form>

    <hr>
    <a href="index.php">⬅ 메인으로</a>
</body>
</html>
