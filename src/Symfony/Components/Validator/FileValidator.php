<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Components\Validator;

use Symfony\Components\Form\UploadedFile;

/**
 * FileValidator validates an uploaded file.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: FileValidator.php 165 2010-01-24 21:06:37Z flo $
 */
class FileValidator extends BaseValidator
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max_size:             The maximum file size in bytes (cannot exceed upload_max_filesize in php.ini)
   *  * mime_types:           Allowed mime types array or category (available categories: web_images)
   *  * mime_type_guessers:   An array of mime type guesser PHP callables (must return the mime type or null)
   *  * mime_categories:      An array of mime type categories (web_images is defined by default)
   *  * path:                 The path where to save the file - as used by the ValidatedFile class (optional)
   *  * validated_file_class: Name of the class that manages the cleaned uploaded file (optional)
   *
   * There are 3 built-in mime type guessers:
   *
   *  * guessFromFileinfo:        Uses the finfo_open() function (from the Fileinfo PECL extension)
   *  * guessFromMimeContentType: Uses the mime_content_type() function (deprecated)
   *  * guessFromFileBinary:      Uses the file binary (only works on *nix system)
   *
   * Available error codes:
   *
   *  * max_size
   *  * mime_types
   *  * partial
   *  * no_tmp_dir
   *  * cant_write
   *  * extension
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see BaseValidator
   */
  protected function configure($options = array(), $messages = array())
  {
    if (!ini_get('file_uploads'))
    {
      throw new \LogicException(sprintf('Unable to use a file validator as "file_uploads" is disabled in your php.ini file (%s)', get_cfg_var('cfg_file_path')));
    }

    $this->addOption('max_size');
    $this->addOption('mime_types');

    $this->addMessage('max_size', 'File is too large (maximum is %max_size% bytes).');
    $this->addMessage('mime_types', 'Invalid mime type (%mime_type%).');
    $this->addMessage('partial', 'The uploaded file was only partially uploaded.');
    $this->addMessage('no_tmp_dir', 'Missing a temporary folder.');
    $this->addMessage('cant_write', 'Failed to write file to disk.');
    $this->addMessage('extension', 'File upload stopped by extension.');
  }

  public function validate($value)
  {
    $this->doValidate($value);
  }

  /**
   * Validates the file.
   *
   * The input value must be an instance of UploadedFile.
   *
   *  * tmp_name: The absolute temporary path to the file
   *  * name:     The original file name (optional)
   *  * type:     The file content type (optional)
   *  * error:    The error code (optional)
   *  * size:     The file size in bytes (optional)
   *
   * @param  mixed $value   The value that should be validated
   * @throws InvalidArgumentException when the argument is not of the
   *                                  expected type
   * @throws ValidatorError when the validation fails
   */
  protected function doValidate($value)
  {
    if (!($value instanceof UploadedFile))
    {
      throw new \InvalidArgumentException('Value must be an instance of UploadedFile.');
    }

    $path = $value->getPath();

    if (is_null($value->getOriginalName()))
    {
      $name = '';
    }
    else
    {
      $name = $value->getOriginalName();
    }

    if (is_null($value->getError()))
    {
      $error = UPLOAD_ERR_OK;
    }
    else
    {
      $error = $value->getError();
    }

    if (is_null($value->size()))
    {
      $size = filesize($value->getPath());
    }
    else
    {
      $size = $value->size();
    }

    if (is_null($value->getMimeType()))
    {
      $mimeType = 'application/octet-stream';
    }
    else
    {
      $mimeType = $value->getMimeType();
    }

    switch ($value->getError())
    {
      case UPLOAD_ERR_INI_SIZE:
        $max = ini_get('upload_max_filesize');
        if ($this->getOption('max_size'))
        {
          $max = min($max, $this->getOption('max_size'));
        }
        throw new ValidatorError($this->getMessage('max_size', array('max_size' => $max, 'size' => (int) $size)));
      case UPLOAD_ERR_FORM_SIZE:
        throw new ValidatorError($this->getMessage('max_size', array('max_size' => 0, 'size' => (int) $size)));
      case UPLOAD_ERR_PARTIAL:
        throw new ValidatorError($this->getMessage('partial'));
      case UPLOAD_ERR_NO_TMP_DIR:
        throw new ValidatorError($this->getMessage('no_tmp_dir'));
      case UPLOAD_ERR_CANT_WRITE:
        throw new ValidatorError($this->getMessage('cant_write'));
      case UPLOAD_ERR_EXTENSION:
        throw new ValidatorError($this->getMessage('extension'));
    }

    // check file size
    if ($this->hasOption('max_size') && $this->getOption('max_size') < (int) $size)
    {
      throw new ValidatorError($this->getMessage('max_size', array('max_size' => $this->getOption('max_size'), 'size' => (int) $size)));
    }

    // check mime type
    if ($this->hasOption('mime_types'))
    {
      if (!in_array($mimeType, array_map('strtolower', $this->getOption('mime_types'))))
      {
        throw new ValidatorError($this->getMessage('mime_types', array('mime_types' => implode(', ', $this->getOption('mime_types')), 'mime_type' => $mimeType)));
      }
    }

  }
}
