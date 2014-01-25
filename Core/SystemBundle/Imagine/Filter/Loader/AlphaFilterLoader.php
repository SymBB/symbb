<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\Imagine\Filter\Loader;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;

class AlphaFilterLoader implements LoaderInterface
{
    
    public function __construct(ImagineInterface $imagine, $rootPath)
    {
        $this->imagine = $imagine;
        $this->rootPath = $rootPath;
    }

    /**
    * @see Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface::load()
    */
    public function load(ImageInterface $image, array $options = array())
    {
        $alpha = $options['alpha'];
        
        return $image->applyMask();
    }
}