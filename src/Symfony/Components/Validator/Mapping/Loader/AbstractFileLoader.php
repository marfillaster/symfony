<?php

namespace Symfony\Components\Validator\Mapping\Loader;

use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\GroupMetadata;

abstract class AbstractFileLoader implements LoaderInterface
{
  protected $paths;
  protected $extension;

  public function __construct(array $paths)
  {
    $this->paths = $paths;
  }

  protected function findMappingFile($class)
  {
    $fileName = str_replace('\\', '.', $class) . $this->extension;

    foreach ($this->paths as $path)
    {
      if (file_exists($path . DIRECTORY_SEPARATOR . $fileName))
      {
        return $path . DIRECTORY_SEPARATOR . $fileName;
      }
    }

    return false;
  }

  public function loadClassMetadata(ClassMetadata $metadata)
  {
    $class = $metadata->getClassName();

    if ($fileName = $this->findMappingFile($class))
    {
      $this->parseClassMetadata($fileName, $metadata);
    }
  }

  abstract protected function parseClassMetadata($file, ClassMetadata $metadata);

  public function loadGroupMetadata(GroupMetadata $metadata)
  {
    $class = $metadata->getClassName();

    if ($fileName = $this->findMappingFile($class))
    {
      $this->parseGroupMetadata($fileName, $metadata);
    }
  }

  abstract protected function parseGroupMetadata($file, GroupMetadata $metadata);

}