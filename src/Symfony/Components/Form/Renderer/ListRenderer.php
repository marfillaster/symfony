<?php

namespace Symfony\Components\Form\Renderer;

use Symfony\Components\Form\FieldInterface;
use Symfony\Components\Form\Field\ChoiceField;

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Renders a field group as HTML list
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class ListRenderer extends BaseRenderer
{
  /**
   * {@inheritDoc}
   */
  public function render(FieldInterface $group, array $attributes = array())
  {
    $html = '';


    foreach ($group as $field)
    {
      $html .= $field->renderErrors()."\n";
      $html .= $field->render()."\n";
    }

    return $html;
  }
}
