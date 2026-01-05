<?php
require_once '../Database.php';
require_once '../config/database.php';
require_once '../Security.php';

header('Content-Type: application/json; charset=utf-8');

Security::requireStudent();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method Not Allowed']));
}

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

if (!is_array($data) || !isset($data['question_id'], $data['answer'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$questionId = filter_var($data['question_id'], FILTER_VALIDATE_INT);
$answer = trim($data['answer']);

if (!$questionId || $answer === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}


// here i get correct_option from database by questionId sended
$pdo = Database::getConexion();
$sql = "SELECT correct_option FROM questions WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$questionId]);
$row = $stmt->fetch();

// $sql = "SELECT correct_option FROM questions WHERE id = ?";
// $stmt = $pdo->query($sql, [$questionId]);

//here i test answer of student to correct answer on database
$isCorrect = $row && $row['correct_option'] === $answer;

echo json_encode([
    'correct' => $isCorrect,
    'message' => 'Good answer'
]);
