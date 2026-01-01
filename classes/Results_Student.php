<?php
/**
 * Classe Result
 * Gère les résultats des quiz (US7 - Voir ses résultats)
 * 
 * SÉCURITÉ: L'utilisateur ne peut voir QUE ses propres résultats
 */

class Result_student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère les résultats d'un étudiant (ses propres résultats SEULEMENT)
     * @param int $etudiantId - L'ID de l'étudiant
     * @return array - Liste des résultats
     */
    public function getMyResults($etudiantId) {
        $sql = "SELECT r.*, q.titre as quiz_titre, c.nom as categorie_nom
                FROM results r
                LEFT JOIN quiz q ON r.quiz_id = q.id
                LEFT JOIN categories c ON q.categorie_id = c.id
                WHERE r.etudiant_id = ?
                ORDER BY r.created_at DESC";
        
        $result = $this->db->query($sql, [$etudiantId]);
        return $result->fetchAll();
    }
    
    /**
     * Récupère un résultat par ID (vérifie que c'est bien le propriétaire)
     * @param int $resultId
     * @param int $etudiantId
     * @return array|false
     */
    public function getById($etudiantId) {
        $sql = "SELECT r.*, q.titre as quiz_titre
                FROM results r
                LEFT JOIN quiz q ON r.quiz_id = q.id
                WHERE r.id = ? AND r.etudiant_id = ?";
        
        $result = $this->db->query($sql, [$etudiantId]);
        return $result->fetch();
    }

    public function isPassQuiz($quizId,$studentId){
        $requette = "SELECT etudiant_id FROM results WHERE etudiant_id = ? AND quiz_id = ?";
        $stm = $this->db->query($requette,[$studentId,$quizId]);
        return $stm->fetchAll();
    }

}
