<?php

namespace Symfony\Components\Finder\Iterator;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * SizeRangeFilterIterator filters out files that are not in the given size range.
 *
 * @package    Symfony
 * @subpackage Components_Finder
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class SizeRangeFilterIterator extends \FilterIterator
{
    protected $patterns = array();

    /**
     * Constructor.
     *
     * @param \Iterator $iterator The Iterator to filter
     * @param array     $patterns An array of \NumberCompare instances
     */
    public function __construct(\Iterator $iterator, array $patterns)
    {
        $this->patterns = $patterns;

        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        $fileinfo = $this->getInnerIterator()->current();

        if (!$fileinfo->isFile())
        {
            return true;
        }

        $filesize = $fileinfo->getSize();
        foreach ($this->patterns as $compare)
        {
            if (!$compare->test($filesize))
            {
                return false;
            }
        }

        return true;
    }
}
