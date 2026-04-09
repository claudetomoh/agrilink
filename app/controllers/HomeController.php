<?php
class HomeController {

    public function index(): void {
        // If already logged in, redirect to role dashboard
        if (Session::isLoggedIn()) {
            $role = Session::get('user_role');
            $dest = match($role) {
                'farmer'    => '/farmer/dashboard',
                'buyer'     => '/buyer/marketplace',
                'transport' => '/transport/dashboard',
                'admin'     => '/admin/dashboard',
                default     => '/login',
            };
            Auth::redirect($dest);
        }
        $pageTitle = 'AgriLink — Ghana\'s Agricultural Marketplace';
        include BASE_PATH . '/app/views/home/index.php';
    }
}
