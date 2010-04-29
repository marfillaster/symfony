<?php

namespace Symfony\Tests\Components\Validator\Fixtures;

require_once __DIR__.'/EntityParent.php';
require_once __DIR__.'/EntityInterface.php';

class Entity extends EntityParent implements EntityInterface
{
  public $firstName;
  public $lastName;

  public function getFirstName()
  {
    return $this->firstName;
  }
}