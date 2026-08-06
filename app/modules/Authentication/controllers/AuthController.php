<?php

declare(strict_types=1);

namespace App\Modules\Authentication\controllers;

use App\Core\Controller;
use App\Modules\Authentication\services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Display or process Login form.
     */
    public function login(): void
    {
        if (is_authenticated()) {
            $this->redirect('/dashboard');
        }

        $error   = null;
        $success = flash_get('success');

        if ($this->isPost()) {
            $csrfToken = $this->input('_csrf_token');
            if (!csrf_verify($csrfToken)) {
                $error = 'Security session expired. Please refresh and try again.';
            } else {
                $loginId   = $this->input('login_id');
                $password  = $this->input('password');
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

                if (empty($loginId) || empty($password)) {
                    $error = 'Please provide both username/email and password.';
                } else {
                    $res = $this->authService->login($loginId, $password, $ipAddress, $userAgent);
                    if ($res['success']) {
                        if ($_SESSION['must_change_password']) {
                            $this->redirect('/change-password');
                        }
                        $this->redirect('/dashboard');
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $this->render('Authentication/views/login', [
            'error'   => $error,
            'success' => $success,
            'title'   => 'CMS Login'
        ], null);
    }

    /**
     * Process Logout.
     */
    public function logout(): void
    {
        $this->authService->logout();
        flash('success', 'You have been logged out successfully.');
        $this->redirect('/login');
    }

    /**
     * Display or process Change Password.
     */
    public function changePassword(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $currentPassword = $this->input('current_password');
                $newPassword     = $this->input('new_password');
                $confirmPassword = $this->input('confirm_password');

                if ($newPassword !== $confirmPassword) {
                    $error = 'New password and confirmation do not match.';
                } else {
                    $res = $this->authService->changePassword(auth_id(), $currentPassword, $newPassword);
                    if ($res['success']) {
                        flash('success', 'Password updated successfully!');
                        $this->redirect('/dashboard');
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $this->render('Authentication/views/change_password', [
            'error'              => $error,
            'success'            => $success,
            'mustChangePassword' => $_SESSION['must_change_password'] ?? false,
            'title'              => 'Change Password'
        ], null);
    }

    /**
     * Forgot Password request.
     */
    public function forgotPassword(): void
    {
        $error   = null;
        $success = null;
        $token   = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $email = $this->input('email');
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } else {
                    $res = $this->authService->requestReset($email);
                    $success = $res['message'];
                    if (isset($res['token'])) {
                        $token = $res['token'];
                    }
                }
            }
        }

        $this->render('Authentication/views/forgot_password', [
            'error'   => $error,
            'success' => $success,
            'token'   => $token,
            'title'   => 'Reset Password'
        ], null);
    }

    /**
     * Reset Password with token.
     */
    public function resetPassword(): void
    {
        $token   = query('token', $this->input('token'));
        $error   = null;

        if (empty($token)) {
            $error = 'Missing reset token.';
        }

        if ($this->isPost() && empty($error)) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $newPassword     = $this->input('new_password');
                $confirmPassword = $this->input('confirm_password');

                if ($newPassword !== $confirmPassword) {
                    $error = 'Passwords do not match.';
                } else {
                    $res = $this->authService->resetPassword($token, $newPassword);
                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/login');
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $this->render('Authentication/views/reset_password', [
            'token' => $token,
            'error' => $error,
            'title' => 'Set New Password'
        ], null);
    }
}
