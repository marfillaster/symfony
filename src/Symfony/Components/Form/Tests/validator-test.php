<?php

use Symfony\Foundation\ClassLoader;
use Symfony\Components\Validator\Constraints\Valid;
use Symfony\Components\Validator\Constraints\Choice;
use Symfony\Components\Validator\Constraints\Size;
use Symfony\Components\Validator\Engine\Validator;
use Symfony\Components\Validator\Engine\ConstraintValidatorFactory;
use Symfony\Components\Validator\Mapping\ClassMetadata;
use Symfony\Components\Validator\Mapping\ClassMetadataFactory;
use Symfony\Components\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Components\Form\Form;
use Symfony\Components\Form\Field;
use Symfony\Components\Form\FieldGroup;

require_once __DIR__.'/Symfony/Foundation/ClassLoader.php';

$classLoader = new ClassLoader();
$classLoader->registerNamespace('Symfony', __DIR__);
$classLoader->register();


class Entity
{
  /**
   * @Valid
   */
  public $firstName;

  /**
   * @Alpin\MyWhateverBundle\Constraints\Longitude(message=Please enter a longitude)
   */
  public $longitude;
  public $latitude;
  public $reference;

  public static function loadMetadata(ClassMetadata $metadata)
  {
    $metadata->addPropertyConstraint('reference', new Valid());
    $metadata->addPropertyConstraint('longitude', new Choice(array('groups' => 'Strict')));
    $metadata->addPropertyConstraint('latitude', new Size(array('min' => 10)));
    $metadata->addGetterConstraint('latitude', new Size(array('max' => 20)));
    $metadata->addPropertyConstraint('latitude', new Size(array('max' => 1, 'groups' => 'Normal')));
  }

  public function getLatitude()
  {
    return $this->latitude + 10;
  }
}

class SubEntity extends Entity
{
  public $property;

  public static function loadMetadata(ClassMetadata $metadata)
  {
    $metadata->addPropertyConstraint('property', new Size());
  }
}

class MyField extends Field
{
  public static function loadMetadata(ClassMetadata $metadata)
  {
    $metadata->addPropertyConstraint('displayedData', new Size());
  }
}

class EntityForm extends Form
{
  public static function loadMetadata(ClassMetadata $metadata)
  {
    $metadata->addPropertyConstraint('data', new Valid());
    $metadata->addPropertyConstraint('iterator', new Valid());
  }

  protected function configure()
  {
    $this->add(new Field('firstName'));

    $group = new FieldGroup('map');
    $group->add(new Field('longitude'));
    $group->add(new Field('latitude'));

    $this->merge($group);
  }
}

$metadataFactory = new ClassMetadataFactory(new StaticMethodLoader('loadMetadata'));
$validator = new Validator($metadataFactory, new ConstraintValidatorFactory());

$entity1 = new Entity();
$entity1->longitude = 6;
$entity1->latitude = 10;
$entity2 = new Entity();
$entity2->latitude = 5;
$entity1->reference = $entity2;
//$form = new EntityForm('entity', $entity, $validator);
//$form->bind(array('firstName' => 'Bernhard', 'map' => array('longitude' => 3, 'latitude' => 3)));

var_dump($validator->validate($entity1));
//var_dump($form->getErrors());
//var_dump($form['firstName']->getErrors());
//var_dump($form['map']['longitude']->getErrors());
//var_dump($form['map']['latitude']->getErrors());
