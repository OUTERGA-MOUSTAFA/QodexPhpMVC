<?php
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category_Student.php';
require_once '../../classes/Quiz_Student.php';
require_once '../../classes/Question_Student.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz.php');
    exit;
}

if (!isset($_POST['hidden'])) {
    // var_dump($_POST['hidden']);
    die("Accès non autorisé");
}else{
    // var_dump((int)$_POST['hidden']);
    $quizId =  new Categories();
    // get id of this id categorie 
    $idquiz = $quizId->getById(strip_tags($_POST['hidden']));

    if((int)$_POST['hidden'] !== (int) $idquiz["id"]) {
        die('Quiz invalide');
    }
    
}

$quizId = (int)$data['quiz_id'];
$userAnswers = json_decode($data['answers'], true);
$totalQuestions = (int)$data['total_questions'];

try {
    //
    $questions = new Question_Student();
    $allQuestions = $questions->getQuestion($quizId);
    
    // calcule
    $correctAnswers = 0;
    
    foreach ($allQuestions as $question) {
        $questionId = $question['id'];
        
        // إذا أجاب المستخدم على السؤال
        if (isset($userAnswers[$questionId])) {
            $userAnswer = $userAnswers[$questionId];
            
            // check answer
            if ($userAnswer === $question['correct_option']) {
                $correctAnswers++;
            }
        }
    }
    
    // calc score
    $score = ($correctAnswers / $totalQuestions) * 100;
    
    // save data on database
    // $quizStudent = new Quiz_Student();
    // $quizStudent->saveResult($_SESSION['user_id'], $quizId, $score, $correctAnswers, $totalQuestions);
    
    echo json_encode([
        'success' => true,
        'score' => $score,
        'correct' => $correctAnswers,
        'total' => $totalQuestions,
        'message' => 'Quiz soumis avec succès'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>