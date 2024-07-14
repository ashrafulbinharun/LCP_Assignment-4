<?php

namespace App\Controllers;

use App\Helpers\FlashMessage;
use App\Requests\TransactionRequest;
use App\Services\TransactionService;
use App\Traits\FormTraits;
use Exception;

class TransactionController {
    use FormTraits;

    protected TransactionService $transactionService;
    protected TransactionRequest $transactionRequest;

    public function __construct() {
        $this->transactionService = new TransactionService();
        $this->transactionRequest = new TransactionRequest();
    }

    public function userDetails( $userId ): array {
        return $this->transactionService->getUserById( $userId );
    }

    public function transactionByUser( int $userId ): array {
        // for latest records to show first
        return array_reverse( $this->transactionService->getTransactions( $userId ) );
    }

    public function deposit( int $userId, string $amount ): void {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $data = $this->getPostData( ['amount'] );

        // Keep old input values in case of errors
        $this->setOldInput( $data );

        // Validate inputs
        [$errors, $amount] = $this->transactionRequest->validate( $data, 'deposit' );
        if ( !empty( $errors ) ) {
            $this->setErrors( $errors );
            return;
        }

        $this->transactionService->deposit( $userId, $amount );
        FlashMessage::setMessage( 'success', 'Deposit successful' );
        header( 'Location: dashboard.php' );
        exit();
    }

    public function withdraw( int $userId, string $amount ): void {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $data = $this->getPostData( ['amount'] );

        // Keep old input values in case of errors
        $this->setOldInput( $data );

        // Validate inputs
        [$errors, $amount] = $this->transactionRequest->validate( $data, 'withdraw' );
        if ( !empty( $errors ) ) {
            $this->setErrors( $errors );
            return;
        }

        try {
            $this->transactionService->withdraw( $userId, $amount );
            FlashMessage::setMessage( 'success', 'Withdraw successful' );
            header( 'Location: dashboard.php' );
            exit();
        } catch ( Exception $e ) {
            $this->setErrors( ['amount' => $e->getMessage()] );
        }
    }

    public function transfer( int $senderId, string $receiverEmail, string $amount ): void {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            return;
        }

        $data = $this->getPostData( ['amount', 'email'] );

        // Keep old input values in case of errors
        $this->setOldInput( $data );

        // Validate inputs
        [$errors, $amount, $receiverEmail] = $this->transactionRequest->validate( $data, 'transfer' );
        if ( !empty( $errors ) ) {
            $this->setErrors( $errors );
            return;
        }

        try {
            $receiver = $this->transactionService->getUserByEmail( $receiverEmail );
            if ( !$receiver ) {
                $this->setErrors( ['email' => 'Receiver does not exist'] );
                return;
            }

            $this->transactionService->transfer( $senderId, $receiverEmail, $amount );
            FlashMessage::setMessage( 'success', 'Transfer successful' );
            header( 'Location: dashboard.php' );
            exit();
        } catch ( Exception $e ) {
            $this->setErrors( ['amount' => $e->getMessage()] );
        }
    }
}