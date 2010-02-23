<?php

namespace Symfony\Components\Form;

/**
 * A form field containing a list of choices.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
interface ChoiceFieldInterface extends FormFieldInterface
{
  /**
   * The preferred choices.
   *
   * If not empty, these choices are usually displayed seperated from normal
   * choices.
   *
   * @return array  The preferred choices
   */
  public function getPreferredChoices();

  /**
   * The available choices.
   *
   * The result of this method should not contain any entries returned by
   * getPreferredChoices().
   *
   * @return array  The choices
   */
  public function getChoices();
}
