<?php

/*
 * This file bootstraps the test environment.
 */
namespace Symfony\Components\Form\Tests;

use Symfony\Components\Form\FormFieldInterface;
use Symfony\Components\Form\Renderer\RendererInterface;
use Symfony\Components\Validator\ValidatorInterface;
use Symfony\Components\ValueTransformer\ValueTransformerInterface;

use Symfony\Components\I18N\Localizable;
use Symfony\Components\I18N\Translatable;
use Symfony\Components\I18N\TranslatorInterface;

if (!isset($_SERVER['SYMFONY']))
{
  throw new \RuntimeException(
<<<EOF
Please set the environment variable SYMFONY to point to the Symfony 2 src/ directory.
On Unix, you can use the command
  export SYMFONY=/path/to/symfony/src
On Windows, you can use the command
  set SYMFONY=\path\to\symfony\src

EOF
  );
}

require_once $_SERVER['SYMFONY'] . '/Symfony/Tests/TestInit.php';


interface LocalizableRenderer extends RendererInterface, Localizable {}
interface TranslatableRenderer extends RendererInterface, Translatable {}
interface LocalizableValidator extends ValidatorInterface, Localizable {}
interface TranslatableValidator extends ValidatorInterface, Translatable {}
interface LocalizableValueTransformer extends ValueTransformerInterface, Localizable {}
interface TranslatableValueTransformer extends ValueTransformerInterface, Translatable {}
interface LocalizableField extends FormFieldInterface, Localizable {}
interface TranslatableField extends FormFieldInterface, Translatable {}

