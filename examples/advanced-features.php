<?php
/**
 * Example: Advanced Form Features
 *
 * Demonstrates filters, custom rules, date fields, number fields,
 * select with optgroups, honeypot bot protection, and callback filters/rules.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use JDZ\Form\Form;
use JDZ\Form\FormData;
use JDZ\Form\FormFieldset;
use JDZ\Form\FormRow;
use JDZ\Form\FormButton;
use JDZ\Form\Field\Text;
use JDZ\Form\Field\Number;
use JDZ\Form\Field\Date;
use JDZ\Form\Field\Time;
use JDZ\Form\Field\Color;
use JDZ\Form\Field\Tel;
use JDZ\Form\Field\Url;
use JDZ\Form\Field\Select;
use JDZ\Form\SelectFieldOption;
use JDZ\Form\SelectFieldOptgroup;
use JDZ\Form\Rule\BotRule;
use JDZ\Form\Rule\GtRule;
use JDZ\Form\Rule\CallbackRule;
use JDZ\Form\Filter\CallbackFilter;

echo "=== Advanced Features Example ===\n\n";

// ---- 1. Number field with min/max constraints ----

echo "--- Number Field ---\n";

$form = new Form('event');
$form->addFieldset(new FormFieldset('main'));

$ageRow = new FormRow('age');
$ageRow->setLabelText('Age')->withRequired();
$ageField = new Number('age');
$ageField->init();
$ageField->setMin(18);
$ageField->setMax(120);
$ageRow->setField($ageField);
$form->addField($ageRow);

$data = new FormData(['age' => '25']);
$form->setData($data);
$form->filter();

echo "Age (input: '25'): " . $data->get('age') . "\n";

// Test with out-of-range value
$ageField->setValue('150');
echo "Age (input: '150', max: 120): " . $ageField->value . "\n";

// ---- 2. Date field ----

echo "\n--- Date Field ---\n";

$dateField = new Date('event_date');
$dateField->init();
$dateField->setMin('2024-01-01');
$dateField->setMax('2025-12-31');
$dateField->setValue('2024-06-15');

echo "Event date: " . $dateField->value . "\n";
echo "Readable: " . $dateField->toStatic() . "\n";

// ---- 3. Time field ----

echo "\n--- Time Field ---\n";

$timeField = new Time('start_time');
$timeField->init();
$timeField->setMin('08:00');
$timeField->setMax('18:00');
$timeField->setValue('14:30');

echo "Start time: " . $timeField->value . "\n";

// ---- 4. Color field ----

echo "\n--- Color Field ---\n";

$colorField = new Color('theme_color');
$colorField->init();
$colorField->setValue('#ff5500');

echo "Theme color: " . $colorField->value . "\n";

// ---- 5. Tel field ----

echo "\n--- Tel Field ---\n";

$telField = new Tel('phone');
$telField->init();

$telData = new FormData(['phone' => '+33 (0)1 23 45 67 89']);
$telField->filter($telData);

echo "Phone (filtered): " . $telData->get('phone') . "\n";

// ---- 6. URL field ----

echo "\n--- URL Field ---\n";

$urlField = new Url('website');
$urlField->init();

$urlData = new FormData(['website' => 'https://example.com/path?q=test']);
$urlField->filter($urlData);

echo "URL (filtered): " . $urlData->get('website') . "\n";

// ---- 7. Select with optgroups ----

echo "\n--- Select with Optgroups ---\n";

$langField = new Select('language');

$european = new SelectFieldOptgroup('European');
$european->setOptions([
    new SelectFieldOption('fr', 'French'),
    new SelectFieldOption('de', 'German'),
    new SelectFieldOption('es', 'Spanish'),
]);

$asian = new SelectFieldOptgroup('Asian');
$asian->setOptions([
    new SelectFieldOption('zh', 'Chinese'),
    new SelectFieldOption('ja', 'Japanese'),
    new SelectFieldOption('ko', 'Korean'),
]);

$langField->addOption($european);
$langField->addOption($asian);
$langField->setValue('fr');

echo "Selected language: " . $langField->toStatic() . "\n";

// ---- 8. Honeypot bot protection ----

echo "\n--- Bot Protection (Honeypot) ---\n";

$form2 = new Form('protected');
$form2->addFieldset(new FormFieldset('main'));

$honeypotRow = new FormRow('website_url');
$honeypotField = new Text('website_url');
$honeypotField->addRule(new BotRule());
$honeypotRow->setField($honeypotField);
$form2->addField($honeypotRow);

// Legitimate user (empty honeypot)
$legitimateData = new FormData(['website_url' => '']);
$form2->setData($legitimateData);
echo "Legitimate user: " . ($form2->submit() ? 'PASS' : 'FAIL') . "\n";

// Bot (filled honeypot)
$form3 = new Form('protected');
$form3->addFieldset(new FormFieldset('main'));

$honeypotRow2 = new FormRow('website_url');
$honeypotField2 = new Text('website_url');
$honeypotField2->addRule(new BotRule());
$honeypotField2->setValue('spam-content');
$honeypotRow2->setField($honeypotField2);
$form3->addField($honeypotRow2);

$botData = new FormData(['website_url' => 'spam-content']);
$form3->setData($botData);
echo "Bot attempt: " . ($form3->submit() ? 'PASS' : 'BLOCKED') . "\n";

// ---- 9. Comparison rule (greater than) ----

echo "\n--- Comparison Rules ---\n";

$form4 = new Form('range');
$form4->addFieldset(new FormFieldset('main'));

$minRow = new FormRow('min_value');
$minField = new Number('min_value');
$minField->init();
$minRow->setField($minField);
$form4->addField($minRow);

$maxRow = new FormRow('max_value');
$maxField = new Number('max_value');
$maxField->init();
$gtRule = new GtRule('Max must be greater than min');
$gtRule->setCompareTo('min_value');
$maxField->addRule($gtRule);
$maxRow->setField($maxField);
$form4->addField($maxRow);

$rangeData = new FormData(['min_value' => '10', 'max_value' => '50']);
$form4->setData($rangeData);
echo "Range 10..50: " . ($form4->submit() ? 'VALID' : 'INVALID') . "\n";

// ---- 10. Callback filter and rule ----

echo "\n--- Callback Filter/Rule ---\n";

$form5 = new Form('custom');
$form5->addFieldset(new FormFieldset('main'));

$slugRow = new FormRow('slug');
$slugField = new Text('slug');

// Custom filter: convert to URL-safe slug
$slugField->addFilter(new CallbackFilter([
    'callback' => function ($field, $data) {
        $value = $data->get($field->getName());
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');
        $field->setValue($value);
        $data->set($field->getName(), $value);
    },
]));

// Custom rule: must not start with a number
$customRule = new CallbackRule('Slug must not start with a number');
$customRule->setCallback(function ($field, $data, $message) {
    if (preg_match('/^[0-9]/', $field->value)) {
        throw new \JDZ\Form\Exception\InvalidException($message);
    }
});
$slugField->addRule($customRule);
$slugRow->setField($slugField);
$form5->addField($slugRow);

$slugData = new FormData(['slug' => 'My Blog Post Title!']);
$form5->setData($slugData);
$form5->submit();

echo "Slug: '" . $slugData->get('slug') . "'\n";
echo "Valid: " . (empty($form5->errors) ? 'YES' : 'NO') . "\n";
