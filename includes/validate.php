<?php
/**
 * Shared server-side validation and sanitization functions
 */

// Sanitize user input (trim whitespace, remove backslashes, convert special chars to HTML entities)
function sanitize_input($data) {
    if ($data === null) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if a field is empty
function validate_required($value) {
    return !empty(trim($value));
}

// Validate email format
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate that a value is numeric and optionally positive
function validate_numeric($value, $must_be_positive = true) {
    if (!is_numeric($value)) {
        return false;
    }
    if ($must_be_positive && $value < 0) {
        return false;
    }
    return true;
}

// Validate minimum length (e.g. for cover letter)
function validate_min_length($string, $min_length) {
    return strlen(trim($string)) >= $min_length;
}

// Validate mock credit card number (format only: 16 digits)
function validate_mock_card($card_number) {
    // Remove spaces and dashes
    $cleaned = str_replace([' ', '-'], '', $card_number);
    return preg_match('/^\d{16}$/', $cleaned);
}

// Validate mock expiry date (format only: MM/YY)
function validate_mock_expiry($expiry) {
    return preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', trim($expiry));
}

// Validate mock CVV (format only: 3 or 4 digits)
function validate_mock_cvv($cvv) {
    return preg_match('/^\d{3,4}$/', trim($cvv));
}
?>