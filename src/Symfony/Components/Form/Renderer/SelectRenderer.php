<?php

namespace Symfony\Components\Form\Renderer;

use Symfony\Components\Form\FieldInterface;
use Symfony\Components\Form\ChoiceFieldInterface;
use Symfony\Components\Form\Renderer\BaseRenderer;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * InputRenderer represents an HTML input tag.
 */
class SelectRenderer extends BaseRenderer
{
  /**
   * {@inheritDoc}
   */
  protected function configure()
  {
    $this->addOption('multiple', false);
  }

  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $field, array $attributes = array())
  {
    if (!$field instanceOf ChoiceFieldInterface)
    {
      throw new \InvalidArgumentException(sprintf('%s expected argument 1 of render() to be of class ChoiceFieldInterface', get_class($this)));
    }

    $attributes['id'] = $field->getId();
    $attributes['name'] = $field->getName();

    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';
    }

    $selected = array_flip(array_map('strval', (array)$field->getDisplayedData()));
    $options = $this->getOptionsForSelect($selected, $field->getChoices());

    return $this->renderContentTag('select', "\n".implode("\n", $options)."\n", $attributes);
  }

  /**
   * Returns an array of option tags for the choice field
   *
   * @param  ChoiceFieldInterface $field
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($selected, $choices)
  {
    $options = array();

    foreach ($choices as $key => $option)
    {
      if (is_array($option))
      {
        $options[] = $this->renderContentTag(
          'optgroup',
          "\n".implode("\n", $this->getOptionsForSelect($selected, $option))."\n",
          array('label' => self::escapeOnce($key))
        );
      }
      else
      {
        $attributes = array('value' => self::escapeOnce($key));

        if (isset($selected[strval($key)]))
        {
          $attributes['selected'] = 'selected';
        }

        $options[] = $this->renderContentTag(
          'option',
          $this->escapeOnce($option),
          $attributes
        );
      }
    }

    return $options;
  }

  /**
   * Helper that returns the textual representation for Date & Time Fields
   *
   * @param  FieldInterface $field  The form field
   * @param  array $options  Contains the choices for the parts of the pattern
   * @return string  The rendered output
   */
  protected function renderPattern(FieldInterface $field, $data)
  {
    $emptyValues = $this->getOption('empty_values');

    $id = $field->getId();
    $name = $field->getName();

    $pattern = $field->getPattern();

    $select = array();
    foreach($field as $part => $subField)
    {
      if (strpos($pattern, '%'.$part.'%') !== false)
      {
        $choices = $subField->getChoices();

        if ($this->getOption('can_be_empty'))
        {
          $choices = array_merge(array('' => isset($emptyValues[$part]) ? $emptyValues[$part] : ''), $choices);
        }

        $selection = isset($data[$part]) ? $data[$part] : '';
        if (preg_match('~^0[0-9]$~', $selection))
        {
          $selection = (int) $data[$part];
        }

        $optionTags = $this->getOptionsForSelect($selection, $choices);
        $select['%'.$part.'%'] = $this->renderContentField('select', implode("", $optionTags), $field, array('name' => $name . '['.$part.']', 'id' => $id . '_'.$part));
      }
    }

    return strtr($pattern, $select);
  }
}
