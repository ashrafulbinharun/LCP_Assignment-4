<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\TransactionService;

class AdminController {
    protected AuthService $authService;
    protected TransactionService $transactionService;

    public function __construct() {
        $this->authService = new AuthService();
        $this->transactionService = new TransactionService();
    }

    public function usersList(): array {
        $data = $this->authService->getUsers();

        // get all users with role "customer"
        $users = array_filter( $data, fn( $user ) => $user['role'] === "customer" );

        // for latest records to show first
        $sortedList = array_reverse( $users );

        return $sortedList;
    }

    public function searchUserByEmail( string $email ): array {
        $data = $this->authService->getUsers();
        $users = array_filter( $data, fn( $user ) => $user['email'] === $email );

        return $users;
    }

    public function userInfo( int $userId ): array {
        return $this->transactionService->getUserById( $userId );
    }

    public function transactionsList(): array {
        // Retrieve and reverse transactions for latest records first
        return array_reverse( $this->transactionService->allTransactions() );
    }

    public function getUserName( $userId ): string {
        $user = $this->transactionService->getUserById( $userId );

        return $user['name'];
    }

    public function transactionsById( int $userId ): array {
        // for latest records to show first
        return array_reverse( $this->transactionService->getTransactions( $userId ) );
    }
}