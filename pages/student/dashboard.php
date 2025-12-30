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

// Vérifier que l'utilisateur est enseignant
Security::requireStudent();

// Variables pour la navigation
$currentPage = 'dashboard';
$pageTitle = 'Tableau de bord';

// Récupérer les données de l'utilisateur
$EtudiantId = $_SESSION['user_id'];
$userName = $_SESSION['user_nom'];

$success = $_SESSION['quiz_success'] ?? '';
$error = $_SESSION['quiz_error'] ?? '';
unset($_SESSION['quiz_success'], $_SESSION['quiz_error']);

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
                                    <form action="<?= htmlspecialchars('quiz.php') ?>" method="post">
                                        <input type="hidden" name="hidden" value='<?= htmlspecialchars($quiz["id"]) ?>'>
                                        <button type="submit" name='send' class="text-<?= $color?>-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</button>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                    <div onclick="showStudentSection('categoryQuizzes', 'JavaScript')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white">
                            <i class="fas fa-laptop-code text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">JavaScript</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Programmation interactive</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>8 quiz</span>
                                <span class="text-purple-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                            </div>
                        </div>
                    </div>

                    <div onclick="showStudentSection('categoryQuizzes', 'PHP/MySQL')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 text-white">
                            <i class="fas fa-database text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">PHP/MySQL</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Backend et bases de données</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>10 quiz</span>
                                <span class="text-green-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                            </div>
                        </div>
                    </div>

                    <div onclick="showStudentSection('studentResults')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
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

        <!-- Category Quizzes List -->
        <div id="categoryQuizzes" class="student-section hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <button onclick="showStudentSection('studentDashboard')" class="text-white hover:text-green-100 mb-4">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux catégories
                    </button>
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

        <!-- Quiz Taking Interface -->
        <div id="takeQuiz" class="student-section hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold mb-2" id="quizTitle">Les Bases de HTML5</h1>
                            <p class="text-green-100">Question <span id="currentQuestion">1</span> sur <span id="totalQuestions">20</span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-green-100 mb-1">Temps restant</div>
                            <div class="text-3xl font-bold" id="timer">30:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="questionText">
                        Quelle balise HTML5 est utilisée pour définir une section de navigation ?
                    </h3>

                    <div class="space-y-4">
                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;nav&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;navigation&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;menu&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;navbar&gt;</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button onclick="previousQuestion()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Précédent
                        </button>
                        <button onclick="nextQuestion()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Suivant<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    

<script src="../script.js"></script>
<script>
    let timeLeft = <?= $time_limit ?>;
    let timer = setInterval(() => {
        timeLeft--;
        document.getElementById('timer').textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('quizForm').submit();
        }
    }, 1000);


// ==================== STUDENT QUIZ FUNCTIONS ====================

// Load quizzes based on category
function loadQuizzesForCategory(categoryName) {
    const quizContainer = document.getElementById('quizListContainer');
    
    // Quiz data by category
    const quizData = {
        'HTML/CSS': [
            {
                title: 'Les Bases de HTML5',
                description: 'Testez vos connaissances sur les éléments HTML5 et leur utilisation',
                questions: quizQuestions['Les Bases de HTML5']?.length || 3,
                duration: 30,
                rating: 4.8,
                badge: 'bg-blue-100 text-blue-700'
            },
            {
                title: 'CSS Avancé',
                description: 'Flexbox, Grid, animations et responsive design',
                questions: quizQuestions['CSS Avancé']?.length || 1,
                duration: 25,
                rating: 4.6,
                badge: 'bg-blue-100 text-blue-700'
            }
        ],
        'JavaScript': [
            {
                title: 'JavaScript Fondamentaux',
                description: 'Variables, types de données, opérateurs et structures de contrôle',
                questions: quizQuestions['JavaScript Fondamentaux']?.length || 1,
                duration: 35,
                rating: 4.7,
                badge: 'bg-purple-100 text-purple-700'
            }
        ],
        'PHP/MySQL': [
            {
                title: 'PHP Basics',
                description: 'Syntaxe de base, variables et opérations en PHP',
                questions: 20,
                duration: 30,
                rating: 4.6,
                badge: 'bg-green-100 text-green-700'
            }
        ]
    };

    const quizzes = quizData[categoryName] || [];
    
    quizContainer.innerHTML = quizzes.map(quiz => `
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
    `).join('');
}

// ==================== SUBMIT QUIZ ====================
function submitQuiz() {
    clearInterval(timerInterval);
    
    if (!currentQuiz) return;
    
    // Calculate score
    const questions = quizQuestions[currentQuiz];
    let score = 0;
    let totalQuestions = questions.length;
    
    studentAnswers.forEach((answer, index) => {
        if (answer === questions[index].correct) {
            score++;
        }
    });
    
    const percentage = Math.round((score / totalQuestions) * 100);
    
    // Show results
    const resultHTML = `
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-6">
                <i class="fas fa-trophy text-4xl text-green-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Quiz Terminé!</h2>
            <p class="text-gray-600 mb-8">Vous avez complété <span class="font-bold">${currentQuiz}</span></p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Score</p>
                        <p class="text-4xl font-bold text-gray-900">${score}/${totalQuestions}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Pourcentage</p>
                        <p class="text-4xl font-bold ${percentage >= 70 ? 'text-green-600' : 'text-red-600'}">
                            ${percentage}%
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Statut</p>
                        <p class="text-xl font-bold ${percentage >= 70 ? 'text-green-600' : 'text-red-600'}">
                            ${percentage >= 70 ? 'Réussi' : 'Échoué'}
                        </p>
                    </div>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full" style="width: ${percentage}%"></div>
                </div>
            </div>
            
            <div class="mb-8 text-left">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Détail des réponses:</h3>
                ${questions.map((q, index) => {
                    const userAnswer = studentAnswers[index];
                    const isCorrect = userAnswer === q.correct;
                    const hasAnswered = userAnswer !== undefined;
                    
                    return `
                        <div class="mb-4 p-4 ${isCorrect ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg">
                            <p class="font-bold mb-2">${index + 1}. ${q.question}</p>
                            <p class="mb-1">Votre réponse: <span class="${isCorrect ? 'text-green-600' : 'text-red-600'} font-bold">
                                ${hasAnswered ? q.options[userAnswer] : 'Non répondue'}
                            </span></p>
                            ${!isCorrect ? `<p class="text-green-600 font-bold">Bonne réponse: ${q.options[q.correct]}</p>` : ''}
                        </div>
                    `;
                }).join('')}
            </div>
            
            <div class="flex gap-3 justify-center">
                <button onclick="showStudentSection('studentDashboard')" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-home mr-2"></i>Tableau de bord
                </button>
                <button onclick="showStudentSection('studentResults')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i>Voir tous mes résultats
                </button>
            </div>
        </div>
    `;
    
    // Replace quiz interface with results
    const quizContainer = document.querySelector('#takeQuiz .max-w-4xl');
    quizContainer.innerHTML = resultHTML;
    
    // Store result in localStorage (simulating database)
    saveQuizResult(currentQuiz, score, totalQuestions, percentage);
}
</script>
</body>
</html>