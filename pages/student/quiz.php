<?php
/**
 * Page: Gestion des quizs de Catégorie
 */
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category_Student.php';
require_once '../../classes/Quiz_Student.php';
require_once '../../classes/Results_Student.php';

// Vérifier que l'utilisateur est role= "etudiant", id_user, name_user
Security::requireStudent();
security::generateCSRFToken();
Security::againstHijacking();

// unset($_SESSION['quiz_success'], $_SESSION['quiz_error']);

// method should be post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// categorie field
if (!isset($_POST['categorie'])) {
    die("Accès non autorisé");
}
$categorieId = (int) $_POST['categorie'];
if ($categorieId <= 0) {
    die('invalide');
}
// check if it same categorie only is it same id
$category = new Categories();
$categoryData = $category->getById(strip_tags($_POST['categorie']));
//var_dump((int)$categoryData);
if(!$categoryData || (int)$_POST['categorie'] !== (int)$categoryData['id']) {
    die('Catégorie invalide you play!');
}


// get quiz activ
$quiz = new Quiz_Student();
$quizzes = $quiz->getQuizActive(strip_tags($_POST['categorie']));


$studentId = $_SESSION['user_id'];
// array of quiz completed
$completedQuizzes = [];

$results = new Result_Student();
foreach($quizzes as $quizItem) {
    //check that user passed this quiz to not let him pass agin
    $hasTakenQuiz = $results->isPassQuiz($quizItem['id'], $studentId);
    $completedQuizzes[$quizItem['id']] = $hasTakenQuiz;
}

?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/nav_student.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>take quiz - Espace Etudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
<!-- Category Quizzes List -->
<br><br>
<div id="categoryQuizzes" class="student-section">
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <a href="dashboard.php" class="text-white hover:text-green-100 mb-4">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux Dashboard
            </a>
            <h1 class="text-4xl font-bold mb-2" id="categoryTitle">
                <?php 
                if (empty($quizzes)) {
                    echo "Aucun Quiz Disponible!";
                } else {
                    echo htmlspecialchars($quizzes[0]['nom'] ?? $categoryData['nom']);
                }
                ?>
            </h1>
            <p class="text-xl text-green-100">Sélectionnez un quiz pour commencer</p>
        </div>
    </div>
    
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-gray-100">
        <?php if (empty($quizzes)): ?>
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Aucun quiz disponible</h3>
                <p class="text-gray-600">Aucun quiz n'est disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div id="quizListContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($quizzes as $quizItem): ?>
                    <?php 
                    $quizCompleted = $completedQuizzes[$quizItem['id']] ?? false;
                    ?>
                    
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    Quiz #<?= htmlspecialchars($quizItem['id']) ?>
                                </span>
                                <span class="text-yellow-500">
                                    <i class="fas fa-star"></i> 4.2 
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <?= htmlspecialchars($quizItem['titre']) ?>
                            </h3>
                            
                            <p class="text-gray-600 mb-4 text-sm">
                                <?= htmlspecialchars($quizItem['description'] ?? 'Pas de description') ?>
                            </p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span>
                                    <i class="fas fa-question-circle mr-1"></i>
                                    <?= htmlspecialchars($quizItem['Question_count']) ?> questions
                                </span>
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    30 min
                                </span>
                            </div>
                            <!-- if quiz completed add this div ofcard as passed quiz -->
                            <?php if($quizCompleted): ?>
                                <div class="mb-4">
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i> Terminé
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- button redirect to him results page-->
                            <?php if($quizCompleted): ?>
                                <form action="<?= htmlspecialchars('results.php')?>">
                                    <button type='submit'  name="go" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                        <i class="fas fa-chart-bar mr-2"></i> Voir les résultats
                                    </button>
                                </form>
                            <!-- else to questions page-->
                            <?php else: ?>
                                <form action="<?= htmlspecialchars('Questions.php')?>" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken()?>">
                                    <input type="hidden" name="quiz_id" value="<?= htmlspecialchars((int)$quizItem['id'])?>">

                                    <button type='submit' name="go"  class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                                        <i class="fas fa-play mr-2"></i> Commencer le Quiz
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>