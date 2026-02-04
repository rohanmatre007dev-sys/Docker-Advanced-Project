<?php
// php-app/src/Controllers/AuthController.php
declare(strict_types=1);

class AuthController
{
    private PDO $pdo;
    private $redis;
    public function __construct(PDO $pdo, $redis = null)
    {
        $this->pdo = $pdo;
        $this->redis = $redis;
    }

    public function redirectToTickets()
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/tickets');
        }
        redirect('/login');
    }

    public function showLogin()
    {
        view('login.php', [
            'title' => 'Login',
            'email' => $_SESSION['email'] ?? '',
            'error' => flash('error')
        ]);
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Please provide email and password');
            redirect('/login');
        }

        require_once __DIR__ . '/../Models/User.php';
        $user = User::findByEmail($this->pdo, $email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials');
            redirect('/login');
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['email'] = $user['email'];
        redirect('/tickets');
    }

    public function showRegister()
    {
        view('register.php', [
            'title' => 'Register',
            'error' => flash('error')
        ]);
    }

    public function register()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (!$email || !$password || !$password_confirm) {
            flash('error', 'Fill all fields');
            redirect('/register');
        }
        if ($password !== $password_confirm) {
            flash('error', 'Passwords do not match');
            redirect('/register');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid email');
            redirect('/register');
        }

        require_once __DIR__ . '/../Models/User.php';
        $existing = User::findByEmail($this->pdo, $email);
        if ($existing) {
            flash('error', 'Email already registered');
            redirect('/register');
        }

        $created = User::create($this->pdo, $email, password_hash($password, PASSWORD_DEFAULT));
        if ($created) {
            // Auto login
            $_SESSION['user_id'] = (int)$created;
            $_SESSION['email'] = $email;
            redirect('/tickets');
        } else {
            flash('error', 'Registration failed');
            redirect('/register');
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        redirect('/login');
    }
}
