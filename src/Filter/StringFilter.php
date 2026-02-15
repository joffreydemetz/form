<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class StringFilter extends Filter
{
    // public string $name = 'string';

    public function __construct(array $config = [])
    {
        $config = array_merge([
            // 'authTags' => [],
            // 'emptyString' => '',
        ], $config);

        parent::__construct($config);
    }

    protected function clean($value): mixed
    {
        $value = parent::clean($value);

        $value = \mb_encode_numericentity(
            \htmlspecialchars_decode(\htmlentities($value, \ENT_NOQUOTES, 'UTF-8', false), \ENT_NOQUOTES),
            [0x80, 0x10FFFF, 0, ~0],
            'UTF-8'
        );

        if (empty($value)) {
            return $this->config->get('emptyString', '');
        }

        try {
            $doc = new \DOMDocument('1.0', 'UTF-8');
            $doc->preserveWhiteSpace = false;
            $doc->loadHtml($value);

            $els = $doc->getElementsByTagName('script');
            for ($i = 0, $n = $els->length; $i < $n; $i++) {
                $els->item($i)->parentNode->removeChild($els->item($i));
            }

            $body = $doc->getElementsByTagName('body');

            if ($body && 0 < $body->length) {
                $body = $body->item(0);
                $value = $doc->savehtml($body);
                $value = substr($value, 6, -7);
            } else {
                return $this->config->get('emptyString', '');
            }
        } catch (\Exception $e) {
            return $this->config->get('emptyString', '');
        }

        if ('' !== $value) {
            if ($authTags = $this->config->getArray('authTags')) {
                $value = strip_tags($value, '<' . implode('><', $authTags) . '>');
            } else {
                $value = strip_tags($value);
            }
        }

        return $value;
    }
}
