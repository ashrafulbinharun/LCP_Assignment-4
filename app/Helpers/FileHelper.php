<?php

namespace App\Helpers;

class FileHelper {
    public static function readFile( string $filePath ): array {
        // Check if the file exists
        if ( file_exists( $filePath ) ) {
            $data = file_get_contents( $filePath );
            return json_decode( $data, true );
        }

        return [];
    }

    public static function writeFile( string $filePath, array $data ): void {
        // Ensure the directory exists
        if ( !is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 0755, true );
        }

        $jsonData = json_encode( $data, JSON_PRETTY_PRINT );

        file_put_contents( $filePath, $jsonData );
    }

    public static function generateId( array $data ): int {
        $length = count( $data );

        if ( $length > 0 ) {
            // Get the maximum ID from the data and increment it by 1
            $id = max( array_column( $data, 'id' ) ) + 1;
        } else {
            // Start IDs from 1 if the data array is empty
            $id = 1;
        }

        return $id;
    }
}