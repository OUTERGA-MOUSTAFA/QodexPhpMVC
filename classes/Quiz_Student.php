<?php

class Quiz_Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }


    
    public function getQuizCategories(){
         $sql = "SELECT c.id,c.nom,c.description,
            COUNT(CASE WHEN q.is_active = 1 THEN 1 END) AS total_actifs
        FROM categories c 
        LEFT JOIN quiz q ON c.id = q.categorie_id
        GROUP BY c.id  ORDER BY nom ASC";
        
        $result = $this->db->query($sql);
        return $result->fetchAll();
    }

    public function getQuizActive($idCategorie){
        $requtte = "SELECT q.id,
                            q.titre,
                            q.description,
                            c.nom,
                            COUNT(ques.id) AS Question_count
                            FROM quiz q 
                            JOIN categories c ON q.categorie_id = c.id 
                            LEFT JOIN questions ques ON ques.quiz_id = q.id
                            WHERE q.categorie_id = ? AND q.is_active = 1 
                            GROUP BY q.id, q.titre, q.description, c.nom";
        $stmt = $this->db->query($requtte,[$idCategorie]);
        // $stmt->execute();
        return $stmt->fetchAll();
    }

    // getAllQuestion
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }  

}