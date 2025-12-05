<?php 
require_once '../includes/auth_guard.php';
require_once '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새 기록 작성 - LifeLog</title>
    <link rel="stylesheet" href="../public/css/calendar.css">
</head>
<body>
    <div class="page-center">
        <div class="content-card">
            <h1 style="text-align:center; color:var(--secondary);">✏️ 새 기록 작성</h1>
            <div style="text-align:center; margin-bottom:30px;">
                <span class="user-badge">@<?= htmlspecialchars($_SESSION['username']) ?></span>
            </div>

            <form action="../write_process.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label for="date">언제였나요? *</label>
                    <input type="date" id="date" name="date" required value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="title">제목</label>
                    <input type="text" id="title" name="title" placeholder="하루의 제목을 지어주세요">
                </div>

                <div class="form-group">
                    <label for="category">어떤 순간인가요? *</label>
                    <select id="category" name="category" required>
                        <option value="맛집">🍴 맛집 탐방</option>
                        <option value="카페">☕ 예쁜 카페</option>
                        <option value="여행">✈️ 즐거운 여행</option>
                        <option value="취미">🎨 나만의 취미</option>
                        <option value="일상">📝 소소한 일상</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rating">오늘의 평점 *</label>
                    <select id="rating" name="rating" required>
                        <option value="5">⭐⭐⭐⭐⭐ (완벽!)</option>
                        <option value="4">⭐⭐⭐⭐ (좋음)</option>
                        <option value="3">⭐⭐⭐ (보통)</option>
                        <option value="2">⭐⭐ (별로)</option>
                        <option value="1">⭐ (최악)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="place_name">장소 / 위치</label>
                    <input type="text" id="place_name" name="place_name" placeholder="장소 이름">
                    <input type="text" id="place_address" name="place_address" placeholder="주소 (선택)" style="margin-top:5px;">
                </div>

                <div class="form-group">
                    <label for="content">내용 *</label>
                    <textarea id="content" name="content" rows="6" required placeholder="자유롭게 기록해보세요."></textarea>
                </div>

                <div class="form-group">
                    <label>사진 (최대 2장)</label>
                    <div class="photo-upload-box" onclick="document.getElementById('photos').click()">
                        📸 사진 선택하기
                        <input type="file" name="photos[]" id="photos" accept="image/*" multiple onchange="previewPhotos(event)" style="display: none;">
                    </div>
                    <div class="photo-preview" id="photoPreview"></div>
                </div>

                <button type="submit" class="btn full-width">💾 저장하기</button>
            </form>

            <div style="text-align:center; margin-top:20px;">
                <a href="../index.php" style="color:#888; border-bottom:1px solid #ddd;">취소하고 돌아가기</a>
            </div>
        </div>
    </div>

    <script>
        function previewPhotos(event) {
            const files = event.target.files;
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '';
            if (files.length > 2) {
                alert('사진은 최대 2장까지만 선택할 수 있어요!');
                event.target.value = ''; return;
            }
            for (let i = 0; i < files.length && i < 2; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(files[i]);
            }
        }
    </script>
</body>
</html>