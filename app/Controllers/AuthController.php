<?php

namespace App\Controllers;

use App\Helpers\FlashMessage;
use App\Requests\AuthRequest;
use App\Services\AuthService;
use App\Traits\FormTraits;
use Exception;

class AuthController {
    use FormTraits;

    private AuthService $authService;
    private AuthRequest $authRequest;

    public function __construct() {
        $this->authService = new AuthService();
        $this->authRequest = new AuthRequest();
    }

    public function register(): void {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $data = $this->getPostData( ['name', 'email', 'password'] );

        // Keep old input values in case of errors
        $this->setOldInput( $data );

        // Validate inputs
        [$errors, $name, $email, $password] = $this->authRequest->validate( $data, 'register' );
        if ( !empty( $errors ) ) {
            $this->setErrors( $errors );
            return;
        }

        // Check for duplicate email
        if ( empty( $errors ) && $this->authService->isDuplicateEmail( $email ) ) {
            $this->setErrors( ['email' => 'Email is already registered'] );
            return;
        }

        $this->authService->registerUser( $name, $email, $password );
        FlashMessage::setMessage( 'success', 'User registered successfully' );
        header( 'Location: /login.php' );
        exit();
    }

    public function login(): void {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $data = $this->getPostData( ['email', 'password'] );

        // Keep old input values in case of errors
        $this->setOldInput( $data );

        // Validate inputs
        [$errors, $email, $password] = $this->authRequest->validate( $data, 'login' );
        if ( !empty( $errors ) ) {
            $this->setErrors( $errors );
            return;
        }

        try {
            $user = $this->authService->authenticateUser( $email, $password );
            session_start();
            $_SESSION['user'] = $user;

            // Redirect based on user role
            $redirectUrl = ( $user['role'] === 'admin' ) ? 'admin/customers.php' : 'customer/dashboard.php';
            FlashMessage::setMessage( 'success', 'Login successful' );
            header( "Location: $redirectUrl" );
            exit();
        } catch ( Exception $e ) {
            $this->setErrors( ['auth' => $e->getMessage()] );
        }
    }

    public function seedAdmin(): void {
        $adminEmail = 'admin@gmail.com';

        if ( $this->authService->isDuplicateEmail( $adminEmail ) ) {
            FlashMessage::setMessage( 'error', 'Admin already exists' );
        } else {
            $this->authService->registerUser( 'John Doe', $adminEmail, 'password', 'admin' );
            FlashMessage::setMessage( 'success', 'Admin seeded successfully' );
        }

        header( 'Location: index.php' );
        exit();
    }

    public function logout(): void {
        session_start();
        session_unset();
        session_destroy();
        header( 'Location: index.php' );
        exit();
    }
}