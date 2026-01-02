<?
include_once 'submit_quiz.php';

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاختبار - مساحة الطالب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    
<div id="takeQuiz">
    <!-- الرأس مع العنوان والمؤقت -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2" id="quizTitle">
                        <?= htmlspecialchars($questionsArray[0]["titre"]) ?>
                    </h1>
                    <p class="text-green-100">
                        سؤال <span id="currentQuestion">1</span> من <span id="totalQuestions"><?= $couterQuest; ?></span>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-green-100 mb-1">الوقت المتبقي</div>
                    <div class="text-3xl font-bold" id="timer">30:00</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- محتوى الاختبار -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- إشعار الاستعادة -->
            <div id="restoreNotification" class="mb-4 p-3 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hidden">
                <i class="fas fa-history ml-2"></i>
                <span>تم استعادة اختبارك السابق</span>
            </div>
            
            <!-- رسالة التغذية الراجعة -->
            <div id="feedbackMessage" class="hidden mb-4 p-4 rounded-lg"></div>
            
            <!-- السؤال والإجابات -->
            <div id="questionContainer">
                <!-- سيتم ملؤه بواسطة JavaScript -->
            </div>
            
            <!-- أزرار التحكم -->
            <div class="flex justify-between mt-8">
                <button type="button" onclick="previousQuestion()" 
                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition" 
                        id="prevBtn">
                    <i class="fas fa-arrow-right mr-2"></i>السابق
                </button>
                
                <button type="button" onclick="checkAnswer()" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" 
                        id="checkBtn">
                    تحقق من الإجابة<i class="fas fa-check ml-2"></i>
                </button>
                
                <button type="button" onclick="nextQuestion()" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" 
                        id="nextBtn">
                    التالي<i class="fas fa-arrow-left ml-2"></i>
                </button>
                
                <button type="button" onclick="finishQuiz()" 
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" 
                        id="finishBtn">
                    إنهاء الاختبار<i class="fas fa-flag-checkered ml-2"></i>
                </button>
            </div>
            
            <!-- تقدم الاختبار -->
            <div class="mt-8">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>تقدم الاختبار</span>
                    <span id="progressText">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <div class="text-center text-sm text-gray-500 mt-2">
                    <span id="answeredCount">0</span> من <span id="totalCount"><?= $couterQuest ?></span> أسئلة مجابة
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// =============================================
// الجزء 1: المتغيرات العامة والإعدادات
// =============================================
const questions = <?= json_encode($questionsArray, JSON_UNESCAPED_UNICODE) ?>;
const totalQuestions = <?= $couterQuest ?>;
const quizId = <?= (int)$_POST['quiz_id'] ?>;

// مفتاح التخزين في localStorage (فريد لكل اختبار)
const STORAGE_KEY = `quiz_${quizId}_state`;

// حالة الاختبار الحالية
let quizState = {
    currentQuestion: 0,              // رقم السؤال الحالي (0-based)
    answers: {},                     // إجابات المستخدم {سؤال: إجابة}
    checkedQuestions: {},            // الأسئلة التي تم التحقق منها
    startTime: null,                 // وقت بدء الاختبار
    elapsedTime: 0,                  // الوقت المنقضي بالثواني
    quizDuration: 1800,              // مدة الاختبار (30 دقيقة = 1800 ثانية)
    lastSave: null                   // آخر وقت حفظ
};

let timerInterval = null;            // مؤشر المؤقت
let isChecking = false;              // هل يتم التحقق حالياً؟

// =============================================
// الجزء 2: التهيئة عند تحميل الصفحة
// =============================================
$(document).ready(function() {
    // محاولة استعادة الاختبار السابق
    const savedState = loadQuizState();
    
    if (savedState && confirm('هل تريد استكمال الاختبار السابق؟')) {
        // استعادة الحالة السابقة
        quizState = savedState;
        showRestoreNotification();
        startTimerFromSaved();
    } else {
        // بدء اختبار جديد
        quizState.startTime = new Date();
        startTimer();
    }
    
    // عرض السؤال الأول
    renderQuestion();
    updateProgress();
    
    // حفظ تلقائي كل 10 ثواني
    setInterval(saveQuizState, 10000);
    
    // حفظ عند إغلاق الصفحة
    window.addEventListener('beforeunload', function(e) {
        saveQuizState();
        // يمكن إظهار رسالة تأكيد (اختياري)
        // e.returnValue = 'هل أنت متأكد أنك تريد المغادرة؟ سيتم حفظ تقدمك.';
    });
});

// =============================================
// الجزء 3: وظائف localStorage
// =============================================
function saveQuizState() {
    try {
        // تحديث وقت الحفظ الأخير
        quizState.lastSave = new Date();
        
        // حساب الوقت المنقضي
        if (quizState.startTime) {
            const now = new Date();
            quizState.elapsedTime = Math.floor((now - new Date(quizState.startTime)) / 1000);
        }
        
        // حفظ في localStorage
        localStorage.setItem(STORAGE_KEY, JSON.stringify(quizState));
        
        // إظهار إشعار حفظ صغير (اختياري)
        showAutoSaveNotification();
        
    } catch (error) {
        console.error('خطأ في حفظ حالة الاختبار:', error);
    }
}

function loadQuizState() {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) return null;
        
        const parsed = JSON.parse(saved);
        
        // تحويل تواريخ السلسلة إلى كائنات Date
        if (parsed.startTime) parsed.startTime = new Date(parsed.startTime);
        if (parsed.lastSave) parsed.lastSave = new Date(parsed.lastSave);
        
        return parsed;
        
    } catch (error) {
        console.error('خطأ في تحميل حالة الاختبار:', error);
        return null;
    }
}

function clearQuizState() {
    localStorage.removeItem(STORAGE_KEY);
}

// =============================================
// الجزء 4: عرض السؤال
// =============================================
function renderQuestion() {
    const question = questions[quizState.currentQuestion];
    const userAnswer = quizState.answers[quizState.currentQuestion];
    const isChecked = quizState.checkedQuestions[quizState.currentQuestion];
    
    // بناء HTML للسؤال
    let html = `
        <h3 class="text-2xl font-bold text-gray-900 mb-6">
            ${quizState.currentQuestion + 1}. ${question.question}
        </h3>
        
        <div class="space-y-4">
    `;
    
    // خيارات الإجابة
    const options = [
        { num: 1, text: question.option1 },
        { num: 2, text: question.option2 },
        { num: 3, text: question.option3 },
        { num: 4, text: question.option4 }
    ];
    
    options.forEach(option => {
        const isSelected = userAnswer === option.num;
        let optionClass = "p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition";
        let radioClass = "w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center ml-4";
        let checkClass = "w-4 h-4 rounded-full bg-green-600 hidden";
        
        if (isSelected) {
            optionClass += " border-green-500 bg-green-50";
            radioClass += " border-green-600";
            checkClass = checkClass.replace("hidden", "");
        }
        
        if (isChecked) {
            // إذا تم التحقق من السؤال
            const isCorrect = option.num === question.correct_option;
            if (isSelected) {
                if (isCorrect) {
                    optionClass += " bg-green-100 border-green-600";
                } else {
                    optionClass += " bg-red-100 border-red-600";
                }
            } else if (isCorrect) {
                optionClass += " bg-green-50 border-green-500";
            }
        }
        
        html += `
            <div onclick="selectAnswer(${option.num})" class="${optionClass}">
                <div class="flex items-center">
                    <div class="${radioClass}">
                        <div class="${checkClass}"></div>
                    </div>
                    <span class="text-lg">${option.text}</span>
                    ${isChecked && option.num === question.correct_option ? 
                      '<span class="mr-auto text-green-600"><i class="fas fa-check ml-2"></i>إجابة صحيحة</span>' : ''}
                    ${isChecked && isSelected && option.num !== question.correct_option ? 
                      '<span class="mr-auto text-red-600"><i class="fas fa-times ml-2"></i>إجابة خاطئة</span>' : ''}
                </div>
            </div>
        `;
    });
    
    html += `</div>`;
    
    // إدراج HTML في الصفحة
    $('#questionContainer').html(html);
    
    // تحديث رقم السؤال الحالي
    $('#currentQuestion').text(quizState.currentQuestion + 1);
    
    // تحديث الأزرار
    updateButtons();
    
    // حفظ الحالة بعد كل تغيير
    saveQuizState();
}

// =============================================
// الجزء 5: اختيار إجابة
// =============================================
function selectAnswer(answerNum) {
    // لا يمكن تغيير الإجابة إذا تم التحقق منها
    if (quizState.checkedQuestions[quizState.currentQuestion]) {
        return;
    }
    
    // حفظ الإجابة
    quizState.answers[quizState.currentQuestion] = answerNum;
    
    // إعادة عرض السؤال مع الإجابة المحددة
    renderQuestion();
    
    // تحديث التقدم
    updateProgress();
}

// =============================================
// الجزء 6: التحقق من الإجابة
// =============================================
function checkAnswer() {
    // التحقق من وجود إجابة
    if (typeof quizState.answers[quizState.currentQuestion] === 'undefined') {
        showFeedback('يرجى اختيار إجابة أولاً', 'warning');
        return;
    }
    
    // منع التحقق المتكرر
    if (isChecking) return;
    isChecking = true;
    
    // إظهار تحميل
    $('#checkBtn').html('<i class="fas fa-spinner fa-spin ml-2"></i>جاري التحقق...');
    $('#checkBtn').prop('disabled', true);
    
    // إرسال طلب Ajax
    $.ajax({
        url: 'check_answer.php',
        type: 'POST',
        data: {
            question_id: questions[quizState.currentQuestion].id,
            user_answer: quizState.answers[quizState.currentQuestion],
            quiz_id: quizId
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                
                if (result.status === 'success') {
                    // علامة أن السؤال تم التحقق منه
                    quizState.checkedQuestions[quizState.currentQuestion] = true;
                    
                    // عرض النتيجة
                    if (result.correct) {
                        showFeedback('إجابة صحيحة! ✓', 'success');
                    } else {
                        showFeedback(`إجابة خاطئة. الإجابة الصحيحة هي: ${result.correct_answer}`, 'error');
                    }
                    
                    // إعادة عرض السؤال مع النتيجة
                    renderQuestion();
                    
                } else {
                    showFeedback('حدث خطأ في التحقق', 'error');
                }
                
            } catch (error) {
                showFeedback('خطأ في معالجة الاستجابة', 'error');
            }
        },
        error: function() {
            showFeedback('خطأ في الاتصال بالخادم', 'error');
        },
        complete: function() {
            // إعادة الزر لحالته الأصلية
            $('#checkBtn').html('تحقق من الإجابة<i class="fas fa-check ml-2"></i>');
            $('#checkBtn').prop('disabled', false);
            isChecking = false;
            
            // حفظ الحالة بعد التحقق
            saveQuizState();
        }
    });
}

// =============================================
// الجزء 7: التنقل بين الأسئلة
// =============================================
function nextQuestion() {
    if (quizState.currentQuestion < totalQuestions - 1) {
        quizState.currentQuestion++;
        renderQuestion();
    }
}

function previousQuestion() {
    if (quizState.currentQuestion > 0) {
        quizState.currentQuestion--;
        renderQuestion();
    }
}

// =============================================
// الجزء 8: المؤقت
// =============================================
function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    
    let timeLeft = quizState.quizDuration - quizState.elapsedTime;
    
    timerInterval = setInterval(function() {
        timeLeft--;
        quizState.elapsedTime++;
        
        // تحديث العرض
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        $('#timer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        
        // إذا انتهى الوقت
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            finishQuiz();
        }
        
        // حفظ كل دقيقة
        if (quizState.elapsedTime % 60 === 0) {
            saveQuizState();
        }
        
    }, 1000);
}

function startTimerFromSaved() {
    if (timerInterval) clearInterval(timerInterval);
    
    let timeLeft = quizState.quizDuration - quizState.elapsedTime;
    
    // إذا انتهى الوقت بالفعل
    if (timeLeft <= 0) {
        finishQuiz();
        return;
    }
    
    timerInterval = setInterval(function() {
        timeLeft--;
        quizState.elapsedTime++;
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        $('#timer').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            finishQuiz();
        }
        
    }, 1000);
}

// =============================================
// الجزء 9: تحديث الواجهة
// =============================================
function updateButtons() {
    // زر السابق
    if (quizState.currentQuestion === 0) {
        $('#prevBtn').hide();
    } else {
        $('#prevBtn').show();
    }
    
    // زر التالي
    if (quizState.currentQuestion === totalQuestions - 1) {
        $('#nextBtn').hide();
        $('#finishBtn').show();
    } else {
        $('#nextBtn').show();
        $('#finishBtn').hide();
    }
    
    // زر التحقق (يظهر فقط إذا لم يتم التحقق من السؤال)
    if (quizState.checkedQuestions[quizState.currentQuestion]) {
        $('#checkBtn').hide();
    } else {
        $('#checkBtn').show();
    }
}

function updateProgress() {
    // حساب عدد الأسئلة المجابة
    const answeredCount = Object.keys(quizState.answers).length;
    const progress = Math.round((answeredCount / totalQuestions) * 100);
    
    // تحديث العرض
    $('#progressText').text(`${progress}%`);
    $('#progressBar').css('width', `${progress}%`);
    $('#answeredCount').text(answeredCount);
    $('#totalCount').text(totalQuestions);
}

// =============================================
// الجزء 10: إنهاء الاختبار
// =============================================
function finishQuiz() {
    if (timerInterval) clearInterval(timerInterval);
    
    // حساب النتيجة
    calculateScore().then(score => {
        // حفظ النتيجة النهائية
        saveFinalResults(score);
        
        // عرض النتيجة
        showFinalResults(score);
        
        // مسح بيانات التقدم
        clearQuizState();
    });
}

async function calculateScore() {
    let correctCount = 0;
    
    // التحقق من كل سؤال تم الإجابة عليه
    for (let i = 0; i < totalQuestions; i++) {
        if (quizState.answers[i] && !quizState.checkedQuestions[i]) {
            // إذا كانت الإجابة موجودة ولم يتم التحقق منها
            try {
                const response = await $.ajax({
                    url: 'check_answer.php',
                    type: 'POST',
                    data: {
                        question_id: questions[i].id,
                        user_answer: quizState.answers[i],
                        quiz_id: quizId
                    }
                });
                
                const result = JSON.parse(response);
                if (result.correct) {
                    correctCount++;
                }
            } catch (error) {
                console.error('خطأ في حساب النتيجة:', error);
            }
        } else if (quizState.checkedQuestions[i]) {
            // إذا تم التحقق من السؤال مسبقاً
            // يمكن حسابها من البيانات المحفوظة
        }
    }
    
    return {
        correct: correctCount,
        total: totalQuestions,
        percentage: Math.round((correctCount / totalQuestions) * 100)
    };
}

function showFinalResults(score) {
    const resultsHtml = `
        <div class="text-center p-8">
            <div class="text-6xl font-bold text-green-600 mb-4">${score.correct}/${score.total}</div>
            <div class="text-3xl text-gray-700 mb-6">${score.percentage}%</div>
            
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="text-sm text-blue-600">الأسئلة المجابة</div>
                    <div class="text-2xl font-bold">${Object.keys(quizState.answers).length}</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="text-sm text-green-600">الإجابات الصحيحة</div>
                    <div class="text-2xl font-bold">${score.correct}</div>
                </div>
            </div>
            
            <button onclick="window.location.href='quiz.php'" 
                    class="px-8 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition text-lg">
                العودة إلى قائمة الاختبارات
            </button>
        </div>
    `;
    
    $('#questionContainer').html(resultsHtml);
    $('.flex.justify-between.mt-8').hide();
    $('.mt-8:last').hide();
}

function saveFinalResults(score) {
    // يمكن إضافة كود هنا لحفظ النتيجة في قاعدة البيانات
    console.log('النتيجة النهائية:', score);
}

// =============================================
// الجزء 11: وظائف المساعدة
// =============================================
function showFeedback(message, type) {
    const colors = {
        success: 'bg-green-100 text-green-800 border-green-200',
        error: 'bg-red-100 text-red-800 border-red-200',
        warning: 'bg-yellow-100 text-yellow-800 border-yellow-200'
    };
    
    $('#feedbackMessage').removeClass('hidden')
        .removeClass('bg-green-100 bg-red-100 bg-yellow-100')
        .addClass(colors[type])
        .html(`
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'exclamation-circle'} ml-2"></i>
                <span>${message}</span>
            </div>
        `);
    
    // إخفاء الرسالة بعد 5 ثواني
    setTimeout(() => {
        $('#feedbackMessage').addClass('hidden');
    }, 5000);
}

function showRestoreNotification() {
    $('#restoreNotification').removeClass('hidden');
    
    // إخفاء الإشعار بعد 5 ثواني
    setTimeout(() => {
        $('#restoreNotification').addClass('hidden');
    }, 5000);
}

function showAutoSaveNotification() {
    // يمكن إضافة إشعار صغير للحفظ التلقائي
    // أو تحديث وقت آخر حفظ في الزاوية
}
</script>
</body>
</html>