<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Imagine\Filter\Loader;

use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Color;
use Imagine\Image\Point;
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

        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        $alpha = $options['opacity'];

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $point = new Point($x, $y);
                $color = $image->getColorAt($point);
                $dR = $color->getRed();
                $dG = $color->getGreen();
                $dB = $color->getBlue();
                $image->draw()->dot($point, new Color(array($dR, $dG, $dB), $alpha));
            }
        }

        return $image;
    }
}