<?php

namespace App\Requests;

class TransactionRequest {
    public function validate( array $data, string $action ): array {
        $errors = [];

        $amount = $this->validateAmount( $data['amount'] ?? '', $errors );

        // Validate the email field if the action is "transfer"
        if ( $action === "transfer" ) {
            $email = $this->validateEmail( $data['email'] ?? '', $errors );
            return [$errors, $amount, $email];
        } else {
            return [$errors, $amount];
        }
    }

    private function validateAmount( string $amount, array &$errors ): float {
        $amount = (float) $amount;

        if ( empty( $amount ) && !is_numeric( $amount ) ) {
            $errors['amount'] = 'Please provide a valid amount';
        } elseif ( $amount <= 0 ) {
            $errors['amount'] = 'Amount must be a positive number';
        } elseif ( !preg_match( '/^[0-9]+(\.[0-9]{1,2})?$/', $amount ) ) {
            $errors['amount'] = 'Invalid amount format';
        } else {
            $amount = filter_var( $amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        }

        return $amount;
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
}