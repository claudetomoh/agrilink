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
