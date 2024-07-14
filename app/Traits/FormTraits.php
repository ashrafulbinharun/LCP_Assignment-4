<?php

namespace App\Traits;

trait FormTraits {
    protected array $errors = [];
    protected array $oldInput = [];

    public function getPostData( array $fields ): array {
        $data = [];
        foreach ( $fields as $field ) {
            $data[$field] = $_POST[$field] ?? '';
        }

        return $data;
    }

    public function setErrors( array $errors ): void {
        $this->errors = $errors;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function setOldInput( array $oldInput ): void {
        $this->oldInput = $oldInput;
    }

    public function getOldInput(): array {
        return $this->oldInput;
    }
}