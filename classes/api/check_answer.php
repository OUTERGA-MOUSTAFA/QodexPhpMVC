<?php
require_once '../classes/Database.php';
require_once '../config/database.php';
require_once '../classes/Security.php';

Security::requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method Not Allowed']));
} 
Security::requireStudent();

//check CSRF TOKEN
$headers = verifyCSRFToken();
if (
    !isset($headers['X-CSRF-TOKEN']) ||
    !Security::checkCSRFToken($headers['X-CSRF-TOKEN'])
) {
    http_response_code(403);
    exit(json_encode(['error' => 'CSRF token invalid']));
}
$data = json_decode(file_get_contents("php://input"), true);

$questionId = (int)$data['question_id'];
$answer = trim($data['answer']);

if (!isset($questionId, $answer)){
    http_response_code(400);
    exit(json_encode(['error' => 'Bad Request']));
}

$questionId = filter_var($questionId, FILTER_VALIDATE_INT);
$answer = trim($answer);

if (!$questionId || $answer === '') {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid data']));
}

$pdo = Database::getConexion();


$sql = "SELECT correct_option FROM questions WHERE id = ?";
$stmt = $pdo->query($sql, [$questionId]);
$row = $stmt->fetch();

$isCorrect = ($row && $row['correct_option'] === $answer);
header('Content-Type: application/json');
echo json_encode([
    "correct" => $isCorrect,
    'message' => 'Good answer'
]);
