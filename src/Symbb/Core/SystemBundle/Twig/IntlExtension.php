<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Twig;

class IntlExtension extends \Twig_Extension
{

    /**
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    public function __construct(\Symbb\Core\UserBundle\Manager\UserManager $userManager)
    {
        $this->userManager = $userManager;

    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getIntlDatePattern', array($this, 'getIntlDatePattern')),
            new \Twig_SimpleFunction('formatIntlDate', array($this, 'formatIntlDate'))
        );

    }

    public function getIntlDatePattern($format = 'MEDIUM', $dateTimeFormFormat = false)
    {

        $fmt = $this->getIntlDateFormater($format);

        $pattern = $fmt->getPattern();

        if ($dateTimeFormFormat) {
            $pattern = \str_replace(array('dd', 'MM', 'yyyy', 'yy', 'HH', 'mm', 'ss'), array('d', 'm', 'Y', 'y', 'H', 'i', 's'), $pattern);
        }

        return $pattern;

    }

    public function formatIntlDate(\DateTime $date, $format = 'MEDIUM')
    {
        $fmt = $this->getIntlDateFormater($format);
        $value = $fmt->format($date);
        return $value;
    }

    /**
     *
     * @param type $format
     * @return \IntlDateFormatter
     * @throws Exception
     */
    protected function getIntlDateFormater($format)
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

        return $fmt;

    }

    public function getName()
    {
        return 'symbb_core_intl';

    }
}