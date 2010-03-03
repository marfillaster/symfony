<?php

namespace Symfony\Components\Validator\Tests\MetaData;

require_once __DIR__.'/../TestInit.php';


use Symfony\Components\Validator\MetaData\ClassMetaData;
use Symfony\Components\Validator\MetaData\PropertyMetaData;
use Symfony\Components\Validator\Specification\ClassSpecification;
use Symfony\Components\Validator\Specification\PropertySpecification;
use Symfony\Components\Validator\Specification\ConstraintSpecification;


class ClassMetaDataTest extends \PHPUnit_Framework_TestCase
{
  protected function assertPropertiesEqual(array $properties, ClassMetaData $class)
  {
    $this->assertEquals(array_keys($properties), $class->getConstrainedProperties());

    foreach ($properties as $property => $metaData)
    {
      $this->assertEquals($metaData, $class->getPropertyMetaData($property));
    }
  }

  public function testPropertiesAreTakenFromSpecification()
  {
    $property = new PropertySpecification('Class', 'property');
    $spec = new ClassSpecification('Class', array($property));
    $class = new ClassMetaData('Class', $spec);

    $expected = array(
      'property' => new PropertyMetaData('Class', 'property', $property),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testParentPropertiesAreMerged()
  {
    $parentProperty = new PropertySpecification('Class', 'parentProperty');
    $spec = new ClassSpecification('Class', array($parentProperty));
    $parent = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'childProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, $parent);

    $expected = array(
      'childProperty' => new PropertyMetaData('SubClass', 'childProperty', $childProperty),
      'parentProperty' => new PropertyMetaData('Class', 'parentProperty', $parentProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testExistingPropertiesRemainUnchanged()
  {
    $parentProperty = new PropertySpecification('Class', 'existingProperty');
    $spec = new ClassSpecification('Class', array($parentProperty));
    $parent = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'existingProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, $parent);

    $expected = array(
      'existingProperty' => new PropertyMetaData('SubClass', 'existingProperty', $childProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testInterfacePropertiesAreMerged()
  {
    $interfaceProperty = new PropertySpecification('Class', 'interfaceProperty');
    $spec = new ClassSpecification('Class', array($interfaceProperty));
    $interface = new ClassMetaData('Class', $spec);

    $childProperty = new PropertySpecification('SubClass', 'childProperty');
    $spec = new ClassSpecification('SubClass', array($childProperty));
    $class = new ClassMetaData('SubClass', $spec, null, array($interface));

    $expected = array(
      'childProperty' => new PropertyMetaData('SubClass', 'childProperty', $childProperty),
      'interfaceProperty' => new PropertyMetaData('Class', 'interfaceProperty', $interfaceProperty),
    );

    $this->assertPropertiesEqual($expected, $class);
  }

  public function testInterfaceConstraintsAreMerged()
  {
    $interfaceConstraint = new ConstraintSpecification('InterfaceConstraint');
    $spec = new ClassSpecification('Interface', array(), array($interfaceConstraint));
    $interface = new ClassMetaData('Interface', $spec);

    $childConstraint = new ConstraintSpecification('ChildConstraint');
    $spec = new ClassSpecification('SubClass', array(), array($childConstraint));
    $element = new ClassMetaData('SubClass', $spec, null, array($interface));

    $expected = array(
      'ChildConstraint' => $childConstraint,
      'InterfaceConstraint' => $interfaceConstraint,
    );

    $this->assertEquals($expected, $element->getConstraints());
  }
}