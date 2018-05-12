<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Set form i18n
 * 
 * @param   array   $strings   Key/value pairs of translations
 * @param   bool    $default   Default translation if not set (@deprecated)
 * @return  void
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function FormTranslate($strings, $default=false)
{
  \JDZ\Form\FormHelper::setTranslations($strings);
}

/**
 * Set the fields namespace
 * 
 * @param   string    $name   Namespace
 * @return  void
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function FormFieldNS($ns)
{
  \JDZ\Form\Field\Field::setNamespace($ns);
}

/**
 * Set the rules namespace
 * 
 * @param   string    $name   Namespace
 * @return  void
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function FormRuleNS($ns)
{
  \JDZ\Form\Rule\Rule::setNamespace($ns);
}
