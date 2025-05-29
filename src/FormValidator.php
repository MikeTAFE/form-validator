<?php

/**
 * FormValidator.php
 *
 * A lightweight, reusable, and extensible PHP 8+ form validation class.
 *
 * @package   MikeTAFE\FormValidator
 * @author    Michael Kirkwood-Smith
 * @license   MIT License
 * @link      https://github.com/MikeTAFE/form-validator
 *
 * Copyright (c) 2025 Michael Kirkwood-Smith
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MikeTAFE;

/**
 * Class FormValidator
 *
 * A reusable helper class for validating form input data in plain PHP.
 * Supports required fields, types, string lengths, number ranges,
 * boolean values, regex patterns, and nested fields via dot notation.
 */
class FormValidator
{
    /**
     * Validates form input against a set of rules.
     *
     * @param array $data  Input data, typically $_POST or $_GET
     * @param array $rules Validation rules in the format:
     *                     [
     *                       'field' => [
     *                         'required',
     *                         'email' => 'Custom error',
     *                         'min_length' => [3, 'Too short']
     *                       ]
     *                     ]
     * @return array<string, string> Array of field => error message
     */
    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $this->getValueByPath($data, $field);

            foreach ($fieldRules as $rule => $ruleValue) {
                $ruleName = is_int($rule) ? $ruleValue : $rule;
                $params = [];
                $customMsg = null;

                if (!is_int($rule) && is_array($ruleValue)) {
                    $params[] = $ruleValue[0];
                    $customMsg = $ruleValue[1] ?? null;
                } elseif (!is_int($rule)) {
                    $customMsg = $ruleValue;
                }

                $result = match ($ruleName) {
                    'required'    => $this->validateRequired($value),
                    'email'       => $this->validateEmail($value),
                    'int'         => $this->validateInt($value),
                    'float'       => $this->validateFloat($value),
                    'boolean'     => $this->validateBoolean($value),
                    'date'        => $this->validateDate($value),
                    'min_length'  => $this->validateMinLength($value, $params[0]),
                    'max_length'  => $this->validateMaxLength($value, $params[0]),
                    'min'         => $this->validateMin($value, $params[0]),
                    'max'         => $this->validateMax($value, $params[0]),
                    'regex'       => $this->validateRegex($value, $params[0] ?? null),
                    default       => "Unknown validation rule: $ruleName"
                };

                if ($result !== true) {
                    $errors[$field] = $customMsg ?? $result;
                    break; // Stop after first error per field
                }
            }
        }

        return $errors;
    }

    /**
     * Retrieve a nested value using dot notation (e.g. "user.profile.age").
     *
     * @param array $data Input array
     * @param string $path Dot-separated path
     * @return mixed|null The value at the path or null if not found
     */
    private function getValueByPath(array $data, string $path): mixed
    {
        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (!is_array($data) || !array_key_exists($key, $data)) {
                return null;
            }
            $data = $data[$key];
        }

        return $data;
    }

    /**
     * Validate that a value is present and not empty.
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateRequired(mixed $value): true|string
    {
        return ($value !== null && $value !== '') ? true : 'This field is required.';
    }

    /**
     * Validate that a value is a valid email address.
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateEmail(mixed $value): true|string
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            ? true
            : 'Invalid email address.';
    }

    /**
     * Validate that a value is an integer.
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateInt(mixed $value): true|string
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false
            ? true
            : 'Must be an integer.';
    }

    /**
     * Validate that a value is a float.
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateFloat(mixed $value): true|string
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false
            ? true
            : 'Must be a float.';
    }

    /**
     * Validate that a value is boolean-like (true, false, 1, 0, "1", "0", etc).
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateBoolean(mixed $value): true|string
    {
        $allowed = [true, false, 1, 0, '1', '0', 'true', 'false'];
        return in_array($value, $allowed, true)
            ? true
            : 'Must be a boolean value.';
    }

    /**
     * Validate that a value matches Y-m-d date format.
     *
     * @param mixed $value
     * @return true|string
     */
    private function validateDate(mixed $value): true|string
    {
        if (!is_string($value)) {
            return 'Invalid date format (Y-m-d).';
        }

        $d = \DateTime::createFromFormat('Y-m-d', $value);
        return ($d && $d->format('Y-m-d') === $value)
            ? true
            : 'Invalid date format (Y-m-d).';
    }

    /**
     * Validate that a string is at least a certain length.
     *
     * @param mixed $value
     * @param int $min
     * @return true|string
     */
    private function validateMinLength(mixed $value, int $min): true|string
    {
        if (!is_string($value)) {
            return 'Invalid value for length check.';
        }

        return mb_strlen($value) >= $min
            ? true
            : "Must be at least $min characters long.";
    }

    /**
     * Validate that a string is no more than a certain length.
     *
     * @param mixed $value
     * @param int $max
     * @return true|string
     */
    private function validateMaxLength(mixed $value, int $max): true|string
    {
        if (!is_string($value)) {
            return 'Invalid value for length check.';
        }

        return mb_strlen($value) <= $max
            ? true
            : "Must be no more than $max characters long.";
    }

    /**
     * Validate that a numeric value is greater than or equal to a minimum.
     *
     * @param mixed $value
     * @param float|int $min
     * @return true|string
     */
    private function validateMin(mixed $value, float|int $min): true|string
    {
        if (!is_numeric($value)) {
            return 'Must be a number.';
        }

        return $value >= $min
            ? true
            : "Must be at least $min.";
    }

    /**
     * Validate that a numeric value is less than or equal to a maximum.
     *
     * @param mixed $value
     * @param float|int $max
     * @return true|string
     */
    private function validateMax(mixed $value, float|int $max): true|string
    {
        if (!is_numeric($value)) {
            return 'Must be a number.';
        }

        return $value <= $max
            ? true
            : "Must be no more than $max.";
    }

    /**
     * Validate that a value matches a regular expression.
     *
     * @param mixed $value
     * @param string|null $pattern
     * @return true|string
     */
    private function validateRegex(mixed $value, ?string $pattern): true|string
    {
        if (!$pattern) {
            return 'Invalid regex pattern.';
        }

        return preg_match($pattern, (string)$value)
            ? true
            : 'Invalid format.';
    }
}
