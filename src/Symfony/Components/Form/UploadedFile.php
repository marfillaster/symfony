<?php

namespace Symfony\Components\Form;

/**
 * A file uploaded through a form.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @author     Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class UploadedFile
{
  
  protected
      $tmpName,
      $name,
      $type,
      $size,
      $error;      
  
  /**
   * Accepts the information of the uploaded file as provided by the PHP
   * global $_FILES.
   *
   * @param string  $tmpName  The full temporary path to the file
   * @param string  $name     The original file name
   * @param string  $type     The type of the file as provided by PHP
   * @param integer $size     The file size
   * @param string  $error    The error constant of the upload. Should be
   *                          one of PHP's UPLOAD_XXX constants.
   */
  public function __construct($tmpName, $name, $type, $size, $error)
  {
    $this->tmpName  = $tmpName;
    $this->name     = $name;
    $this->type     = $type;
    $this->size     = $size;
    $this->error    = $error;
  }

  /**
   * Returns the absolute path to the file.
   *
   * @returns string  The file path
   */
  public function getPath()
  {
    return $this->tmpName;
  }

  /**
   * Returns the original file name including its extension.
   *
   * @returns string  The file name
   */
  public function getOriginalName()
  {
    return $this->name;
  }

  /**
   * Returns the mime type of the file.
   *
   * The mime type is guessed using the functions finfo(), mime_content_type()
   * and the system binary "file" (in this order), depending on which of those
   * is available on the current operating system.
   *
   * @returns string  The guessed mime type, e.g. "application/pdf"
   */
  public function getMimeType()
  {
    $mime = $this->guessFromFileinfo($this->getPath());
    if (!is_null($mime))
    {
      return $mime;
    }
    $mime = $this->guessFromMimeContentType($this->getPath());
    if (!is_null($mime))
    {
      return $mime;
    }
    $mime = $this->guessFromFileBinary($this->getPath());
    if (!is_null($mime))
    {
      return $mime;
    }
    return $this->type;
  }

  /**
   * Returns the upload error.
   *
   * If the upload was successful, the constant UPLOAD_ERR_OK is returned.
   * Otherwise one of the other UPLOAD_ERR_XXX constants is returned.
   *
   * @returns integer  The upload error
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   * Returns the size of this file
   *
   * @return integer  The file size in bytes
   */
  public function size()
  {
    return $this->size;
  }
  
  /**
   * Guess the file mime type with PECL Fileinfo extension
   *
   * @param  string $file  The absolute path of a file
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromFileinfo($file)
  {
    if (!function_exists('finfo_open') || !is_readable($file))
    {
      return null;
    }

    if (!$finfo = new \finfo(FILEINFO_MIME))
    {
      return null;
    }

    $type = $finfo->file($file);

    // remove charset (added as of PHP 5.3)
    if (false !== $pos = strpos($type, ';'))
    {
      $type = substr($type, 0, $pos);
    }

    return $type;
  }

  /**
   * Guess the file mime type with mime_content_type function (deprecated)
   *
   * @param  string $file  The absolute path of a file
    *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromMimeContentType($file)
  {
    if (!function_exists('mime_content_type') || !is_readable($file))
    {
      return null;
    }

    $type = mime_content_type($file);
    // remove charset (added as of PHP 5.3)
    if (false !== $pos = strpos($type, ';'))
    {
      $type = substr($type, 0, $pos);
    }

    return $type;
  }

  /**
   * Guess the file mime type with the file binary (only available on *nix)
   *
   * @param  string $file  The absolute path of a file
   *
   * @return string The mime type of the file (null if not guessable)
   */
  protected function guessFromFileBinary($file)
  {
    ob_start();
    //need to use --mime instead of -i. see #6641
    passthru(sprintf('file -b --mime %s 2>/dev/null', escapeshellarg($file)), $return);
    if ($return > 0)
    {
      ob_end_clean();

      return null;
    }
    $type = trim(ob_get_clean());

    if (!preg_match('#^([a-z0-9\-]+/[a-z0-9\-]+)#i', $type, $match))
    {
      // it's not a type, but an error message
      return null;
    }

    return $match[1];
  }
}