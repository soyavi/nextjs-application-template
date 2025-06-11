<?php
require_once '../config.php';

header('Content-Type: application/json');

// Attendance by event date
$stmt = $pdo->query("
    SELECT e.name AS event_name, a.check_in, COUNT(a.id) AS attendance_count
    FROM attendance a
    JOIN events e ON a.event_id = e.id
    GROUP BY e.name, DATE(a.check_in)
    ORDER BY DATE(a.check_in) DESC
");
$attendanceData = $stmt->fetchAll();

// Top active users
$stmt = $pdo->query("
    SELECT u.name, u.email, COUNT(a.id) AS attendances
    FROM users u
    JOIN attendance a ON u.id = a.user_id
    GROUP BY u.id
    ORDER BY attendances DESC
    LIMIT 10
");
$topUsers = $stmt->fetchAll();

// Event statistics
$stmt = $pdo->query("
    SELECT e.name, e.modality, COUNT(a.id) AS total_attendees,
    SUM(CASE WHEN e.modality = 'paid' THEN 1 ELSE 0 END) * 50 AS revenue -- assuming fixed price 50
    FROM events e
    LEFT JOIN attendance a ON e.id = a.event_id
    GROUP BY e.id
");
$eventStats = $stmt->fetchAll();

echo json_encode([
    'attendanceData' => $attendanceData,
    'topUsers' => $topUsers,
    'eventStats' => $eventStats,
]);
?>
