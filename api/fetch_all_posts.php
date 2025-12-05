<?php
/**
 * 사용자의 모든 게시글 반환
 * Response: JSON { "posts": [...] }
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => '로그인이 필요합니다.', 'posts' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];

try {
    // 사용자의 모든 게시글 조회
    $stmt = $conn->prepare("
        SELECT id, title, content, category, rating, place_name, place_address, created_at, `date`
        FROM posts 
        WHERE user_id = ?
        ORDER BY `date` DESC, created_at DESC
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $post_id = $row['id'];
        
        // 해당 게시글의 사진 조회
        $img_stmt = $conn->prepare("
            SELECT file_path 
            FROM photos 
            WHERE post_id = ?
            ORDER BY uploaded_at ASC
        ");
        $img_stmt->bind_param("i", $post_id);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
        
        $images = [];
        while ($img_row = $img_result->fetch_assoc()) {
            $images[] = $img_row['file_path'];
        }
        
        $posts[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'category' => $row['category'],
            'rating' => $row['rating'],
            'place_name' => $row['place_name'],
            'place_address' => $row['place_address'],
            'images' => $images,
            'date' => $row['date']
        ];
    }
    
    echo json_encode(['posts' => $posts], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => '데이터 조회 실패: ' . $e->getMessage(), 
        'posts' => []
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
