<?php

namespace Symfony\Components\Form\Renderer;

use Symfony\Components\Form\FieldInterface;

/**
 * BaseRenderer is the base class for all renderers.
 */
abstract class BaseRenderer implements RendererInterface
{
  protected
    $options         = array(),
    $attributes      = array(),
    $knownOptions    = array(),
    $requiredOptions = array(),
    $charset         = 'UTF-8';

  protected static
    $xhtml   = true;

  /**
   * Constructor.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @throws \InvalidArgumentException when a option is not supported
   * @throws \RuntimeException         when a required option is not given
   */
  public function __construct(array $options = array(), array $attributes = array())
  {
    $this->options = $options;
    $this->attributes = $attributes;

    $this->configure();

    // check option names
    if ($diff = array_diff_key($this->options, $this->knownOptions))
    {
      throw new \InvalidArgumentException(sprintf('%s does not support the following options: \'%s\'.', get_class($this), implode('\', \'', array_keys($diff))));
    }

    // check required options
    if ($diff = array_diff_key($this->requiredOptions, $this->options))
    {
      throw new \RuntimeException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('\', \'', array_keys($diff))));
    }
  }

  /**
   * Configures the current renderer.
   */
  protected function configure()
  {
  }

  /**
   * Gets an option value.
   *
   * @param  string $name  The option name
    *
   * @return mixed  The option value
   */
  protected function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return BaseRenderer The current renderer instance
   */
  protected function addOption($name, $value = null)
  {
    $this->knownOptions[$name] = true;

    if (!array_key_exists($name, $this->options))
    {
      $this->options[$name] = $value;
    }

    return $this;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   *
   * @return BaseRenderer The current renderer instance
   *
   * @throws \InvalidArgumentException when a option is not supported
   */
  protected function setOption($name, $value)
  {
    if (!in_array($name, array_merge(array_keys($this->options), $this->requiredOptions)))
    {
      throw new \InvalidArgumentException(sprintf('%s does not support the following option: \'%s\'.', get_class($this), $name));
    }

    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  protected function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Adds a required option.
   *
   * @param string $name  The option name
   *
   * @return BaseRenderer The current renderer instance
   */
  protected function addRequiredOption($name)
  {
    $this->knownOptions[$name] = true;
    $this->requiredOptions[$name] = true;

    return $this;
  }

  /**
   * Sets a default HTML attribute.
   *
   * @param string $name   The attribute name
   * @param string $value  The attribute value
   *
   * @return BaseRenderer The current renderer instance
   */
  protected function setAttribute($name, $value)
  {
    $this->attributes[$name] = $value;

    return $this;
  }

  /**
   * Returns the HTML attribute value for a given attribute name.
   *
   * @param  string $name  The attribute name.
   *
   * @return string The attribute value, or null if the attribute does not exist
   */
  protected function getAttribute($name)
  {
    return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
  }

  /**
   * Gets the stylesheet paths associated with the renderer.
   *
   * The array keys are files and values are the media names (separated by a ,):
   *
   *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array();
  }

  /**
   * Gets the JavaScript paths associated with the renderer.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array();
  }

  /**
   * {@inheritDoc}
   */
  public function setCharset($charset)
  {
    $this->charset = $charset;
  }

  /**
   * Returns the charset to use when render.
   *
   * @return string The charset (defaults to UTF-8)
   */
  protected function getCharset()
  {
    return $this->charset;
  }

  /**
   * Sets the XHTML generation flag.
   *
   * @param bool $boolean  true if renderers must be generated as XHTML, false otherwise
   */
  static public function setXhtml($boolean)
  {
    self::$xhtml = (boolean) $boolean;
  }

  /**
   * Returns whether to generate XHTML tags or not.
   *
   * @return bool true if renderers must be generated as XHTML, false otherwise
   */
  static public function isXhtml()
  {
    return self::$xhtml;
  }

  /**
   * Renders a HTML tag.
   *
   * @param string $tag         The tag name
   * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   *
   * @param string An HTML tag string
   */
  protected function renderTag($tag, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    return sprintf('<%s%s%s', $tag, $this->attributesToHtml($attributes), self::$xhtml ? ' />' : (strtolower($tag) == 'input' ? '>' : sprintf('></%s>', $tag)));
  }

  /**
   * Renders a HTML content tag.
   *
   * @param string $tag         The tag name
   * @param string $content     The content of the tag
   * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   *
   * @param string An HTML tag string
   */
  protected function renderContentTag($tag, $content = null, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    return sprintf('<%s%s>%s</%s>', $tag, $this->attributesToHtml($attributes), $content, $tag);
  }

  /**
   * Escapes a string.
   *
   * @param  string $value  string to escape
   * @return string escaped string
   */
  protected function escapeOnce($value)
  {
    return $this->fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, $this->getCharset()));
  }

  /**
   * Fixes double escaped strings.
   *
   * @param  string $escaped  string to fix
   * @return string single escaped string
   */
  protected function fixDoubleEscape($escaped)
  {
    return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
  }

  /**
   * Generates a two chars range
   *
   * TODO: Move somewhere else
   *
   * @param  int  $start
   * @param  int  $stop
   * @return array
   */
  protected static function generateTwoCharsRange($start, $stop)
  {
    $results = array();
    for ($i = $start; $i <= $stop; $i++)
    {
      $results[$i] = sprintf('%02d', $i);
    }
    return $results;
  }

  /**
   * Converts an array of attributes to its HTML representation.
   *
   * @param  array  $attributes An array of attributes
   *
   * @return string The HTML representation of the HTML attribute array.
   */
  private function attributesToHtml(array $attributes)
  {
    $attributes = array_merge($this->attributes, $attributes);

    return implode('', array_map(array($this, 'attributesToHtmlCallback'), array_keys($attributes), array_values($attributes)));
  }

  /**
   * Prepares an attribute key and value for HTML representation.
   *
   * It removes empty attributes, except for the value one.
   *
   * @param  string $k  The attribute key
   * @param  string $v  The attribute value
   *
   * @return string The HTML representation of the HTML key attribute pair.
   */
  private function attributesToHtmlCallback($k, $v)
  {
    return false === $v || null === $v || ('' === $v && 'value' != $k) ? '' : sprintf(' %s="%s"', $k, $this->escapeOnce($v));
  }

  /**
   * {@inheritDoc}
   */
  public function isHidden()
  {
    return false;
  }

  /**
   * {@inheritDoc}
   */
  public function needsMultipartForm()
  {
    return false;
  }
}