<?php

namespace Symfony\Components\Form\Renderer;

use Symfony\Components\Form\FieldInterface;

/**
 * Renders a given form field.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
interface RendererInterface
{
  /**
   * Returns the textual representation of the given field.
   *
   * @param  FieldInterface $field  The form field
   * @param  array $attributes          The attributes to include in the
   *                                    rendered output
   * @return string                     The rendered output
   * @throws InvalidArgumentException   If the $field is not instance of the
   *                                    expected class
   */
  public function render(FieldInterface $field, array $attributes = array());

  /**
   * Sets the charset used by the renderer.
   *
   * @param string $charset
   */
  public function setCharset($charset);

  /**
   * Returns true if the rendered tag is hidden.
   *
   * @return boolean  Whether the renderer outputs a hidden tag
   */
  public function isHidden();

  /**
   * Returns true if the rendered tag requires the form to be multipart
   *
   * @return boolean  Whether the rendered tag requires a multipart form
   */
  public function needsMultipartForm();
}