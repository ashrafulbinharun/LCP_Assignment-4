<?php

namespace App\Requests;

class AuthRequest {
    public function validate( array $data, string $action ): array {
        $errors = [];

        if ( $action === "register" ) {
            $name = $this->validateName( $data['name'] ?? '', $errors );
        }

        $email = $this->validateEmail( $data['email'] ?? '', $errors );
        $password = $this->validatePassword( $data['password'] ?? '', $errors );

        if ( $action === "register" ) {
            return [$errors, $name, $email, $password];
        } else {
            return [$errors, $email, $password];
        }
    }

    private function validateName( string $name, array &$errors ): ?string {
        $name = trim( $name );
        $name = filter_var( $name, FILTER_SANITIZE_SPECIAL_CHARS );

        if ( empty( $name ) ) {
            $errors['name'] = 'Please provide a name';
            return null;
        }

        return $name;
    }

    private function validateEmail( string $email, array &$errors ): string {
        $email = trim( $email );

        if ( empty( $email ) ) {
            $errors['email'] = 'Please provide an email address';
        } elseif ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $errors['email'] = 'Please provide a valid email address';
        }

        return $email;
    }

    private function validatePassword( string $password, array &$errors ): string {
        $password = trim( $password );
        $password = filter_var( $password, FILTER_SANITIZE_SPECIAL_CHARS );

        if ( empty( $password ) ) {
            $errors['password'] = 'Please provide a password';
        } elseif ( strlen( $password ) < 6 ) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        return $password;
    }
}