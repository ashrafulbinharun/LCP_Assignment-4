<?php

namespace App\Services;

use App\Constants\FilePaths;
use App\Helpers\FileHelper;
use Exception;

class AuthService {
    public function getUsers(): array {
        return FileHelper::readFile( FilePaths::USERS );
    }

    public function saveUsers( $users ): void {
        FileHelper::writeFile( FilePaths::USERS, $users );
    }

    public function isDuplicateEmail( string $email ): bool {
        $users = $this->getUsers();

        // Checks if the given email already exists
        foreach ( $users as $user ) {
            if ( $user['email'] === $email ) {
                return true;
            }
        }

        return false;
    }

    public function registerUser( string $name, string $email, string $password, string $role = "customer", int $id = null ): void {
        $users = $this->getUsers();

        // Generate a new ID
        if ( $id === null ) {
            $id = FileHelper::generateId( $users );
        }

        $hashedPassword = password_hash( $password, PASSWORD_BCRYPT );

        // Create the new user array
        $newUser = [
            'id'       => $id,
            'role'     => $role,
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'balance'  => 0,
        ];

        // Add the new user
        $users[] = $newUser;
        $this->saveUsers( $users );
    }

    public function authenticateUser( string $email, string $password ): array {
        $users = $this->getUsers();

        foreach ( $users as $user ) {
            if ( $user['email'] === $email ) {
                if ( password_verify( $password, $user['password'] ) ) {
                    return $user;
                } else {
                    throw new Exception( 'Invalid password' );
                }
            }
        }

        throw new Exception( 'User not found' );
    }
}