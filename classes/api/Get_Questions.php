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
// get raw input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF token missing']);
    exit;
}

if (!Security::checkCSRFToken($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF token invalid']);
    exit;
} die('CSRF attack');


$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['quiz_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'quiz_id missing']);
    exit;
}

$quizId = filter_var($input['quiz_id'], FILTER_VALIDATE_INT);
if (!$quizId) {
    http_response_code(400);
    echo json_encode(['error' => 'quiz_id invalid']);
    exit;
}


// here i get table of questions only from database by questionId sended
$pdo = Database::getConexion();
$sql = "SELECT ques.id,ques.question,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre 
			FROM questions ques JOIN quiz q 
			ON ques.quiz_id = q.id
			WHERE ques.quiz_id = ?
			GROUP BY ques.id,ques.question,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre";
        $result = $this->pdo->query($sql,[$quezId]);

//here i test answer of student to correct answer on database
$Questions = $result->fetchAll();

echo json_encode([
    'Table_Questions' => $Questions,
    'message' => 'Questions'
]);
