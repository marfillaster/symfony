<?php

namespace Symfony\Components\Validator;

class ConstraintViolation
{
  protected $messageTemplate;
  protected $messageParameters;
  protected $root;
  protected $propertyPath;
  protected $invalidValue;

  public function __construct($messageTemplate, array $messageParameters, $root, $propertyPath, $invalidValue)
  {
    $this->messageTemplate = $messageTemplate;
    $this->messageParameters = $messageParameters;
    $this->root = $root;
    $this->propertyPath = $propertyPath;
    $this->invalidValue = $invalidValue;
  }

  // how to interpolate?
  public function getMessage()
  {
    return $this->message;
  }

  public function getMessageTemplate()
  {
    return $this->messageTemplate;
  }

  public function getMessageParameters()
  {
    return $this->messageParameters;
  }

  public function getRoot()
  {
    return $this->root;
  }

  public function getPropertyPath()
  {
    return $this->propertyPath;
  }

  public function getInvalidValue()
  {
    return $this->invalidValue;
  }
}