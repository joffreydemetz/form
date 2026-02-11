<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class UrlFilter extends Filter
{
    // public string $type = 'url';

    public function clean($value)
    {
        $value = parent::clean($value);

        if ('' !== $value) {
            $urlParts = \parse_url($value);

            if (\array_key_exists('scheme', $urlParts)) {
                if (!\in_array($urlParts['scheme'], ['http', 'https'])) {
                    $urlParts['scheme'] = 'http';
                }
            } else {
                $urlParts['scheme'] = 'http';
            }

            $scheme = isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '';
            $host = isset($urlParts['host']) ? $urlParts['host'] : '';
            $port = isset($urlParts['port']) ? ':' . $urlParts['port'] : '';
            $path = isset($urlParts['path']) ? $urlParts['path'] : '';
            $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
            $fragment = isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '';
            $user = isset($urlParts['user']) ? $urlParts['user'] : '';
            $pass = isset($urlParts['pass']) ? ':' . $urlParts['pass'] : '';

            $credentials = ($user || $pass) ? "$pass@" : '';

            $value = $scheme . $user . $credentials . $host . $port . $path . $query . $fragment;
        }

        return $value;
    }
}
