<?php

namespace Symfony\Components\Validator\Exception;

class MissingOptionsException extends ValidatorException
{
  private $missingOptions;
  private $object;

  public function __construct($object, array $missingOptions)
  {
    parent::__construct(sprintf('%s requires the option "%s"', get_class($object), implode('", "', $missingOptions)));

    $this->missingOptions = $missingOptions;
    $this->object = $object;
  }

  public function getObject()
  {
    return $this->object;
  }

  public function getMissingOptions()
  {
    return $this->missingOptions;
  }
}