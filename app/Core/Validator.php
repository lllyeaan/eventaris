<?php
declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $rule = trim($rule);
                [$ruleName, $ruleValue] = array_pad(explode(':', $rule, 2), 2, null);

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                            $this->addError($field, 'This field is required.');
                        }
                        break;
                    case 'email':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $this->addError($field, 'The email format is invalid.');
                        }
                        break;
                    case 'min':
                        $min = (int) $ruleValue;
                        if ($value !== null && strlen((string) $value) < $min) {
                            $this->addError(
                                $field,
                                sprintf('Panjang minimal %d karakter.', $min)
                            );
                        }
                        break;
                    case 'max':
                        $max = (int) $ruleValue;
                        if ($value !== null && strlen((string) $value) > $max) {
                            $this->addError(
                                $field,
                                sprintf('Panjang maksimal %d karakter.', $max)
                            );
                        }
                        break;
                    case 'numeric':
                        if ($value !== null && $value !== '' && !is_numeric($value)) {
                            $this->addError($field, 'This field must be numeric.');
                        }
                        break;
                }
            }
        }

        return !$this->fails();
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }
}
