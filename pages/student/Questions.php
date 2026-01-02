
<?php

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
require_once '../../classes/Category_Student.php';
require_once '../../classes/Attempt_Student.php';
require_once '../../classes/Quiz_Student.php';
require_once '../../classes/Question_Student.php';
//require_once '../../classes/Question_Student.php';

// Vérifier que l'utilisateur est role= "etudiant", id_user, name_user
Security::requireStudent();
security::generateCSRFToken();
// when refresh page method request change to GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz.php');
    exit;
}

   // var_dump((int)$_POST['quiz_id']);
 $quizIdInput = filter_input(INPUT_POST, 'quiz_id', FILTER_VALIDATE_INT);
if (!$quizIdInput) {
    die('Accès non autorisé');
}

// if (!isset($_POST['quiz_id'])) {
//     // var_dump($_POST['quiz_id']);
//     die("Accès non autorisé");
// }

$quiz_id = (int)$_POST['quiz_id'];
if($quiz_id<=0){
    die('Quiz invalide you play!');
}
 
$attempt = new Attempt_Student();
$atmp = $attempt->hasFinished($_SESSION['user_id'], $quiz_id);
//print_r($atmp['Attempt']);
if ($atmp['Attempt']>0) {
    exit('Vous avez déjà passé ce quiz');
}

$questions = new Question_Student();
$CountQuestions = $questions->getCountQuestionsThisQuiz($_POST['quiz_id']);
// get questions this quiz_id quiz_id
$questionsArray = $questions->getQuestion($_POST['quiz_id']);
if (!$questionsArray) {
    die('Questions invalide');
}
// echo "<pre>";
// print_r($CountQuestions);
// echo "<pre>";
// calcule combien question on ce quiz id hidden
$couterQuest = 0;
//$couterQuest = count($questionsArray);
//  ||
foreach($questionsArray as $ques){
    $couterQuest++;
}
$time_limit = 30;
$questionsJson = json_encode($questionsArray, JSON_UNESCAPED_UNICODE);
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
            <div class="bg-gradient-to-r from-green-600  to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold mb-2" id="quizTitle">Titre Quiz: <?= htmlspecialchars($questionsArray[0]["titre"])?></h1>
                            <p class="text-green-100">Question <span id="currentQuestion"></span> sur <span id="totalQuestions"><?= $CountQuestions[0]['TotalQuestions']?></span></p>
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
            <form id='form_id'>
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($_POST['quiz_id']) ?>">
                <!-- <input type="hidden" name="question_id" value=""> question_id on JS-->
                <div class="bg-white rounded-xl shadow-lg p-8">

                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="questionText">
                        
                        
                    </h3>

                    <div class="space-y-4">
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt1" value=''>
                            <label for="opt1" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt2" value=''>
                            <label for="opt2" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt3" value=''>
                            <label for="opt3" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt4" value=''>
                            <label for="opt4" class="answer-label"></label>
                        </div>

                    </div>

                    <div class="flex justify-between mt-8">
                        <button class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Précédent
                        </button>
                        
                        <button type="submit" id="next" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Suivant<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="array">

    </div>
    <script>
        // timer
        
        timeLeft = 30;
        let timer = setInterval(() => {
        timeLeft--;
        document.getElementById('timer').textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('form_id').submit();
        }
        }, 1000);

        // get csrf_token
        let csrfToken = document.querySelector('input[name="csrf_token"]').value
        let questions = <?= json_encode($questionsJson); ?>
        let index = 0;
        let quizId = <?= (int)$quiz_id ?>;
        let storageKey = "quiz_" + quizId + "_answers";

        if (!localStorage.getItem(storageKey)) {
            localStorage.setItem(storageKey, JSON.stringify([]));
        }

        document.getElementById("form_id").addEventListener("submit", function(e) {
            e.preventDefault();

            let selected = document.querySelector('input[name="answer"]:checked');
            if (!selected) {
                alert("Choisissez une réponse");
                return;
            }

            fetch("/classes/api/check_answer.php", {
                method: "POST",
                headers: {"Content-Type": "application/json","X-CSRF-TOKEN":csrfToken},
                
                body: JSON.stringify({
                    question_id: questions[index].id,
                    answer: selected.value
                })
            })
            .then(res => text.json())
            .then(data => {
                   console.log(data);
                let history = JSON.parse(localStorage.getItem(storageKey));
                history.push({
                    question: questions[index].question,
                    selected: selected.value,
                    correct: data.correct
                });
                localStorage.setItem(storageKey, JSON.stringify(history));

                index++;

                if (index >= questions.length) {
                    window.location.href = "result.php?quiz_id=" + quizId;
                    return;
                }

                loadQuestion();
            });
        });

        function loadQuestion() {
            let q = questions[index];
            document.getElementById("questionText").innerText = q.question;
            document.getElementById("currentQuestion").innerText = index + 1;

            let inputs = document.querySelectorAll('input[name="answer"]');
            let labels = document.querySelectorAll(".answer-label");

            inputs.forEach((input, i) => {
                input.checked = false
                input.value = q["option" + (i + 1)]
                labels[i].innerText = q["option" + (i + 1)];
            });
        }
        loadQuestion();
</script>

</body>
</html>