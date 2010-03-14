<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;

class PhpLoader extends AbstractFileLoader
{
  protected $extension = '.val.php';

  protected function parseClassMetadata($file, ClassMetadata $metadata)
  {
    require $file;
  }

  protected function parseGroupMetadata($file, GroupMetadata $metadata)
  {
    require $file;
  }
}