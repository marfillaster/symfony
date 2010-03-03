<?php

namespace Symfony\Components\Validator;

interface MetaDataInterface
{
  public function getClassMetaData($class);

  public function getGroupMetaData($interface);
}