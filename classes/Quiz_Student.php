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


    
}