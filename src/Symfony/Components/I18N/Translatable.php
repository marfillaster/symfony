<?php

namespace Symfony\Components\I18N;

/**
 * Marks classes that you can inject a translator into.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
interface Translatable
{
  /**
   * Sets the translator unit of the class.
   *
   * @param TranslatorInterface $translator
   */
  public function setTranslator(TranslatorInterface $translator);
}
