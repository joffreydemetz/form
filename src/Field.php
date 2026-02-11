<?php
declare(strict_types=1);

namespace JDZ\Form;

use JDZ\Form\Contract\FieldInterface;
use JDZ\Form\Filter\StringFilter;
use JDZ\Form\Rule\RequiredRule;
use JDZ\Form\Exception\RuleException;
use JDZ\Renderer\Element;
use JDZ\Utils\Data as jData;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class Field extends Element implements FieldInterface
{
    public jData $config;

    public string $uid = '';
    public string $prefix = '';
    public string $autocomplete = '';
    public string $errorMessage = 'Incorrect value';
    public bool $required = false;
    public bool $disabled = false;
    public bool $readonly = false;
    public bool $arrayName = false;
    public bool $noCheck = false;
    public bool $immutable = false;
    public array $filters = [];
    public array $rules = [];
    public mixed $value = null;
    public string $default = '';
    public array $errors = [];
    protected string $renderer = 'field';

    public function __construct(string $name, array $config = [])
    {
        $this->setName($name);

        $this->config = new jData();
        if ($config) {
            $this->config->sets($config);
        }
    }

    public function __clone()
    {
        parent::__clone();

        $this->filters = [];
        $this->rules = [];
        $this->required = false;
        $this->readonly = false;
        $this->arrayName = false;
        $this->noCheck = false;
        $this->immutable = false;
        $this->position = 0;
        $this->value = null;
        $this->default = '';
    }

    public function init()
    {
        $this->addFilter(
            new StringFilter()
        );
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        if (false === $this->immutable) {
            $this->value = $value;
        }
        return $this;
    }

    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    public function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setAutocomplete(string $autocomplete)
    {
        $this->autocomplete = $autocomplete;
        return $this;
    }

    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    public function addFilter(Filter $filter)
    {
        if (!isset($this->filters[$filter->name])) {
            $this->filters[$filter->name] = $filter;
        }
        return $this;
    }

    public function getFilter(string $name): ?Filter
    {
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }
        return null;
    }

    public function removeFilter(string $name)
    {
        if (isset($this->filters[$name])) {
            unset($this->filters[$name]);
        }
        return $this;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }

    public function addRule(Rule $rule, bool $replace = false)
    {
        if (false === $replace && isset($this->rules[$rule->name])) {
            throw new \RuntimeException('Rule already set for ' . $rule->name);
        }

        $this->rules[$rule->name] = $rule;
        return $this;
    }

    public function removeRule(string $name)
    {
        if (isset($this->rules[$name])) {
            unset($this->rules[$name]);
        }
        return $this;
    }

    public function noRules()
    {
        $this->rules = [];
        return $this;
    }

    public function withRequired(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function withDisabled(bool $disabled = true)
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function withReadonly(bool $readonly = true)
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function withImmutable(bool $immutable = true)
    {
        $this->immutable = $immutable;
        return $this;
    }

    public function withArrayName(bool $arrayName = true)
    {
        $this->arrayName = $arrayName;
        return $this;
    }

    public function withNoCheck(bool $noCheck = true)
    {
        $this->noCheck = $noCheck;
        return $this;
    }

    public function isStaticEmpty(): bool
    {
        return '' !== $this->toStatic();
    }

    public function isEmpty(): bool
    {
        return '' === $this->value;
    }

    public function filter(FormData $data)
    {
        if (empty($this->filters)) {
            $this->addFilter(new StringFilter());
        }

        if (true === $this->disabled) {
            $data->erase($this->name);
        } else {
            foreach ($this->filters as $filter) {
                $filter->execute($this, $data);
            }
        }

        return $this;
    }

    public function validate(FormData $data)
    {
        if (true === $this->disabled) {
            $data->erase($this->name);
            return true;
        }

        $rules = $this->rules;

        if (true === $this->required && !isset($rules['required'])) {
            array_unshift($rules, new RequiredRule('This field is required'));
        }

        foreach ($rules as $rule) {
            try {
                $rule->execute($this, $data);
            } catch (RuleException $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        return empty($this->errors);
    }

    public function onFillValues(FormData $data)
    {
        if (false === $data->has($this->name)) {
            $value = $this->default;
        } else {
            $value = $data->get($this->name);
        }

        $data->set($this->name, $value);
        $this->setValue($value);
    }

    public function toData(): array
    {
        $data = parent::toData();

        $data['name'] = $this->name;

        if (true === $this->disabled) {
            $data['static'] = true;
            $data['staticValue'] = $this->toStatic();
        }

        if ($this->value) {
            $data['value'] = $this->value;
        }

        return $data;
    }

    public function toStatic(): string
    {
        return $this->getValue();
    }

    protected function renderAttrs(): array
    {
        $attrs = parent::renderAttrs();

        if ($this->prefix) {
            $attrs['name'] = $this->prefix . '[' . $this->name . ']';
        } else {
            $attrs['name'] = $this->name;
        }

        if (true === $this->arrayName) {
            $attrs['name'] .= '[]';
        }

        if ('' !== $this->autocomplete) {
            $attrs['autocomplete'] = $this->autocomplete;
        }

        if (true === $this->required) {
            $attrs['required'] = 'true';
        }

        if (true === $this->disabled) {
            $attrs['disabled'] = 'true';
        }

        if (true === $this->readonly) {
            $attrs['readonly'] = 'true';
        }

        return $attrs;
    }
}
