<?php
/**
 * AgriLink – Authentication Controller
 * Methods are invoked by the front-controller in public/index.php.
 */
class AuthController {
    private UserModel $users;
    private NotificationModel $notifs;

    public function __construct() {
        $this->users  = new UserModel();
        $this->notifs = new NotificationModel();
    }

    // GET /login
    public function showLogin(): void {
        Auth::redirectIfLoggedIn();
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        include BASE_PATH . '/app/views/auth/login.php';
    }

    // POST /login  (action=login)
    public function doLogin(): void {
        Auth::redirectIfLoggedIn();

        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            Auth::redirect('/login');
        }

        // ── Rate limiting: max 5 attempts per 15 minutes ──────────────────
        $now      = time();
        $window   = 15 * 60;   // 15-minute window
        $maxTries = 5;
        $attempts = $_SESSION['_login_attempts'] ?? [];
        // Prune attempts outside the window
        $attempts = array_filter($attempts, fn($t) => $now - $t < $window);
        if (count($attempts) >= $maxTries) {
            $wait = $window - ($now - min($attempts));
            Session::setFlash('error', 'Too many login attempts. Please wait ' . ceil($wait / 60) . ' minute(s) before trying again.');
            Auth::redirect('/login');
        }

        $email    = sanitize($_POST['email']    ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Email and password are required.');
            Auth::redirect('/login');
        }

        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Record failed attempt
            $attempts[] = $now;
            $_SESSION['_login_attempts'] = array_values($attempts);
            Session::setFlash('error', 'Invalid email or password.');
            Auth::redirect('/login');
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            Session::setFlash('error', 'Your account has been suspended. Contact support.');
            Auth::redirect('/login');
        }

        // Clear attempts on successful login
        unset($_SESSION['_login_attempts']);
        Session::login($user);
        Auth::redirectToDashboard();
    }

    // GET /register
    public function showRegister(): void {
        Auth::redirectIfLoggedIn();
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $old     = Session::get('old_input', []);
        Session::delete('old_input');
        $regions = GH_REGIONS;
        include BASE_PATH . '/app/views/auth/register.php';
    }

    // GET /forgot-password
    public function showForgotPassword(): void {
        Auth::redirectIfLoggedIn();
        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $info    = Session::getFlash('info');
        $old     = Session::get('old_input', []);
        Session::delete('old_input');
        $resetLink = SHOW_PASSWORD_RESET_LINK ? Session::get('password_reset_demo_link') : null;
        Session::delete('password_reset_demo_link');
        include BASE_PATH . '/app/views/auth/forgot_password.php';
    }

    // POST /forgot-password
    public function doForgotPassword(): void {
        Auth::redirectIfLoggedIn();

        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            Auth::redirect('/forgot-password');
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Enter a valid email address.');
            Session::set('old_input', ['email' => $email]);
            Auth::redirect('/forgot-password');
        }

        $user = $this->users->findByEmail($email);
        $mailSent = false;
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);
            $this->users->storePasswordResetToken((int)$user['id'], $tokenHash, $expiresAt);
            $resetLink = APP_URL . '/reset-password?token=' . urlencode($token);
            if (ENABLE_EMAIL_DELIVERY) {
                $mailSent = Mailer::sendPasswordReset($user['email'], $user['name'], $resetLink);
            }
            if (SHOW_PASSWORD_RESET_LINK) {
                Session::set('password_reset_demo_link', $resetLink);
            }
        }

        Session::setFlash('success', 'If the email exists, password reset instructions have been sent.');
        if (SHOW_PASSWORD_RESET_LINK && !ENABLE_EMAIL_DELIVERY) {
            Session::setFlash('info', 'Email delivery is not configured in this build, so the one-time reset link is shown below when available.');
        } elseif (!empty($mailSent)) {
            Session::setFlash('info', 'Check your inbox for the password reset link.');
        } else {
            Session::setFlash('info', 'If your account exists but no email arrives, contact ' . SUPPORT_EMAIL . ' for help completing the reset.');
        }
        Auth::redirect('/forgot-password');
    }

    // GET /reset-password
    public function showResetPassword(): void {
        Auth::redirectIfLoggedIn();
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            Session::setFlash('error', 'Your reset link is missing or invalid.');
            Auth::redirect('/forgot-password');
        }

        $user = $this->users->findByValidPasswordResetToken($token);
        if (!$user) {
            Session::setFlash('error', 'This reset link is invalid or has expired. Request a new one.');
            Auth::redirect('/forgot-password');
        }

        $error   = Session::getFlash('error');
        $success = Session::getFlash('success');
        $email   = $user['email'];
        include BASE_PATH . '/app/views/auth/reset_password.php';
    }

    // POST /reset-password
    public function doResetPassword(): void {
        Auth::redirectIfLoggedIn();

        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            Auth::redirect('/forgot-password');
        }

        $token    = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if ($token === '') {
            Session::setFlash('error', 'Your reset token is missing. Request a new link.');
            Auth::redirect('/forgot-password');
        }

        $user = $this->users->findByValidPasswordResetToken($token);
        if (!$user) {
            Session::setFlash('error', 'This reset link is invalid or has expired. Request a new one.');
            Auth::redirect('/forgot-password');
        }

        if (strlen($password) < 8) {
            Session::setFlash('error', 'Password must be at least 8 characters.');
            Auth::redirect('/reset-password?token=' . urlencode($token));
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Passwords do not match.');
            Auth::redirect('/reset-password?token=' . urlencode($token));
        }

        $this->users->changePasswordAndClearReset((int)$user['id'], $password);
        unset($_SESSION['_login_attempts']);
        Session::setFlash('success', 'Your password has been reset. Sign in with your new password.');
        Auth::redirect('/login');
    }

    // POST /register  (action=register)
    public function doRegister(): void {
        Auth::redirectIfLoggedIn();

        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token.');
            Auth::redirect('/register');
        }

        $name     = sanitize($_POST['name']     ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $phone    = sanitize($_POST['phone']    ?? '');
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $role     = sanitize($_POST['role']     ?? 'buyer');
        $region   = sanitize($_POST['region']   ?? '');
        $town     = sanitize($_POST['town']     ?? '');

        $allowedRoles = ['farmer', 'buyer', 'transport'];
        $errors = [];

        if (strlen($name) < 2)      $errors[] = 'Name must be at least 2 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (strlen($password) < 8)  $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)  $errors[] = 'Passwords do not match.';
        if (!in_array($role, $allowedRoles, true)) $errors[] = 'Invalid role selected.';
        if ($this->users->emailExists($email))  $errors[] = 'This email is already registered.';

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            Session::set('old_input', compact('name','email','phone','role','region','town'));
            Auth::redirect('/register');
        }

        $id = $this->users->create(compact('name','email','phone','password','role','region','town'));

        // Welcome notification
        $this->notifs->create($id, 'welcome', 'Welcome to AgriLink!',
            'Your account has been created. Start exploring the Ghana Agricultural Marketplace.', null);

        // Auto-login & send to onboarding
        $newUser = $this->users->findByEmail($email);
        if ($newUser) Session::login($newUser);

        Auth::redirect('/auth/onboarding');
    }

    // GET /auth/onboarding
    public function showOnboarding(): void {
        Auth::require();
        $user    = $this->users->findById(Session::userId());
        $regions = GH_REGIONS;
        $roles   = ['farmer' => 'Farmer', 'buyer' => 'Market Buyer', 'transport' => 'Transport Provider'];
        $pageTitle = 'Complete Your Profile';
        include BASE_PATH . '/app/views/auth/onboarding.php';
    }

    // POST /auth/onboarding  (action=onboarding)
    public function doOnboarding(): void {
        Auth::require();

        if (!Session::verifyCsrf($_POST['_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token.');
            Auth::redirect('/auth/onboarding');
        }

        $userId  = Session::userId();
        $phone   = sanitize($_POST['phone']  ?? '');
        $region  = sanitize($_POST['region'] ?? '');
        $town    = sanitize($_POST['town']   ?? '');

        $data = [];
        if ($phone)  $data['phone']  = $phone;
        if ($region) $data['region'] = $region;
        if ($town)   $data['town']   = $town;

        if (!empty($data)) {
            $this->users->update($userId, $data);
        }

        Session::setFlash('success', 'Profile completed! Welcome to AgriLink.');
        Auth::redirectToDashboard();
    }

    // GET /logout
    public function doLogout(): void {
        Session::destroy();
        Auth::redirect('/login');
    }
}
