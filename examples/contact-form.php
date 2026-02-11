<?php
/**
 * Example: Contact Form
 *
 * Demonstrates building a basic contact form with validation.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Form\Form;
use JDZ\Form\FormData;
use JDZ\Form\FormFieldset;
use JDZ\Form\FormRow;
use JDZ\Form\FormButton;
use JDZ\Form\Field\Text;
use JDZ\Form\Field\Email;
use JDZ\Form\Field\Textarea;
use JDZ\Form\Field\Hidden;

// --- Build the form ---

$form = new Form('contact');
$form->setAction('/contact/submit')
    ->setMethod('POST')
    ->withVertical();

// Create fieldsets
$form->addFieldset(new FormFieldset('main'));

// Name field
$nameRow = new FormRow('name');
$nameRow->setLabelText('Your Name')
    ->setTip('Enter your full name')
    ->withRequired();
$nameField = new Text('name');
$nameField->setPlaceholder('John Doe')
    ->setMaxlength(100);
$nameRow->setField($nameField);
$form->addField($nameRow);

// Email field
$emailRow = new FormRow('email');
$emailRow->setLabelText('Email Address')
    ->withRequired();
$emailField = new Email('email');
$emailField->init();
$emailField->setPlaceholder('john@example.com');
$emailRow->setField($emailField);
$form->addField($emailRow);

// Message field
$messageRow = new FormRow('message');
$messageRow->setLabelText('Message')
    ->withRequired();
$messageField = new Textarea('message');
$messageField->setPlaceholder('Your message here...')
    ->setRows(6)
    ->setMaxlength(2000);
$messageRow->setField($messageField);
$form->addField($messageRow);

// Hidden field (outside fieldset)
$hiddenRow = new \JDZ\Form\FormRow\Hidden('form_token');
$hiddenField = new Hidden('form_token');
$hiddenField->setValue('abc123');
$hiddenRow->setField($hiddenField);
$form->addField($hiddenRow);

// Submit button
$submit = new FormButton('submit');
$submit->setText('Send Message');
$submit->addStyle('btn-primary');
$form->addButton($submit);

// --- Initialize with data ---

$data = new FormData([
    'name' => '',
    'email' => '',
    'message' => '',
    'form_token' => 'abc123',
]);

$form->init($data);

// --- Simulate form submission ---

echo "=== Contact Form Example ===\n\n";

// Simulate POST data
$postData = new FormData([
    'name' => '  John Doe  ',
    'email' => 'John@Example.COM',
    'message' => 'Hello, I would like to get in touch!',
    'form_token' => 'abc123',
]);
$form->setData($postData);

$isValid = $form->submit();

if ($isValid) {
    echo "Form is valid!\n";
    echo "Name: " . $postData->get('name') . "\n";
    echo "Email: " . $postData->get('email') . "\n";
    echo "Message: " . $postData->get('message') . "\n";
} else {
    echo "Form has errors:\n";
    foreach ($form->errors as $error) {
        echo "  - " . $error . "\n";
    }
}

echo "\n--- Testing with invalid data ---\n\n";

// Reset form
$form2 = new Form('contact');
$form2->addFieldset(new FormFieldset('main'));

$nameRow2 = new FormRow('name');
$nameRow2->withRequired();
$nameRow2->setLabelText('Name');
$nameRow2->setField(new Text('name'));
$form2->addField($nameRow2);

$emailRow2 = new FormRow('email');
$emailRow2->withRequired();
$emailRow2->setLabelText('Email');
$emailField2 = new Email('email');
$emailField2->init();
$emailRow2->setField($emailField2);
$form2->addField($emailRow2);

$invalidData = new FormData([
    'name' => '',
    'email' => 'not-an-email',
]);
$form2->setData($invalidData);

$isValid = $form2->submit();

if (!$isValid) {
    echo "Form correctly rejected invalid data:\n";
    foreach ($form2->errors as $error) {
        echo "  - " . $error . "\n";
    }
}
