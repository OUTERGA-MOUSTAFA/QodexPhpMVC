
<?php

require_once '../../config/database.php';
require_once '../../classes/Database.php';
require_once '../../classes/Security.php';
// require_once '../../classes/Category_Student.php';
require_once '../../classes/Attempt_Student.php';
require_once '../../classes/Quiz_Student.php';
// require_once '../../classes/Question_Student.php';
//require_once '../../classes/Question_Student.php';

// Vérifier que l'utilisateur est role= "etudiant", id_user, name_user
Security::requireStudent();
//Security::generateCSRFToken();
Security::againstHijacking();
// when refresh page method request change to GET
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz.php');
    exit;
}
//check CSRF TOKEN
$token = $_POST['csrf_token'];
// $headers = Security::verifyCSRFToken($token);
// if ($headers !== true) {
//     die('Accès non autorisé');
// }

if (!Security::verifyCSRFToken($token)) {
    die('Accès non autorisé');
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
$token = $_POST['csrf_token'];

if($quiz_id<=0){
    die('Quiz invalide you play!');
}

$attempt = new Attempt_Student();
$atmp = $attempt->hasFinished($_SESSION['user_id'], $quiz_id);
//print_r($atmp['Attempt']);
if ($atmp['Attempt']>0) {
    echo "<script>alert('Vous avez déjà passé ce quiz')</script>";
    exit();
}



// $questions = new Question_Student();
// $CountQuestions = $questions->getCountQuestionsThisQuiz($_POST['quiz_id']);
// // get questions this quiz_id quiz_id
// $questionsArray = $questions->getQuestion($_POST['quiz_id']);
// if (!$questionsArray) {
//     die('Questions invalide');
// }
// echo "<pre>";
// print_r($CountQuestions);
// echo "<pre>";
// calcule combien question on ce quiz id hidden
// $couterQuest = 0;
//As total_Questions on $questionsArray['total_Questions']
//$couterQuest = count($questionsArray);
//  ||
// foreach($questionsArray as $ques){
//     $couterQuest++;
// }
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
                            <h1 class="text-3xl font-bold mb-2" id="quizTitle">Titre Quiz: </h1>
                            <p class="text-green-100">Question <span id="currentQuestion"></span> sur <span id="totalQuestions"></span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-green-100 mb-1">Temps restant</div>
                            <div class="text-3xl font-bold" id="timer">1:20</div>
                        </div>
                    </div>
                </div>
                <!-- to get values of quiz_id and csrf_token -->
                <input type="hidden" id="quiz_id" value="<?=(int) $quiz_id ?>">
                <input type="hidden" id="csrf_token" value="<?= htmlspecialchars($token) ?>">
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
                            <input type="radio" name="answer" id="opt1">
                            <label for="opt1" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt2">
                            <label for="opt2" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt3">
                            <label for="opt3" class="answer-label"></label>
                        </div>
                        
                        <div class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <input type="radio" name="answer" id="opt4">
                            <label for="opt4" class="answer-label"></label>
                        </div>

                    </div>

                    <div class="flex justify-between mt-8">
                        <button class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Précédent
                        </button>
                        
                        <button type="submit" id="Suivant" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
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
        
            let questions = [];
            const QuizId = document.getElementById('quiz_id').value;
            const csrfToken = document.getElementById('csrf_token').value;

            console.log(QuizId, csrfToken);
            
            let currentIndex = 0;

            fetch("/classes/api/Get_Questions.php", {
                method: "POST",
                headers: {"Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ quiz_id: QuizId })
            })
            .then(res => res.json())
            .then(data => {
                questions = data;
                document.getElementById('totalQuestions').innerText = questions.length;
                loadQuestion();
            });

            function loadQuestion() {
                const q = questions[currentIndex];

                document.getElementById("questionText").innerText = q.question;
                document.getElementById("currentQuestion").innerText = currentIndex + 1;

                const inputs = document.querySelectorAll('input[name="answer"]');
                const labels = document.querySelectorAll('.answer-label');

                inputs.forEach((input, i) => {
                    input.checked = false;
                    input.value = q['option_' + (i + 1)];
                    labels[i].innerText = q['option_' + (i + 1)];
                });

                const btn = document.getElementById("Suivant");
                btn.innerText = (currentIndex === questions.length - 1) ? "Submit" : "Suivant";
            }

        // timer
        let timeLeft = 100;
        let timer = setInterval(() => {
        timeLeft--;
        document.getElementById('timer').textContent = timeLeft;

        if (timeLeft <= 0) {
            clearInterval(timer);
            document.getElementById('form_id').submit();
        }
        }, 1000);

        // get csrf_token
        //let csrfToken = document.querySelector('input[name="csrf_token"]').value
        
        let storage = "quiz_" + QuizId + "_answers";

        if (!localStorage.getItem(storage)) {
            localStorage.setItem(storage, JSON.stringify([]));
        }

        document.getElementById("form_id").addEventListener("submit", function(e){
            e.preventDefault();

            const selected = document.querySelector('input[name="answer"]:checked');
            if(!selected){
                alert("choiser le bon reponce!");
                return;
            }

            fetch("/classes/api/check_answer.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    question_id: questions[currentIndex].id,
                    answer: selected.value
                })
            })
            .then(res => res.json())
            .then(data => {
                saveToLocalStorage(data.correct);
                currentIndex++;

                if(currentIndex >= questions.length){
                    window.location.href = "result.php";
                    return;
                }

                loadQuestion();
            });
        });

        function saveToLocalStorage(correct){
            let key = "quiz_" + QuizId + "_wrong";
            let history = JSON.parse(localStorage.getItem(key)) || [];

            if(!correct){
                history.push({
                    question: questions[currentIndex].question,
                    selected: document.querySelector('input[name="answer"]:checked').value
                });
            }

            localStorage.setItem(key, JSON.stringify(history));
        }
        loadQuestion();
</script>

</body>
</html>