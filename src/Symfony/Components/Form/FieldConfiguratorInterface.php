<?php

namespace Symfony\Components\Form;

interface FieldConfiguratorInterface
{
  public function initialize($object);

  public function getClass($fieldName);

  public function getOptions($fieldName);

  public function isRequired($fieldName);
}