<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Twig;

class TemplateExtension extends \Twig_Extension
{

    /**
     *
     * @var \Symbb\Core\SystemBundle\Manager\SiteManager
     */
    protected $siteManager;

    public function __construct($siteManager)
    {
        $this->siteManager = $siteManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbTemplate', array($this, 'getSymbbTemplate'))
        );
    }

    public function getSymbbTemplate($config)
    {
        $template = $this->siteManager->getTemplate($config);
        return $template;
    }

    public function getName()
    {
        return 'symbb_config_template';
    }
}