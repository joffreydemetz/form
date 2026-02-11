<?php
declare(strict_types=1);

namespace JDZ\Form\Filter;

use JDZ\Form\Filter\StringFilter;

/**
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class EmailFilter extends StringFilter
{
    // public string $type = 'email';

    public function clean($value)
    {
        $value = parent::clean($value);

        if ('' !== $value) {
            $value = strip_tags($value);
            $value = htmlentities($value, \ENT_QUOTES, 'utf-8');
            $value = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $value); // remplace les accents
            $value = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $value); // pour les ligatures e.g. '&oelig;'
            $value = preg_replace('#&[^;]+;#', '', $value); // supprime les autres caractères
            $value = trim($value);
            $value = html_entity_decode($value);

            if ('' !== $value) {
                $value = strtolower($value);

                if (false === strpos($value, '@') || !filter_var($value, \FILTER_VALIDATE_EMAIL)) {
                    return $value;
                }

                list($username, $domain) = explode('@', $value, 2);

                $username = preg_replace("/[^a-z0-9._+-]/", "", $username);
                $domain = preg_replace("/[^a-z0-9.-]/", "", $domain);

                $value = $username . '@' . $domain;
            }
        }

        return $value;
    }
}
