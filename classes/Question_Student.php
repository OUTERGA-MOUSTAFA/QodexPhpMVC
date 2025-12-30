<?php
/**
 * Classe Category
 * Gère les opérations CRUD sur les catégories
 */

class Questions {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    public function getQuestion($idQuiz){
        $sql = "SELECT * FROM questions WHERE quiz_id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetchAll();
    }
    // getAllIdQuis
    public function getById($id) {
        $sql = "SELECT id FROM quiz WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }
}