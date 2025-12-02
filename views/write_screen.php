<?php 
require_once '../includes/auth_guard.php';
require_once '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새 글 작성 - LifeLog</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fafafa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"], input[type="date"], input[type="number"], 
        select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            font-family: inherit;
        }
        .photo-upload {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .photo-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .photo-preview img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #ddd;
        }
        button[type="submit"] {
            background: #4b6cb7;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        button[type="submit"]:hover {
            background: #3a5a9e;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4b6cb7;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ 새 기록 작성</h1>
        <p>작성자: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
        <hr>

        <form action="../write_process.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="date">작성 날짜 *</label>
                <input type="date" id="date" name="date" required value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="title">제목 (선택)</label>
                <input type="text" id="title" name="title" placeholder="제목을 입력하세요">
            </div>

            <div class="form-group">
                <label for="category">카테고리 *</label>
                <select id="category" name="category" required>
                    <option value="맛집">🍴 맛집</option>
                    <option value="카페">☕ 카페</option>
                    <option value="여행">✈️ 여행</option>
                    <option value="취미">🎨 취미</option>
                    <option value="일상">📝 일상</option>
                </select>
            </div>

            <div class="form-group">
                <label for="rating">평점 (1~5) *</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="place_name">장소 이름 (선택)</label>
                <input type="text" id="place_name" name="place_name" placeholder="예: 스타벅스 강남점">
            </div>

            <div class="form-group">
                <label for="place_address">장소 주소 (선택)</label>
                <input type="text" id="place_address" name="place_address" placeholder="예: 서울시 강남구...">
            </div>

            <div class="form-group">
                <label for="content">내용 *</label>
                <textarea id="content" name="content" rows="8" required placeholder="오늘 있었던 일을 기록하세요"></textarea>
            </div>

            <div class="form-group">
                <label>사진 업로드 (최대 2장)</label>
                <div class="photo-upload">
                    <input type="file" name="photos[]" id="photos" accept="image/*" multiple onchange="previewPhotos(event)">
                    <p style="margin: 10px 0 0 0; color: #777; font-size: 0.9rem;">
                        JPG, PNG, GIF 형식 지원 (최대 5MB)
                    </p>
                </div>
                <div class="photo-preview" id="photoPreview"></div>
            </div>

            <button type="submit">💾 저장하기</button>
        </form>

        <a href="../index.php" class="back-link">⬅ 메인으로 돌아가기</a>
    </div>

    <script>
        function previewPhotos(event) {
            const files = event.target.files;
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '';

            if (files.length > 2) {
                alert('최대 2장의 사진만 업로드할 수 있습니다.');
                event.target.value = '';
                return;
            }

            for (let i = 0; i < files.length && i < 2; i++) {
                const file = files[i];
                
                // 파일 크기 체크 (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('파일 크기는 5MB를 초과할 수 없습니다: ' + file.name);
                    event.target.value = '';
                    preview.innerHTML = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
