<?php
/**
 * Classe Categories Student
 * Gère les opérations
 */

class Categories {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
   // getAllByStudent
   
    public function getAllByTeacher($teacherId) {
        $sql = "SELECT c.*, 
                       COUNT(DISTINCT q.id) as quiz_count
                FROM categories c
                LEFT JOIN quiz q ON c.id = q.categorie_id
                WHERE c.created_by = ?
                GROUP BY c.id
                ORDER BY c.created_at DESC";
        
        $result = $this->db->query($sql, [$teacherId]);
        return $result->fetchAll();
    }
    
   // getAllCtegorie
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result->fetch();
    }    
     
    // Récupère toutes les catégories (pour les sélections)
    public function getAll() {
        $sql = "SELECT * FROM categories ORDER BY nom ASC";
        $result = $this->db->query($sql);
        return $result->fetchAll();
    }
}