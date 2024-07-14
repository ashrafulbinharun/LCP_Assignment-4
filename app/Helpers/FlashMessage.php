<?php

namespace App\Helpers;

class FlashMessage {
    public static function setMessage( string $key, $message ): void {
        self::startSession();

        // Set the flash message in session
        $_SESSION['flash'][$key] = $message;
    }

    public static function getMessage( string $key, bool $clear = true ): ?string {
        self::startSession();

        // Check if the flash message exists in session
        if ( isset( $_SESSION['flash'][$key] ) ) {
            $message = $_SESSION['flash'][$key];

            // Optionally clear the message from session
            if ( $clear ) {
                unset( $_SESSION['flash'][$key] );
            }

            return $message;
        }
        return null;
    }

    private static function startSession() {
        if ( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }
    }
}