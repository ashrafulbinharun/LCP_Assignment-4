<?php

namespace App\Services;

use App\Constants\FilePaths;
use App\Constants\TransactionTypes;
use App\Helpers\FileHelper;
use Exception;

class TransactionService {
    public function allTransactions(): array {
        return FileHelper::readFile( FilePaths::TRANSACTIONS );
    }

    public function saveTransaction( array $transactions, bool $append = false ): void {
        FileHelper::writeFile( FilePaths::TRANSACTIONS, $transactions );
    }

    public function getTransactions( int $userId ): array {
        $transactions = $this->allTransactions();

        // Filter transactions to get only those for the specified customer
        $userTransaction = array_filter(
            $transactions, fn( $transaction ) => $transaction['user_id'] === $userId
        );

        return $userTransaction;
    }

    public function getUserById( int $id ): array | bool {
        $users = FileHelper::readFile( FilePaths::USERS );

        // Iterate over users to find the one with the matching ID
        foreach ( $users as $user ) {
            if ( $user['id'] === $id ) {
                return $user;
            }
        }

        return false;
    }

    public function getUserByEmail( string $email ): array | bool {
        $users = FileHelper::readFile( FilePaths::USERS );

        // Iterate over users to find the one with the matching email
        foreach ( $users as $user ) {
            if ( $user['email'] === $email ) {
                return $user;
            }
        }

        return false;
    }

    public function updateUserBalance( int $userId, int | float $balance ): void {
        $users = FileHelper::readFile( FilePaths::USERS );

        // Iterate over users to update the balance for the matching user
        foreach ( $users as &$user ) {
            if ( $user['id'] === $userId ) {
                $user['balance'] = $balance;
                break;
            }
        }

        FileHelper::writeFile( FilePaths::USERS, $users );
    }

    public function recordTransaction( int $userId, string $type, int | float $amount ): void {
        // Bangladesh Timezone
        date_default_timezone_set( 'Asia/Dhaka' );

        $transactions = $this->allTransactions();

        // Generate a new unique ID for the transaction
        $id = FileHelper::generateId( $transactions );

        // Create a new transaction array
        $transactions[] = [
            'id'         => $id,
            'user_id'    => $userId,
            'type'       => $type,
            'amount'     => $amount,
            'created_at' => date( 'Y-m-d H:i:s' ),
        ];

        // Save the updated transactions array
        $this->saveTransaction( $transactions );
    }

    public function deposit( int $userId, int | float $amount ): void {
        $user = $this->getUserById( $userId );

        // Update the user's balance and record the transaction
        $user['balance'] += $amount;
        $this->updateBalanceAndSaveRecord( $userId, $user['balance'], TransactionTypes::DEPOSIT, $amount );
    }

    public function withdraw( int $userId, int | float $amount ): void {
        $user = $this->getUserById( $userId );

        if ( $user['balance'] <= $amount ) {
            throw new Exception( 'Insufficient balance' );
        }

        // Update the user's balance and record the transaction
        $user['balance'] -= $amount;
        $this->updateBalanceAndSaveRecord( $userId, $user['balance'], TransactionTypes::WITHDRAW, $amount );
    }

    public function transfer( int $senderId, string $receiverEmail, int | float $amount ): void {
        $sender = $this->getUserById( $senderId );
        $receiver = $this->getUserByEmail( $receiverEmail );

        if ( $sender['balance'] <= $amount ) {
            throw new Exception( 'Insufficient balance' );
        }

        // Update balances for both sender and receiver, and record the transactions
        $sender['balance'] -= $amount;
        $receiver['balance'] += $amount;

        $this->updateBalanceAndSaveRecord( $senderId, $sender['balance'], TransactionTypes::TRANSFER, $amount );
        $this->updateBalanceAndSaveRecord( $receiver['id'], $receiver['balance'], TransactionTypes::RECEIVE, $amount );
    }

    private function updateBalanceAndSaveRecord( int $userId, int | float $balance, string $type, int | float $amount ): void {
        $this->updateUserBalance( $userId, $balance );
        $this->recordTransaction( $userId, $type, $amount );
    }
}