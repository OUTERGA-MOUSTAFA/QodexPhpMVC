<?php
require_once '../config/database.php';
require_once '../classes/Database.php';
require_once '../classes/Security.php';
require_once '../classes/Question.php';
require_once '../classes/Quiz.php';

Security::requireTeacher();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/Student/quiz.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['quiz_error'] = 'Token de sécurité invalide';
    header('Location: ../pages/Student/quiz.php');
    exit();
}


if ($_POST['answer'] !== 'POST') {
    return "Veuillez reponde un question";
}
