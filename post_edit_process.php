<?php
require_once 'includes/auth_guard.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("잘못된 접근입니다.");
}

$id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

// 권한 확인
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post || $post['user_id'] !== $user_id) {
    die("<script>alert('수정 권한이 없습니다.'); history.back();</script>");
}

// 트랜잭션 시작
$conn->begin_transaction();

try {
    // 1. 게시글 기본 정보 업데이트
    $title = $_POST['title'];
    $rating = intval($_POST['rating']);
    $content = $_POST['content'];
    $place_name = !empty($_POST['place_name']) ? $_POST['place_name'] : null;
    $place_address = !empty($_POST['place_address']) ? $_POST['place_address'] : null;
    
    $update_stmt = $conn->prepare("UPDATE posts SET title=?, rating=?, content=?, place_name=?, place_address=? WHERE id=?");
    $update_stmt->bind_param("sisssi", $title, $rating, $content, $place_name, $place_address, $id);
    $update_stmt->execute();
    
    // 2. 삭제할 사진 처리
    if (!empty($_POST['delete_photos'])) {
        foreach ($_POST['delete_photos'] as $photo_id) {
            $photo_id = intval($photo_id);
            
            // 파일 경로 조회
            $file_stmt = $conn->prepare("SELECT file_path FROM photos WHERE id = ? AND post_id = ?");
            $file_stmt->bind_param("ii", $photo_id, $id);
            $file_stmt->execute();
            $file_result = $file_stmt->get_result();
            $photo = $file_result->fetch_assoc();
            
            if ($photo) {
                // 실제 파일 삭제
                $file_path = __DIR__ . '/' . $photo['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                // DB에서 삭제
                $del_stmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
                $del_stmt->bind_param("i", $photo_id);
                $del_stmt->execute();
            }
        }
    }
    
    // 3. 새 사진 업로드
    if (isset($_FILES['new_photos']) && !empty($_FILES['new_photos']['name'][0])) {
        // 현재 사진 개수 확인
        $count_stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM photos WHERE post_id = ?");
        $count_stmt->bind_param("i", $id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $current_count = $count_result->fetch_assoc()['cnt'];
        
        $upload_dir = 'public/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $uploaded = 0;
        foreach ($_FILES['new_photos']['tmp_name'] as $key => $tmp_name) {
            if ($current_count + $uploaded >= 2) break;
            if (empty($tmp_name)) continue;
            
            $file_type = $_FILES['new_photos']['type'][$key];
            $file_size = $_FILES['new_photos']['size'][$key];
            $file_error = $_FILES['new_photos']['error'][$key];
            
            if ($file_error !== UPLOAD_ERR_OK) continue;
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("지원하지 않는 이미지 형식입니다.");
            }
            
            if ($file_size > $max_size) {
                throw new Exception("파일 크기는 5MB를 초과할 수 없습니다.");
            }
            
            // 파일 저장
            $ext = pathinfo($_FILES['new_photos']['name'][$key], PATHINFO_EXTENSION);
            $new_name = uniqid('img_', true) . '.' . $ext;
            $file_path = $upload_dir . $new_name;
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                // DB에 저장
                $photo_stmt = $conn->prepare("INSERT INTO photos (post_id, file_path) VALUES (?, ?)");
                $photo_stmt->bind_param("is", $id, $file_path);
                $photo_stmt->execute();
                $uploaded++;
            }
        }
    }
    
    $conn->commit();
    echo "<script>alert('수정되었습니다!'); window.location.href='views/post_view.php?id={$id}';</script>";
    
} catch (Exception $e) {
    $conn->rollback();
    die("<script>alert('오류: " . addslashes($e->getMessage()) . "'); history.back();</script>");
}
?>
