<?php

namespace Symfony\Components\Templating\Helper;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Helper is the base class for all helper classes.
 *
 * @package    Symfony
 * @subpackage Components_Templating
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
abstract class Helper implements HelperInterface
{
    protected $charset = 'UTF-8';

    /**
     * Sets the default charset.
     *
     * @param string $charset The charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }
}
