<?php
declare(strict_types=1);

namespace JDZ\Form\Field;

use JDZ\Form\InputField;
use JDZ\Form\FormData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Password extends InputField
{
    public string $type = 'password';
    public string $autocomplete = 'new-password';
    public string $errorMessage = 'Invalid password';

    public array $pw = [
        'min' => 8,
        'max' => 20,
        'upper' => 1,
        'lower' => 1,
        'digit' => 1,
        'special' => 1,
    ];

    public function setPw(array $config)
    {
        $this->pw = array_merge($this->pw, $config);

        foreach ($this->pw as $k => $v) {
            $this->dataAttrs[$k] = $v;
        }

        $this->maxlength = $this->pw['max'];
        $this->pattern = $this->buildPattern();

        return $this;
    }

    public function validate(FormData $data)
    {
        if (false === $this->noCheck && !isset($this->rules['password'])) {
            $rule = new \JDZ\Form\Rule\PasswordRule($this->errorMessage);
            $rule->setPattern($this->pattern);
            $this->addRule($rule);
        }

        return parent::validate($data);
    }

    protected function buildPattern()
    {
        $pattern = '^';

        if ($this->pw['upper'] === 1) {
            $pattern .= '(?=.*[A-Z])';
        } elseif ($this->pw['upper'] > 1) {
            $pattern .= '(?=(.*[A-Z]){' . $this->pw['upper'] . ',})';
        }

        if ($this->pw['lower'] === 1) {
            $pattern .= '(?=.*[a-z])';
        } elseif ($this->pw['lower'] > 1) {
            $pattern .= '(?=(.*[a-z]){' . $this->pw['lower'] . ',})';
        }

        if ($this->pw['digit'] === 1) {
            $pattern .= '(?=.*[\d])';
        } elseif ($this->pw['digit'] > 1) {
            $pattern .= '(?=(.*[\d]){' . $this->pw['digit'] . ',})';
        }

        if ($this->pw['special'] === 1) {
            $pattern .= '(?=.*[\W])';
        } elseif ($this->pw['special'] > 1) {
            $pattern .= '(?=(.*[\W]){' . $this->pw['special'] . ',})';
        }

        $pattern .= '(?!.*\s).{' . $this->pw['min'] . ',' . $this->pw['max'] . '}$';

        return $pattern;
    }
}
