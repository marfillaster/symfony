<?php

namespace Symfony\Components\Validator\Exception;

class InvalidAttributesException extends ValidatorException
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