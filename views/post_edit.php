<?php
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if(!$post) die("<script>alert('수정 권한이 없습니다.'); history.back();</script>");

// 현재 업로드된 사진 조회
$img_stmt = $pdo->prepare("SELECT id, file_path FROM photos WHERE post_id = ?");
$img_stmt->execute([$post['id']]);
$existing_photos = $img_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
    <link rel="stylesheet" href="../public/css/calendar.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        #map {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid #E0E0E0;
        }
    </style>
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
            <h1 style="text-align:center; color:var(--secondary);">✏️ 기록 수정하기</h1>

            <form action="../post_edit_process.php" method="POST" enctype="multipart/form-data">
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

                <!-- Leaflet 지도 -->
                <div class="form-group">
                    <label>장소 검색 (지도)</label>
                    <div id="map"></div>
                    <p style="font-size:0.9rem; color:#888;">💡 검색해서 결과를 클릭하면 아래 주소가 자동으로 입력돼요!</p>
                </div>

                <div class="form-group">
                    <label>장소 / 위치</label>
                    <input type="text" id="place_name" name="place_name" placeholder="장소 이름" value="<?=htmlspecialchars($post['place_name'] ?? '')?>">
                    <input type="text" id="place_address" name="place_address" placeholder="주소 (선택)" value="<?=htmlspecialchars($post['place_address'] ?? '')?>" style="margin-top:5px;">
                </div>

                <!-- 현재 사진 관리 -->
                <?php if(!empty($existing_photos)): ?>
                <div class="form-group">
                    <label>현재 사진</label>
                    <div style="display:flex; gap:15px; flex-wrap:wrap;">
                        <?php foreach($existing_photos as $photo): ?>
                            <div style="position:relative; text-align:center;">
                                <img src="../<?= htmlspecialchars($photo['file_path']) ?>" style="width:150px; height:150px; object-fit:cover; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                                <label style="display:block; margin-top:8px; font-size:0.9rem;">
                                    <input type="checkbox" name="delete_photos[]" value="<?= $photo['id'] ?>">
                                    <span style="color:#ff7675;">삭제</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 새 사진 추가 -->
                <div class="form-group">
                    <label>새 사진 추가 (최대 2장)</label>
                    <div class="photo-upload-box" onclick="document.getElementById('newPhotos').click()" style="cursor:pointer; border:2px dashed #E0E0E0; padding:20px; text-align:center; border-radius:12px; background:#F8F9FA;">
                        📸 사진 선택하기
                        <input type="file" name="new_photos[]" id="newPhotos" accept="image/*" multiple onchange="previewNewPhotos(event)" style="display:none;">
                    </div>
                    <div id="newPhotoPreview" style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;"></div>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" class="btn btn-cancel full-width" onclick="location.href='../index.php'">취소</button>
                    <button type="submit" class="btn full-width">💾 수정 완료</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // 새 사진 미리보기
        function previewNewPhotos(event) {
            const files = event.target.files;
            const preview = document.getElementById('newPhotoPreview');
            preview.innerHTML = '';
            
            if (files.length > 2) {
                alert('사진은 최대 2장까지만 선택할 수 있어요!');
                event.target.value = '';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style = 'width:150px; height:150px; object-fit:cover; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(files[i]);
            }
        }
        
        // Leaflet 지도 초기화
        var map = L.map('map').setView([35.2315770, 129.0841310], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        var currentMarker = null;
        
        // 검색 UI 생성
        var searchControl = L.control({position: 'topright'});
        searchControl.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = `
                <div style="background:white; padding:10px; border-radius:4px; box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                    <input type="text" id="mapSearchInput" placeholder="지명 검색 (예시: 부산대)" 
                           style="width:250px; padding:8px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                    <div id="searchResults" style="margin-top:5px; max-height:200px; overflow-y:auto;"></div>
                </div>
            `;
            
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        searchControl.addTo(map);
        
        // Photon API 검색
        var searchTimeout;
        var searchInput = document.getElementById('mapSearchInput');
        var resultsDiv = document.getElementById('searchResults');
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            var query = e.target.value.trim();
            
            if (query.length < 2) {
                resultsDiv.innerHTML = '';
                return;
            }
            
            searchTimeout = setTimeout(function() {
                fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(query)}&limit=5`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.features || data.features.length === 0) {
                            resultsDiv.innerHTML = '<div style="padding:8px; color:#999;">검색 결과가 없습니다</div>';
                            return;
                        }
                        
                        resultsDiv.innerHTML = data.features.map(feature => {
                            var props = feature.properties;
                            var coords = feature.geometry.coordinates;
                            var name = props.name || '';
                            var city = props.city || '';
                            var street = props.street || '';
                            var displayName = [name, street, city].filter(Boolean).join(', ');
                            
                            return `
                                <div class="search-result-item" 
                                     data-lat="${coords[1]}" data-lon="${coords[0]}" 
                                     data-name="${name}" data-address="${displayName}"
                                     style="padding:8px; cursor:pointer; border-bottom:1px solid #eee; font-size:13px;">
                                    📍 ${displayName}
                                </div>
                            `;
                        }).join('');
                        
                        document.querySelectorAll('.search-result-item').forEach(item => {
                            item.addEventListener('click', function() {
                                var lat = parseFloat(this.dataset.lat);
                                var lon = parseFloat(this.dataset.lon);
                                var name = this.dataset.name;
                                var address = this.dataset.address;
                                
                                if (currentMarker) {
                                    map.removeLayer(currentMarker);
                                }
                                
                                map.setView([lat, lon], 16);
                                currentMarker = L.marker([lat, lon]).addTo(map);
                                
                                document.getElementById('place_name').value = name;
                                document.getElementById('place_address').value = address;
                                
                                searchInput.value = '';
                                resultsDiv.innerHTML = '';
                            });
                        });
                    })
                    .catch(err => {
                        console.error('검색 오류:', err);
                        resultsDiv.innerHTML = '<div style="padding:8px; color:#f44;">검색 중 오류 발생</div>';
                    });
            }, 500);
        });
    </script>
</body>
</html>