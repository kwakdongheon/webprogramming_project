<?php
/**
 * 월별 게시글이 있는 날짜 목록 반환
 * GET params: year, month
 * Response: JSON { "dates": [1, 5, 12, ...] }
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => '로그인이 필요합니다.', 'dates' => []]);
    exit;
}

require_once '../includes/db.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');

// 유효성 검사
if ($month < 1 || $month > 12) {
    echo json_encode(['error' => '잘못된 월입니다.', 'dates' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

// 해당 월의 시작일과 종료일
$start_date = sprintf('%04d-%02d-01', $year, $month);
$end_date = date('Y-m-t', strtotime($start_date)); // 해당 월의 마지막 날

try {
    // 게시글이 있는 날짜(일) 추출
    $stmt = $conn->prepare("
        SELECT DISTINCT DAY(`date`) as day 
        FROM posts 
        WHERE user_id = ? 
        AND `date` BETWEEN ? AND ?
        ORDER BY day ASC
    ");
    
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = intval($row['day']);
    }
    
    echo json_encode(['dates' => $dates]);
    
} catch (Exception $e) {
    echo json_encode(['error' => '데이터 조회 실패', 'dates' => []]);
}

$conn->close();
?>
