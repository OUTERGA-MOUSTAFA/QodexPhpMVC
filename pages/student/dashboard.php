<?php
/**
 * Page: Tableau de bord Enseignant
 * Affiche les statistiques et les quiz récents
 */

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category_Student.php';
require_once '../../classes/Quiz_Student.php';

// Vérifier que l'utilisateur est role= "etudiant", id_user, name_user
 Security::requireStudent();

// Variables pour la navigation
$currentPage = 'dashboard';
$pageTitle = 'Tableau de bord';

// Récupérer les données de l'utilisateur auqu'a ou besoin categorien pour ce etudiant
// $EtudiantId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

// Récupérer les statistiques
$categoryObj = new Categories();
$quizObj = new Quiz_Student();



// $categories = $categoryObj->getAll();
$quizzes = $quizObj->getQuizCategories();
// echo '<pre>';
// print_r($quizzes);
// echo '<pre>';
// $totalQuizzes = count($quizzes);
// $totalCategories = count($categories);

//Calculer le nombre total de questions
$totalQuestions = 0;
// foreach ($quizzes as $quiz) {
//     $totalQuestions += $quiz['questions_count'];
// }

// Calculer le nombre total de participants
$totalParticipants = 0;
// foreach ($quizzes as $quiz) {
//     $totalParticipants += $quiz['participants_count'];
// }

// Initiales pour l'avatar
$initials = strtoupper(substr($userName, 0, 1) . substr(explode(' ', $userName)[1] ?? '', 0, 1));
// print_r($EtudiantId);
// echo '<pre>';
// print_r($quizzes);
// echo '<pre>';
// var_dump($quizzes);

// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     header('Location: quiz.php');
//     exit;
// }

?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_student.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Espace Etudiant</title>
        <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    

 <!-- ESPACE ÉTUDIANT -->
 <div id="studentSpace" class="pt-16">
        
        <!-- Student Dashboard -->
        <div id="studentDashboard" class="student-section">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 class="text-4xl font-bold mb-4">Espace Étudiant</h1>
                    <p class="text-xl text-green-100 mb-6">Passez des quiz et suivez votre progression</p>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Catégories Disponibles</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                   <?php 
                   $colors = ['blue', 'purple', 'green', 'red', 'yellow', 'pink', 'indigo', 'teal'];
                   $logos = ['fa-solid fa-database', 'fa-regular fa-file-code', 'fa-brands fa-deviantart', 'fa-solid fa-terminal', 'fa-solid fa-microchip', 'fa-solid fa-server'];
                        foreach ($quizzes as $index => $quiz): 
                        $color = $colors[$index % count($colors)];
                        $logo = $logos[$index % count($logos)];

                    ?>
                        <div onclick="showStudentSection('categoryQuizzes')" class="bg-<?= $color?> rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                            <div class="bg-gradient-to-br from-<?= $color?>-600 to-<?= $color?>-500 p-6 text-white">
                                <i class="<?= $logo?> text-4xl mb-3"></i>
                                <h3 class="text-xl font-bold"><?=htmlspecialchars( $quiz['nom'],ENT_QUOTES, 'UTF-8')?></h3>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-600 mb-4"> desc: <?= htmlspecialchars($quiz["description"],ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i><?= $quiz["total_actifs"] ?> quiz</span>
                                    <form action="<?= 'quiz.php'?>" method="post">
                                        <input type="hidden" name="categorie" value='<?= $quiz["id"]?>'>
                                        <button type="submit" name='send' class="text-<?= $color?>-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</button>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                    

                    
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 text-white">
                            <i class="fas fa-chart-line text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">Mes Résultats</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Consultez vos performances</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-trophy mr-2"></i>24 tentatives</span>
                                <span class="text-orange-600 font-semibold group-hover:translate-x-2 transition-transform">Voir →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
    </div>
</body>
</html>