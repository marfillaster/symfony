<?php

namespace Symfony\Components\Form;

use Symfony\Components\Form\Renderer\Html\InputHiddenRenderer;

use Symfony\Components\Validator\BaseValidator;
use Symfony\Components\Validator\ValidatorSchema;
use Symfony\Components\Validator\ValidatorSchemaForEach;
use Symfony\Components\Validator\ValidatorErrorSchema;
use Symfony\Components\Validator\CSRFTokenValidator;
use Symfony\Components\Validator\AndValidator;
use Symfony\Components\Validator\ValidatorInterface;

use Symfony\Components\Validator\Engine\PropertyPath;
use Symfony\Components\Validator\Engine\ConstraintViolation;

use Symfony\Components\I18N\TranslatorInterface;

use Symfony\Components\File\UploadedFile;


/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Form represents a form.
 *
 * A form is composed of a validator schema and a widget form schema.
 *
 * Form also takes care of CSRF protection by default.
 *
 * A CSRF secret can be any random string. If set to false, it disables the
 * CSRF protection, and if set to null, it forces the form to use the global
 * CSRF secret. If the global CSRF secret is also null, then a random one
 * is generated on the fly.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Form.php 245 2010-01-31 22:22:39Z flo $
 */
class Form extends FormFieldGroup
{
  protected static
    $defaultCSRFSecret = false,
    $CSRFFieldName     = '_csrf_token',
    $toStringException = null,
    $defaultLocale     = null,
    $defaultTranslator = null;

  public
    $CSRFSecret      = null;

  protected $validator = null;

  /**
   * Constructor.
   *
   * @param array  $defaults    An array of field default values
   * @param array  $options     An array of options
   * @param string $defaultCSRFSecret  A CSRF secret
   */
  public function __construct($name, $object, ValidatorInterface $validator, array $options = array())
  {
    $this->validator = $validator;

    parent::__construct($name, $options);

    $this->initialize($object);

    if (self::$defaultCSRFSecret !== false)
    {
      $this->enableCSRFProtection(self::$defaultCSRFSecret);
    }

    if (!is_null(self::$defaultLocale))
    {
      $this->setLocale(self::$defaultLocale);
    }

    if (!is_null(self::$defaultTranslator))
    {
      $this->setTranslator(self::$defaultTranslator);
    }
  }

  /**
   * Sets the default locale for newly created forms.
   *
   * @param string $defaultLocale
   */
  static public function setDefaultLocale($defaultLocale)
  {
    self::$defaultLocale = $defaultLocale;
  }

  /**
   * Returns the default locale for newly created forms.
   *
   * @return string
   */
  static public function getDefaultLocale()
  {
    return self::$defaultLocale;
  }

  /**
   * Sets the default translator for newly created forms.
   *
   * @param TranslatorInterface $defaultTranslator
   */
  static public function setDefaultTranslator(TranslatorInterface $defaultTranslator)
  {
    self::$defaultTranslator = $defaultTranslator;
  }

  /**
   * Returns the default translator for newly created forms.
   *
   * @return TranslatorInterface
   */
  static public function getDefaultTranslator()
  {
    return self::$defaultTranslator;
  }

  /**
   * Binds the form with values and files.
   *
   * This method is final because it is very easy to break a form when
   * overriding this method and adding logic that depends on $taintedFiles.
   * You should override doBind() instead where the uploaded files are
   * already merged into the data array.
   *
   * @param  array $taintedValues  The form data of the $_POST array
   * @param  array $taintedFiles   The form data of the $_FILES array
   * @return boolean               Whether the form is valid
   */
  final public function bind($taintedValues, array $taintedFiles = null)
  {
    if (!is_array($taintedValues))
    {
      throw new \InvalidArgumentException('The tainted values must be an array');
    }

    if (is_null($taintedFiles))
    {
      if ($this->isMultipart() && is_null($this->getParent()))
      {
        throw new \InvalidArgumentException('You must provide a files array for multipart forms');
      }

      $taintedFiles = array();
    }

    // check that post_max_size has not been reached
//    if (isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $this->getBytes(ini_get('post_max_size')))
//    {
//      $errorSchema->addError(new ValidatorError($this, 'post_max_size'));
//
//      throw $errorSchema;
//    }

    $this->doBind(self::deepArrayUnion(
      $taintedValues,
      self::convertFileInformation(self::fixPhpFilesArray($taintedFiles))
    ));
  }

  /**
   * Binds the form with the given data.
   *
   * @param  array $taintedData  The data to bind to the form
   * @return boolean             Whether the form is valid
   */
  protected function doBind(array $taintedData)
  {
    parent::bind($taintedData);

    $this->validate();
  }

  /**
   * Validates the form and distributes errors across the fieldsf
   */
  protected function validate()
  {
    if ($violations = $this->validator->validate($this))
    {
      foreach ($violations as $violation)
      {
        $path = $violation->getPropertyPath();
        $path->rewind();

        if ($path->current() == 'data')
        {
          $this->addDataError($this, $path, $violation);
        }
        else
        {
          $this->addFieldError($this, $path, $violation);
        }
      }
    }
  }

  /**
   * Recursively adds errors of the form fields to the fields
   *
   * Violations in the form fields usually have property paths like:
   *
   * <code>
   * iterator[firstName].data
   * iterator[firstName].displayedData
   * iterator[Address].iterator[street].displayedData
   * ...
   * </code>
   *
   * @param FormFieldInterface $field
   * @param PropertyPath $path
   * @param ConstraintViolation$violation
   */
  protected function addFieldError(FormFieldInterface $field, PropertyPath $path, ConstraintViolation $violation)
  {
    $path->next(); // jump to next iterator index
    $fieldName = $path->current();

    if ($path->valid() && $field instanceof FormFieldGroup && $field->has($fieldName))
    {
      $path->next(); // jump to next "iterator" (if exists)

      $this->addFieldError($field->get($fieldName), $path, $violation);
    }
    else
    {
      $field->addError($violation->getMessageTemplate(), $violation->getMessageParameters());
    }
  }

  /**
   * Recursively adds errors of the form data to the fields
   *
   * Violations in the form data usually have property paths like:
   *
   * <code>
   * data.firstName
   * data.Address.street
   * ...
   * </code>
   *
   * @param FormFieldInterface $field
   * @param PropertyPath $path
   * @param ConstraintViolation$violation
   */
  protected function addDataError(FormFieldInterface $field, PropertyPath $path, ConstraintViolation $violation)
  {
    $path->next(); // jump to next property name
    $fieldName = $path->current();

    if ($path->valid() && $field instanceof FormFieldGroup && $field->has($fieldName))
    {
      $this->addDataError($field->get($fieldName), $path, $violation);
    }
    else
    {
      $field->addError($violation->getMessageTemplate(), $violation->getMessageParameters());
    }
  }

  /**
   * Returns a string representation of the form.
   *
   * @return string A string representation of the form
   *
   * @see render()
   */
  public function __toString()
  {
    try
    {
      return $this->render();
    }
    catch (\Exception $e)
    {
      self::setToStringException($e);

      // we return a simple Exception message in case the form framework is used out of symfony.
      return 'Exception: '.$e->getMessage();
    }
  }

  /**
   * Gets the stylesheet paths associated with the form.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return $this->getWidget()->getStylesheets();
  }

  /**
   * Gets the JavaScript paths associated with the form.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return $this->getWidget()->getJavaScripts();
  }

  /**
   * Returns a CSRF token, given a secret.
   *
   * If you want to change the algorithm used to compute the token, you
   * can override this method.
   *
   * @param  string $secret The secret string to use (null to use the current secret)
   *
   * @return string A token string
   */
  protected function getCSRFToken()
  {
    return md5($this->CSRFSecret.session_id().get_class($this));
  }

  /**
   * @return true if this form is CSRF protected
   */
  public function isCSRFProtected()
  {
    return !is_null($this->CSRFSecret);
  }

  /**
   * Sets the CSRF field name.
   *
   * @param string $name The CSRF field name
   */
  static public function setCSRFFieldName($name)
  {
    self::$CSRFFieldName = $name;
  }

  /**
   * Gets the CSRF field name.
   *
   * @return string The CSRF field name
   */
  static public function getCSRFFieldName()
  {
    return self::$CSRFFieldName;
  }

  /**
   * Enables CSRF protection for this form.
   *
   * @param string $secret A secret to use when computing the CSRF token
   */
  public function enableCSRFProtection($secret = null)
  {
    if (is_null($secret))
    {
      $secret = md5(__FILE__.php_uname());
    }

    $this->CSRFSecret = $secret;
  }

  /**
   * Disables CSRF protection for this form.
   */
  public function disableCSRFProtection()
  {
    $this->CSRFSecret = false;
  }

  /**
   * Enables CSRF protection for all forms.
   *
   * The given secret will be used for all forms, except if you pass a secret in the constructor.
   * Even if a secret is automatically generated if you don't provide a secret, you're strongly advised
   * to provide one by yourself.
   *
   * @param string $secret A secret to use when computing the CSRF token
   */
  static public function enableDefaultCSRFProtection($secret = null)
  {
    self::$defaultCSRFSecret = $secret;
  }

  /**
   * Disables CSRF protection for all forms.
   */
  static public function disableDefaultCSRFProtection()
  {
    self::$defaultCSRFSecret = false;
  }

  /**
   * Renders the form tag.
   *
   * This methods only renders the opening form tag.
   * You need to close it after the form rendering.
   *
   * This method takes into account the multipart widgets
   * and converts PUT and DELETE methods to a hidden field
   * for later processing.
   *
   * @param  string $url         The URL for the action
   * @param  array  $attributes  An array of HTML attributes
   *
   * @return string An HTML representation of the opening form tag
   */
  public function renderFormTag($url, array $attributes = array())
  {
    $attributes['action'] = $url;
    $attributes['method'] = isset($attributes['method']) ? strtolower($attributes['method']) : 'post';
    if ($this->isMultipart())
    {
      $attributes['enctype'] = 'multipart/form-data';
    }

    $html = '';
    if (!in_array($attributes['method'], array('get', 'post')))
    {
      $html = $this->getWidget()->renderTag('input', array('type' => 'hidden', 'name' => '_method', 'value' => $attributes['method'], 'id' => false));
      $attributes['method'] = 'post';
    }

    return sprintf('<form%s>', $this->getWidget()->attributesToHtml($attributes)).$html;
  }

  /**
   * Returns true if a form thrown an exception in the __toString() method
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @return boolean
   */
  static public function hasToStringException()
  {
    return null !== self::$toStringException;
  }

  /**
   * Gets the exception if one was thrown in the __toString() method.
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @return Exception
   */
  static public function getToStringException()
  {
    return self::$toStringException;
  }

  /**
   * Sets an exception thrown by the __toString() method.
   *
   * This is a hack needed because PHP does not allow to throw exceptions in __toString() magic method.
   *
   * @param Exception $e The exception thrown by __toString()
   */
  static public function setToStringException(Exception $e)
  {
    if (null === self::$toStringException)
    {
      self::$toStringException = $e;
    }
  }

  /**
   * Merges two arrays without reindexing numeric keys.
   *
   * @param array $array1 An array to merge
   * @param array $array2 An array to merge
   *
   * @return array The merged array
   */
  static protected function deepArrayUnion($array1, $array2)
  {
    foreach ($array2 as $key => $value)
    {
      if (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
      {
        $array1[$key] = self::deepArrayUnion($array1[$key], $value);
      }
      else
      {
        $array1[$key] = $value;
      }
    }

    return $array1;
  }

  /**
   * Fixes a malformed PHP $_FILES array.
   *
   * PHP has a bug that the format of the $_FILES array differs, depending on
   * whether the uploaded file fields had normal field names or array-like
   * field names ("normal" vs. "parent[child]").
   *
   * This method fixes the array to look like the "normal" $_FILES array.
   *
   * @param  array $data
   * @return array
   */
  static protected function fixPhpFilesArray(array $data)
  {
    $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
    $keys = array_keys($data);
    sort($keys);

    $files = $data;

    if ($fileKeys == $keys && isset($data['name']) && is_array($data['name']))
    {
      foreach ($fileKeys as $k)
      {
        unset($files[$k]);
      }

      foreach (array_keys($data['name']) as $key)
      {
        $files[$key] = self::fixPhpFilesArray(array(
          'error'    => $data['error'][$key],
          'name'     => $data['name'][$key],
          'type'     => $data['type'][$key],
          'tmp_name' => $data['tmp_name'][$key],
          'size'     => $data['size'][$key],
        ));
      }
    }

    return $files;
  }

  /**
   * Converts uploaded files to instances of clsas UploadedFile.
   *
   * @param  array $files A (multi-dimensional) array of uploaded file information
   * @return array A (multi-dimensional) array of UploadedFile instances
   */
  static protected function convertFileInformation(array $files)
  {
    $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');

    foreach ($files as $key => $data)
    {
      if (is_array($data))
      {
        $keys = array_keys($data);
        sort($keys);

        if ($keys == $fileKeys)
        {
          $files[$key] = new UploadedFile($data['tmp_name'], $data['name'], $data['type'], $data['size'], $data['error']);
        }
        else
        {
          $files[$key] = self::convertFileInformation($data);
        }
      }
    }

    return $files;
  }
}
