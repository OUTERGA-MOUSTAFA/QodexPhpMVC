<?php
/**
 * Page: Gestion des Catégories
 * Permet de créer, modifier et supprimer des catégories
 */
require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category.php';
require_once '../../classes/Quiz_Student.php';

// Vérifier que l'utilisateur est requireStudent
 Security::requireStudent();

// Récupérer les données
$studentId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

// Messages
$success = $_SESSION['quiz_success'] ?? '';
$error = $_SESSION['quiz_error'] ?? '';
unset($_SESSION['quiz_success'], $_SESSION['quiz_error']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_POST['hidden'])) {
    var_dump($_POST['hidden']);
    die("Accès non autorisé");
}else{

    $categoryid =  new Category();
    $idCategorie = $categoryid->getById(strip_tags($_POST['hidden']));

    if((int)$_POST['hidden'] !== (int) $idCategorie['id']) {
        die('Quiz invalide');
    }

}

// $stmt = $pdo->prepare("SELECT * FROM quiz WHERE categorie_id = ? AND is_active = 1");
// $stmt->execute([$idCategorie]);
// $quizzes = $stmt->fetchAll();


// تستعملها فـ query
//echo "Category ID: " . $idCategorie;
?>
<?php include '../partials/header.php'; ?>

<?php include '../partials/nav_student.php'; print_r($quizzes); ?>

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
        <div id="categoryQuizzes" class="student-section">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <a href="dashboard.php" class="text-white hover:text-green-100 mb-4">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux Dashboard
                    </a>
                    <h1 class="text-4xl font-bold mb-2" id="categoryTitle">HTML/CSS</h1>
                    <p class="text-xl text-green-100">Sélectionnez un quiz pour commencer</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div id="quizListContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Quiz cards will be loaded dynamically -->
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 ${quiz.badge} text-xs font-semibold rounded-full">${categoryName}</span>
                    <span class="text-yellow-500"><i class="fas fa-star"></i> ${quiz.rating}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">${quiz.title}</h3>
                <p class="text-gray-600 mb-4 text-sm">${quiz.description}</p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span><i class="fas fa-question-circle mr-1"></i>${quiz.questions} questions</span>
                    <span><i class="fas fa-clock mr-1"></i>${quiz.duration} min</span>
                </div>
                <button onclick="startQuiz('${quiz.title}')" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-play mr-2"></i>Commencer le Quiz
                </button>
            </div>
        </div>
</body>
</html>