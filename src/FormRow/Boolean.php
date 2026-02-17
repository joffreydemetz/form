<?php

declare(strict_types=1);

namespace JDZ\Form\FormRow;

use JDZ\Form\Contract\BooleanInterface;
use JDZ\Form\Field\Radio;
use JDZ\Form\FormData;

class Boolean extends Radioboxes implements BooleanInterface
{
    protected string $yesText = 'Yes';
    protected string $noText = 'No';

    public function __construct(string $name)
    {
        $this->addStyle('boolean');

        parent::__construct($name);
    }

    public function setYesText(string $yesText): static
    {
        $this->yesText = $yesText;
        return $this;
    }

    public function setNoText(string $noText): static
    {
        $this->noText = $noText;
        return $this;
    }

    public function setList(array $list = []): static
    {
        $list = [];

        $yes = new Radio($this->name);
        $yes->init();
        $yes->uid = md5($this->name . 'radio' . Radio::class);
        $yes->setCheckboxValue("1");
        $yes->setCheckboxLabel($this->yesText);

        $no = new Radio($this->name);
        $no->init();
        $no->uid = md5($this->name . 'radio' . Radio::class);
        $no->setCheckboxValue("0");
        $no->setCheckboxLabel($this->noText);

        $list[] = $yes;
        $list[] = $no;

        return parent::setList($list);
    }

    public function onFillValues(FormData $data): void
    {
        parent::onFillValues($data);

        foreach ($this->list as $item) {
            $item->withChecked((string)$data->get($this->name) === $item->value);
        }
    }
}
