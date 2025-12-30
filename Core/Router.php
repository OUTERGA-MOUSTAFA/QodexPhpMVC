<?php

namespace www\Core;

class Router {

    public static function route() {

        $page = $_GET['page'] ?? 'dashboard';

        switch ($page) {

            case 'dashboard':
                $controller = new \www\action\DashboardController();
                $controller->index();
                break;

            case 'quizzes':
                $controller = new \www\action\QuizController();
                $controller->index();
                break;

            case 'logout':
                $controller = new \www\action\AuthController();
                $controller->logout();
                break;

            default:
                echo "404 - Page not found";
        }
    }
}