<?php

namespace Symfony\Components\Validator\Exception;

class MissingAttributesException extends ValidatorException
{
  private $attributes;

  public function __construct($message, array $attributes)
  {
    parent::__construct($message);

    $this->attributes = $attributes;
  }

  public function getAttributes()
  {
    return $this->attributes;
  }
}