<?php
/**
 * 게시글 작성 처리 (사진 업로드 포함)
 */
require_once 'includes/auth_guard.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("잘못된 접근입니다.");
}

$user_id = $_SESSION['user_id'];
$title = !empty($_POST['title']) ? $_POST['title'] : null;
$content = $_POST['content'];
$category = $_POST['category'];
$rating = intval($_POST['rating']);
$date = $_POST['date'];
$place_name = !empty($_POST['place_name']) ? $_POST['place_name'] : null;
$place_address = !empty($_POST['place_address']) ? $_POST['place_address'] : null;

// 유효성 검증
$allowed_categories = ['맛집', '카페', '여행', '취미', '일상'];
if (!in_array($category, $allowed_categories)) {
    die("잘못된 카테고리입니다.");
}

if ($rating < 1 || $rating > 5) {
    die("평점은 1~5 사이여야 합니다.");
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    die("잘못된 날짜 형식입니다.");
}

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // 게시글 저장
    $stmt = $conn->prepare("
        INSERT INTO posts (user_id, title, content, category, rating, date, place_name, place_address) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt === false) {
        throw new Exception("게시글 저장 준비 실패: " . $conn->error);
    }
    
    $stmt->bind_param(
        "isssisss", 
        $user_id, 
        $title, 
        $content, 
        $category, 
        $rating, 
        $date, 
        $place_name, 
        $place_address
    );
    
    if (!$stmt->execute()) {
        throw new Exception("게시글 저장 실패: " . $stmt->error);
    }
    
    $post_id = $conn->insert_id;
    $stmt->close();

    // 사진 업로드 처리 (최대 2장)
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $upload_dir = 'public/uploads/';
        
        // 업로드 디렉토리가 없으면 생성
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $max_photos = 2;

        $uploaded_count = 0;
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            if ($uploaded_count >= $max_photos) break;
            if (empty($tmp_name)) continue;

            $file_name = $_FILES['photos']['name'][$key];
            $file_size = $_FILES['photos']['size'][$key];
            $file_type = $_FILES['photos']['type'][$key];
            $file_error = $_FILES['photos']['error'][$key];

            // 파일 업로드 에러 체크
            if ($file_error !== UPLOAD_ERR_OK) {
                continue;
            }

            // MIME 타입 체크
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("지원하지 않는 이미지 형식입니다: {$file_name}");
            }

            // 파일 크기 체크
            if ($file_size > $max_size) {
                throw new Exception("파일 크기가 너무 큽니다: {$file_name}");
            }

            // 파일명 난수화 (보안)
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_filename = uniqid('img_', true) . '.' . $extension;
            $upload_path = $upload_dir . $new_filename;

            // 파일 이동
            if (move_uploaded_file($tmp_name, $upload_path)) {
                // DB에 사진 경로 저장
                $photo_stmt = $conn->prepare("INSERT INTO photos (post_id, file_path) VALUES (?, ?)");
                
                if ($photo_stmt === false) {
                    throw new Exception("사진 정보 저장 준비 실패: " . $conn->error);
                }
                
                $photo_stmt->bind_param("is", $post_id, $upload_path);
                
                if (!$photo_stmt->execute()) {
                    throw new Exception("사진 정보 저장 실패: " . $photo_stmt->error);
                }
                
                $photo_stmt->close();
                $uploaded_count++;
            }
        }
    }

    // 트랜잭션 커밋
    $conn->commit();

    echo "<script>
            alert('📌 기록이 저장되었습니다!');
            window.location.href='index.php';
        </script>";

} catch (Exception $e) {
    // 트랜잭션 롤백
    $conn->rollback();
    echo "<script>
            alert('오류 발생: " . addslashes($e->getMessage()) . "');
            history.back();
        </script>";
}

$conn->close();
?>
