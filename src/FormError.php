<?php

declare(strict_types=1);

namespace JDZ\Form;

enum FormError: string
{
    case REQUIRED           = 'This field is required';
    case INVALID_EMAIL      = 'Invalid email address';
    case VALUES_NOT_EQUAL   = 'Values do not match';
    case TOO_MANY_CHARS     = 'Too many characters';
    case COMPARE_FAILED     = 'Compare failed';
    case NOT_GREATER_THAN   = 'Field integer value <= Control integer value';
    case NOT_LESS_THAN      = 'Field integer value >= Control integer value';
    case INVALID_DATE       = 'Invalid date';
    case DATE_NOT_GT        = 'Field date value <= Control date value';
    case DATE_NOT_LT        = 'Field date value >= Control date value';
    case INVALID_TIME       = 'Invalid time';
    case INVALID_TEL        = 'Invalid phone number';
    case INVALID_COLOR      = 'Invalid hex color';
    case INVALID_PASSWORD   = 'Invalid password';
    case BOT_DETECTED       = 'No bots allowed';
    case CALLBACK_FAILED    = 'Condition failed';
    case PATTERN_MISMATCH   = 'Pattern mismatch';
    case INCORRECT_VALUE    = 'Incorrect value';
    case INVALID_NUMBER     = 'Invalid number';
    case INVALID_URL        = 'Invalid url';
    case EXCEEDS_LENGTH     = 'This field exceeds the allowed length';
    case DUPLICATE_ENTRY    = 'Duplicate entry';
    case CAPTCHA_FAILED     = 'Captcha verification failed';
    case CUSTOM             = 'Custom error';
}
