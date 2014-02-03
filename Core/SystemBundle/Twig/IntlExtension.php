<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\systemBundle\Twig;

class IntlExtension extends \Twig_Extension
{

    /**
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager 
     */
    protected $userManager;

    public function __construct(\SymBB\Core\UserBundle\DependencyInjection\UserManager $userManager)
    {
        $this->userManager = $userManager;

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getIntlDatePattern', array($this, 'getIntlDatePattern'))
        );

    }

    public function getIntlDatePattern($format, $dateTimeFormFormat = false)
    {

        if (\is_string($format)) {
            $format = \constant('\IntlDateFormatter::' . \strtoupper($format));
        } else if (!\is_numeric($format)) {
            throw new Exception('Format must be an string or IntlDateFormater Int Value');
        }

        $locale = \Symfony\Component\Locale\Locale::getDefault();
        $tz = $this->userManager->getTimezone();

        $fmt = new \IntlDateFormatter(
            $locale, $format, $format, $tz->getName(), \IntlDateFormatter::GREGORIAN
        );

        $pattern = $fmt->getPattern();

        if ($dateTimeFormFormat) {
            $pattern = \str_replace(array('dd', 'MM', 'yyyy', 'yy', 'HH', 'mm', 'ss'), array('d', 'm', 'Y', 'y', 'H', 'i', 's'), $pattern);
        }

        return $pattern;

    }

    public function getName()
    {
        return 'symbb_core_intl';

    }
}