
<?php

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category_Student.php';
require_once '../../classes/Quiz_Student.php';
require_once '../../classes/Question_Student.php';
//require_once '../../classes/Question_Student.php';


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
    $idquiz = $quizId->getById(strip_tags($_POST['hidden']));

    if((int)$_POST['hidden'] !== (int) $idquiz["id"]) {
        die('Quiz invalide');
    }
    
}
$questions = new Question_Student();
$questionsArray = $questions->getQuestion(strip_tags($_POST['hidden']));
// echo "<pre>";
// print_r($questionsArray);
// echo "<pre>";

$couterQuest = 0;
foreach($questionsArray as $ques){
    $couterQuest++;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions - Espace Etudiant</title>
        <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
<!-- Quiz Taking Interface -->
    <div id="takeQuiz" class="student-section">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold mb-2" id="quizTitle">Titre Quiz: <?= $questionsArray[0]["titre"]?></h1>
                            <p class="text-green-100">Question <span id="currentQuestion">1</span> sur <span id="totalQuestions"><?= $couterQuest;?></span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-green-100 mb-1">Temps restant</div>
                            <div class="text-3xl font-bold" id="timer">30:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6" id="questionText">
                    <?= $questionsArray[0]["question"]?>
                </h3>

                <div class="space-y-4">
                    <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                            </div>
                            <span class="text-lg"><?= $questionsArray[0]["option1"]?></span>
                        </div>
                    </div>

                    <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                <div class="w-4 h-4 rounded-full bg-green-600  option-selected"></div>
                            </div>
                            <span class="text-lg"><?= $questionsArray[0]["option2"]?></span> 
                        </div>
                    </div>

                    <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                            </div>
                            <span class="text-lg"><?= $questionsArray[0]["option3"]?></span>
                        </div>
                    </div>

                    <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                            </div>
                            <span class="text-lg"><?= $questionsArray[0]["question"]?></span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button onclick="previousQuestion()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Précédent
                    </button>
                    <form action="<?= htmlspecialchars('results.php')?>" method="post"></form>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Suivant<i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>