<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Traits\SanitizerTrait;
use App\Traits\SecurityTrait;
use App\Traits\ValidationTrait;
use App\Traits\LoggerTrait;
use App\Exceptions\ValidationException;
use App\Exceptions\SecurityException;
use App\Exceptions\AuthenticationException;

class AuthController extends BaseController
{
    use SanitizerTrait, SecurityTrait, ValidationTrait, LoggerTrait;

    public function showLoginForm(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['admin_id'])) {
            $this->redirect('/dashboard');
        }

        $csrfToken = $this->generateCsrfToken();
        $this->render('auth/login', ['csrfToken' => $csrfToken]);
    }

    public function login(): void
    {
        try {
            $this->checkCsrfOrThrow($_POST['csrf_token'] ?? null);

            $email    = $this->sanitizeString($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $errors = $this->validateRequired(['email' => $email, 'password' => $password], ['email', 'password']);
            if ($emailErr = $this->validateEmail($email)) {
                $errors['email'] = $emailErr;
            }

            $this->checkValidation($errors);

            $admin = Admin::findByEmail($email);
            if (!$admin || !$this->verifyPassword($password, $admin['password_hash'])) {
                self::logWarning("Tentative de connexion échouée pour l'email: {$email}");
                throw new AuthenticationException("Identifiants de connexion incorrects.");
            }

            session_regenerate_id(true);
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_name']  = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];

            self::logInfo("Connexion réussie de l'administrateur ID: {$admin['id']}");
            $this->redirect('/dashboard');

        } catch (ValidationException | AuthenticationException | SecurityException $e) {
            $this->render('auth/login', [
                'error'     => $e->getMessage(),
                'email'     => $_POST['email'] ?? '',
                'csrfToken' => $this->generateCsrfToken(),
            ]);
        }
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::logInfo("Déconnexion de l'administrateur ID: " . ($_SESSION['admin_id'] ?? 'inconnu'));

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        $this->redirect('/login');
    }
}