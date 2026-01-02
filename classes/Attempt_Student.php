<?php

/**
 * Classe Attempt_Student
 * Gère les attempt de student pour le quiz/ get attempt on this quiz
 */

include_once 'Database.php';
class Attempt_Student{
     private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }


function hasFinished($userId,$quiz_Id){
    $requette = "SELECT COUNT(*) AS Attempt FROM results WHERE etudiant_id = ? AND quiz_id = ?";
    $stmt = $this->db->query($requette,[$userId,$quiz_Id]);
    return $stmt->fetch();
}
    
}