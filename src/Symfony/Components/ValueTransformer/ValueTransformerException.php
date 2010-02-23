<?php

namespace Symfony\Components\ValueTransformer;

/**
 * Indicates a value transformation error.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class ValueTransformerException extends \RuntimeException
{
  /**
   * Constructor.
   *
  * @param string $message The description of the transformation error.
  */
  public function __construct($message)
  {
    parent::__construct($message);
  }
}
