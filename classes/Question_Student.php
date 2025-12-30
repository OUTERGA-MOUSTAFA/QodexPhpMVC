<?php
/**
 * Classe Category
 * Gère les opérations CRUD sur les catégories
 */

class Categories {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    

}