<?php

namespace Symfony\Components\Validator\Exception;

class UnknownOptionsException extends ValidatorException
{
  private $unknownOptions;
  private $object;

  public function __construct($object, array $unknownOptions)
  {
    parent::__construct(sprintf('%s does not accept the option "%s"', get_class($object), implode('", "', $unknownOptions)));

    $this->unknownOptions = $unknownOptions;
    $this->object = $object;
  }

  public function getObject()
  {
    return $this->object;
  }

  public function getUnknownOptions()
  {
    return $this->unknownOptions;
  }
}