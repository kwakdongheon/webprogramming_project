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
            <button class="btn logout-btn" onclick="location.href='../logout.php'">로그아웃</button>
        </div>
    </header>

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
                        <option value="기타">📦 기타</option>
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

                <!-- Leaflet 지도 -->
                <div class="form-group">
                    <label>장소 검색 (지도)</label>
                    <div id="map"></div>
                    <p style="font-size:0.9rem; color:#888;">💡 검색해서 결과를 클릭하면 아래 주소가 자동으로 입력돼요!</p>
                </div>

                <div class="form-group">
                    <label for="place_name">장소 / 위치</label>
                    <input type="text" id="place_name" name="place_name" placeholder="장소 이름을 입력하세요">
                    <input type="text" id="place_address" name="place_address" placeholder="주소를 입력하세요 (선택)" style="margin-top:5px;">
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

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

        // Leaflet 지도 초기화
        var map = L.map('map').setView([35.2315770, 129.0841310], 15); // 부산대학교 기본 위치
        
        // OpenStreetMap 타일 레이어 추가
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        var currentMarker = null; // 현재 마커 저장
        
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
        
        // Photon API 검색 기능
        var searchTimeout;
        document.addEventListener('DOMContentLoaded', function() {
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
                    // Photon API 호출
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
                            
                            // 결과 클릭 이벤트
                            document.querySelectorAll('.search-result-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    var lat = parseFloat(this.dataset.lat);
                                    var lon = parseFloat(this.dataset.lon);
                                    var name = this.dataset.name;
                                    var address = this.dataset.address;
                                    
                                    // 기존 마커 제거
                                    if (currentMarker) {
                                        map.removeLayer(currentMarker);
                                    }
                                    
                                    // 새 마커 추가 및 지도 이동
                                    map.setView([lat, lon], 16);
                                    currentMarker = L.marker([lat, lon]).addTo(map);
                                    
                                    // 폼 입력
                                    document.getElementById('place_name').value = name;
                                    document.getElementById('place_address').value = address;
                                    
                                    // 검색창 초기화
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
        });

    </script>
</body>
</html>