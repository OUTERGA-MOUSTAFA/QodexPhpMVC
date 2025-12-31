<?php
/**
 * Classe Question_Student
 * Gère les opérations CRUD sur les catégories
 */

class Question_Student {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance();
    }
    public function getQuestion($idQuiz){
        $sql = "SELECT ques.id,ques.question,ques.correct_option,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre 
			FROM questions ques JOIN quiz q 
			ON ques.quiz_id = q.id
			WHERE ques.quiz_id = ?
			GROUP BY ques.id,ques.question,ques.correct_option,ques.option1,ques.option2,
			ques.option3,ques.option4, q.titre";
        $result = $this->pdo->query($sql,[$idQuiz]);
        return $result->fetchAll();
    }
    // getAllIdQuis
    public function getById($id) {
        $sql = "SELECT id FROM quiz WHERE id = ?";
        $result = $this->pdo->query($sql, [$id]);
        return $result->fetch();
    }
}