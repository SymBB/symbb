<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\Imagine\Filter\Loader; 

use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Box;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class OpacityFilterLoader implements LoaderInterface
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
        
        $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        if (!empty($options['mode']) && 'inset' === $options['mode']) {
            $mode = ImageInterface::THUMBNAIL_INSET;
        }
        
        $alpha      = $options['opacity'];
        
        $size       = $image->getSize();
        $origWidth  = $size->getWidth();
        $origHeight = $size->getHeight();
        
        $palette    = new RGB();
        $size       = new Box($origWidth, $origHeight);
        $color      = $palette->color('#000', $alpha);
        $image      = $this->imagine->create($size, $color);
        
        return $image->applyMask($image); 
    }
}