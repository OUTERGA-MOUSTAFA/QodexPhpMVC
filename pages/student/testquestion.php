<?
include_once 'submit_quiz.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions - Espace Etudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <!-- استبدل الـ form بالـ div مع id لتحديث المحتوى -->
    <div id="quizContainer" class="bg-white rounded-xl shadow-lg p-8">
        <!-- محتوى السؤال سيتم تحديثه هنا عبر AJAX -->
        <h3 class="text-2xl font-bold text-gray-900 mb-6" id="questionText">
            <?= $questionsArray["question"]; ?>
        </h3>

        <div class="space-y-4" id="answersContainer">
            <!-- الإجابات ستتغير هنا -->
            <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                        <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                    </div>
                    <span class="text-lg"><?= $questionsArray["option1"] ?></span>
                </div>
            </div>
            <!-- ... بقية الخيارات ... -->
        </div>

        <div class="flex justify-between mt-8">
            <button onclick="previousQuestion()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Précédent
            </button>
            
            <!-- زر التالي سيعمل مع AJAX -->
            <button onclick="nextQuestion()" id="nextBtn" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Suivant<i class="fas fa-arrow-right ml-2"></i>
            </button>
            
            <!-- زر الإرسال النهائي (سيظهر فقط عند السؤال الأخير) -->
            <button onclick="submitQuiz()" id="submitBtn" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition hidden">
                Terminer le Quiz<i class="fas fa-paper-plane ml-2"></i>
            </button>
        </div>
    </div>
</div>

<script>
// متغيرات عامة
let currentQuestion = 0;
const totalQuestions = <?= $couterQuest; ?>;
const quizId = <?= (int)$_POST['hidden']; ?>;
const questionsData = <?= json_encode($questionsArray); ?>;
let userAnswers = {}; // لتخزين إجابات المستخدم

// دالة تغيير السؤال
function loadQuestion(questionIndex) {
    // تحديث رقم السؤال الحالي
    currentQuestion = questionIndex;
    $('#currentQuestion').text(questionIndex + 1);
    
    // إذا كان هذا السؤال الأخير، غير نص الزر
    if (questionIndex === totalQuestions - 1) {
        $('#nextBtn').addClass('hidden');
        $('#submitBtn').removeClass('hidden');
    } else {
        $('#nextBtn').removeClass('hidden');
        $('#submitBtn').addClass('hidden');
    }
    
    // تحميل بيانات السؤال الجديد
    const question = questionsData[questionIndex];
    
    // تحديث نص السؤال
    $('#questionText').text(question.question);
    
    // تحديث الخيارات
    $('#answersContainer').html(`
        <div onclick="selectAnswer(this)" data-value="option1" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                </div>
                <span class="text-lg">${question.option1}</span>
            </div>
        </div>
        <div onclick="selectAnswer(this)" data-value="option2" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                </div>
                <span class="text-lg">${question.option2}</span>
            </div>
        </div>
        <div onclick="selectAnswer(this)" data-value="option3" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                </div>
                <span class="text-lg">${question.option3}</span>
            </div>
        </div>
        <div onclick="selectAnswer(this)" data-value="option4" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                </div>
                <span class="text-lg">${question.option4}</span>
            </div>
        </div>
    `);
    
    // إذا كان المستخدم قد أجاب على هذا السؤال مسبقاً، حدد الإجابة
    if (userAnswers[question.id]) {
        const selectedOption = $(`[data-value="${userAnswers[question.id]}"]`);
        selectedOption.find('.option-selected').removeClass('hidden');
        selectedOption.addClass('border-green-500 bg-green-50');
    }
}

// دالة اختيار إجابة
function selectAnswer(element) {
    // إزالة التحديد من كل الخيارات
    $('.answer-option').removeClass('border-green-500 bg-green-50');
    $('.option-selected').addClass('hidden');
    
    // تحديد الخيار المختار
    $(element).addClass('border-green-500 bg-green-50');
    $(element).find('.option-selected').removeClass('hidden');
    
    // حفظ الإجابة في المصفوفة
    const questionId = questionsData[currentQuestion].id;
    const selectedValue = $(element).data('value');
    userAnswers[questionId] = selectedValue;
}

// دالة السؤال التالي
function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        loadQuestion(currentQuestion + 1);
    }
}

// دالة السؤال السابق
function previousQuestion() {
    if (currentQuestion > 0) {
        loadQuestion(currentQuestion - 1);
    }
}

// دالة إرسال النتائج
function submitQuiz() {
    // التحقق من إجابة جميع الأسئلة
    const unansweredQuestions = [];
    questionsData.forEach((question, index) => {
        if (!userAnswers[question.id]) {
            unansweredQuestions.push(index + 1);
        }
    });
    
    if (unansweredQuestions.length > 0) {
        if (confirm(`Vous n'avez pas répondu aux questions: ${unansweredQuestions.join(', ')}. Voulez-vous continuer?`)) {
            sendResults();
        }
    } else {
        sendResults();
    }
}

// إرسال النتائج إلى السيرفر
function sendResults() {
    $.ajax({
        url: 'submit_quiz.php',
        type: 'POST',
        data: {
            quiz_id: quizId,
            answers: JSON.stringify(userAnswers),
            total_questions: totalQuestions
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // إعادة التوجيه إلى صفحة النتائج
                window.location.href = 'results.php?quiz_id=' + quizId + '&score=' + response.score;
            } else {
                alert('Erreur: ' + response.message);
            }
        },
        error: function() {
            alert('Erreur de connexion au serveur');
        }
    });
}

// بدء التايمر (اختياري)
function startTimer(duration) {
    let timer = duration, minutes, seconds;
    const interval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        $('#timer').text(minutes + ":" + seconds);

        if (--timer < 0) {
            clearInterval(interval);
            // إذا انتهى الوقت، أرسل الإجابات تلقائياً
            sendResults();
        }
    }, 1000);
}

// بدء التايمر بـ 30 دقيقة (1800 ثانية)
startTimer(1800);
</script>
</body>
</html>