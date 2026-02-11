<?php
/**
 * Example: User Registration Form
 *
 * Demonstrates password validation, field comparison (equals rule),
 * select fields with options, and checkbox fields.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Form\Form;
use JDZ\Form\FormData;
use JDZ\Form\FormFieldset;
use JDZ\Form\FormRow;
use JDZ\Form\FormButton;
use JDZ\Form\Field\Text;
use JDZ\Form\Field\Email;
use JDZ\Form\Field\Password;
use JDZ\Form\Field\Select;
use JDZ\Form\Field\Checkbox;
use JDZ\Form\SelectFieldOption;
use JDZ\Form\Rule\EqualsRule;

// --- Build the form ---

$form = new Form('register');
$form->setAction('/register')
    ->setMethod('POST');

// Account fieldset
$form->addFieldset(
    (new FormFieldset('main'))->setLabel('Account Information')
);

// Username
$usernameRow = new FormRow('username');
$usernameRow->setLabelText('Username')->withRequired();
$usernameField = new Text('username');
$usernameField->setPlaceholder('Choose a username')
    ->setMaxlength(50);
$usernameRow->setField($usernameField);
$form->addField($usernameRow);

// Email
$emailRow = new FormRow('email');
$emailRow->setLabelText('Email')->withRequired();
$emailField = new Email('email');
$emailField->init();
$emailRow->setField($emailField);
$form->addField($emailRow);

// Password
$passwordRow = new FormRow('password');
$passwordRow->setLabelText('Password')
    ->setTip('Min 8 chars, must include uppercase, lowercase, digit, and special char')
    ->withRequired();
$passwordField = new Password('password');
$passwordField->setPw(['min' => 8, 'max' => 20, 'upper' => 1, 'lower' => 1, 'digit' => 1, 'special' => 1]);
$passwordRow->setField($passwordField);
$form->addField($passwordRow);

// Password confirmation with equals rule
$confirmRow = new FormRow('password_confirm');
$confirmRow->setLabelText('Confirm Password')->withRequired();
$confirmField = new Password('password_confirm');
$equalsRule = new EqualsRule('Passwords do not match');
$equalsRule->setCompareTo('password');
$confirmField->addRule($equalsRule);
$confirmRow->setField($confirmField);
$form->addField($confirmRow);

// Profile fieldset
$form->addFieldset(
    (new FormFieldset('profile'))->setLabel('Profile Details')
);

// Country select
$countryRow = new FormRow('country');
$countryRow->setLabelText('Country');
$countryField = new Select('country');
$countryField->addOption(new SelectFieldOption('', '-- Select --'));
$countryField->addOption(new SelectFieldOption('fr', 'France'));
$countryField->addOption(new SelectFieldOption('us', 'United States'));
$countryField->addOption(new SelectFieldOption('uk', 'United Kingdom'));
$countryField->addOption(new SelectFieldOption('de', 'Germany'));
$countryField->addOption(new SelectFieldOption('jp', 'Japan'));
$countryRow->setField($countryField);
$form->addField($countryRow, 'profile');

// Terms checkbox
$termsRow = new FormRow('terms');
$termsRow->withRequired();
$termsField = new Checkbox('terms');
$termsField->setCheckboxLabel('I agree to the Terms of Service');
$termsField->setCheckboxValue('1');
$termsRow->setField($termsField);
$form->addField($termsRow, 'profile');

// Submit button
$submit = new FormButton('register');
$submit->setText('Create Account');
$submit->addStyle('btn-success');
$form->addButton($submit);

// --- Test with valid data ---

echo "=== Registration Form Example ===\n\n";

$validData = new FormData([
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => 'Str0ng!Pass',
    'password_confirm' => 'Str0ng!Pass',
    'country' => 'fr',
    'terms' => '1',
]);
$form->setData($validData);

$isValid = $form->submit();

if ($isValid) {
    echo "Registration successful!\n";
    echo "Username: " . $validData->get('username') . "\n";
    echo "Email: " . $validData->get('email') . "\n";
    echo "Country: " . $validData->get('country') . "\n";
} else {
    echo "Validation errors:\n";
    foreach ($form->errors as $error) {
        echo "  - " . $error . "\n";
    }
}

// --- Test with mismatched passwords ---

echo "\n--- Testing mismatched passwords ---\n\n";

$form2 = new Form('register');
$form2->addFieldset(new FormFieldset('main'));

$pwRow = new FormRow('password');
$pwRow->setLabelText('Password')->withRequired();
$pwField = new Password('password');
$pwRow->setField($pwField);
$form2->addField($pwRow);

$confirmRow2 = new FormRow('password_confirm');
$confirmRow2->setLabelText('Confirm Password')->withRequired();
$confirmField2 = new Password('password_confirm');
$equalsRule2 = new EqualsRule('Passwords do not match');
$equalsRule2->setCompareTo('password');
$confirmField2->addRule($equalsRule2);
$confirmRow2->setField($confirmField2);
$form2->addField($confirmRow2);

$badData = new FormData([
    'password' => 'Str0ng!Pass',
    'password_confirm' => 'DifferentPass1!',
]);
$form2->setData($badData);

$isValid = $form2->submit();

if (!$isValid) {
    echo "Correctly rejected mismatched passwords:\n";
    foreach ($form2->errors as $error) {
        echo "  - " . $error . "\n";
    }
}
