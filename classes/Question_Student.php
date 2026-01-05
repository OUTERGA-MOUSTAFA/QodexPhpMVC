<?php
/**
 * Classe Question_Student
 * Gère les questions de quiz
 */

include_once 'Database.php';
class Question_Student {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
   $idQuiz = filter_var($data['question_id'], FILTER_VALIDATE_INT);
   if (!$idQuiz) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Quiz_id']);
    exit;
    }
        $sql = "SELECT ques.id,ques.question,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre 
			FROM questions ques JOIN quiz q 
			ON ques.quiz_id = q.id
			WHERE ques.quiz_id = ?
			GROUP BY ques.id,ques.question,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre";
        $result = $this->pdo->query($sql,[$idQuiz]);
         
    
    echo json_encode([
        'Questions' => $result->fetchAll();
    ]);

    public function getCountQuestionsThisQuiz($idQuiz){
        $sql = "SELECT  COUNT(ques.id) AS TotalQuestions
			FROM questions ques JOIN quiz q 
			ON ques.quiz_id = q.id
			WHERE ques.quiz_id = ?";
        $result = $this->pdo->query($sql,[$idQuiz]);
        return $result->fetchAll();
    }


    // getAllIdQuis
    public function getById($id) {
        $sql = "SELECT id, titre FROM quiz WHERE id = ?";
        $result = $this->pdo->query($sql, [$id]);
        return $result->fetch()['TotalQuestions'];
    }
}