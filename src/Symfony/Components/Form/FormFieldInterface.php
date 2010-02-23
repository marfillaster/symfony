<?php

namespace Symfony\Components\Form;

/**
 * A form field that can be embedded in a form.
 *
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id: FormFieldInterface.php 247 2010-02-01 09:24:55Z bernhard $
 */
interface FormFieldInterface
{
  /**
   * Clones this field.
   */
  public function __clone();

  /**
   * Sets the parent field.
   *
   * @param FormFieldInterface $parent  The parent field
   */
  public function setParent(FormFieldInterface $parent);

  /**
   * Returns the key by which the field is identified in field groups.
   *
   * @return string  The key of the field.
   */
  public function getKey();

  /**
   * Returns the name of the field.
   *
   * @return string  When the field has no parent, the name is equal to its
   *                 key. If the field has a parent, the name is composed of
   *                 the parent's name and the field's key, where the field's
   *                 key is wrapped in squared brackets
   *                 (e.g. "parent_name[field_key]")
   */
  public function getName();

  /**
   * Returns the ID of the field.
   *
   * @return string  The ID of a field is equal to its name, where all
   *                 sequences of squared brackets are replaced by a single
   *                 underscore (e.g. if the name is "parent_name[field_key]",
   *                 the ID is "parent_name_field_key").
   */
  public function getId();

  /**
   * Sets the field's default data.
   *
   * @param mixed $data
   */
  public function setDefault($data);

  /**
   * Returns the normalized data of the field.
   *
   * @return mixed  When the field is not bound, the default data is returned.
   *                When the field is bound, the normalized bound data is
   *                returned if the field is valid, null otherwise.
   */
  public function getData();

  /**
   * Returns the data of the field as it is displayed to the user.
   *
   * @return string|array  When the field is not bound, the transformed
   *                       default data is returned. When the field is bound,
   *                       the bound data is returned.
   */
  public function getDisplayedData();

  /**
   * Binds POST data to the field, transforms and validates it.
   *
   * @param  string|array $taintedData  The POST data
   * @return boolean                    Whether the form is valid
   * @throws InvalidConfigurationException when the field is not configured
   *                                       correctly
   */
  public function bind($taintedData);

  /**
   * Processes the business logic of the field.
   *
   * @throws NotBoundException when the field is not yet bound
   * @throws NotValidException when the field is invalid
   * @throws InvalidConfigurationException when the field is not configured
   *                                       correctly
   */
  public function process();

  /**
   * Renders this field.
   *
   * @param  array $attributes  The attributes to include in the rendered
   *                            output
   * @return string             The rendered output of this field
   */
  public function render(array $attributes = array());

  /**
   * Returns whether the field is bound.
   *
   * @return boolean
   */
  public function isBound();

  /**
   * Returns whether the field is valid.
   *
   * @return boolean
   */
  public function isValid();

  /**
   * Returns whether the field is processed.
   *
   * @return boolean
   */
  public function isProcessed();

  /**
   * Returns whether the field requires a multipart form.
   *
   * @return boolean
   */
  public function isMultipart();

  /**
   * Returns whether the field is required to be filled out.
   *
   * If the field has a parent and the parent is not required, this method
   * will always return false. Otherwise the value set with setRequired()
   * is returned.
   *
   * @return boolean
   */
  public function isRequired();

  /**
   * Sets whether this field is required to be filled out when submitted.
   *
   * @param boolean $required
   */
  public function setRequired($required);

  /**
   * Sets the charset of the field.
   *
   * @param string $charset
   */
  public function setCharset($charset);
}