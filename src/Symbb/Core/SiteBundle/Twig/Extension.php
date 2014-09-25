<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\SiteBundle\Twig;

use \Symbb\Core\SiteBundle\Manager\SiteManager;

class Extension extends \Twig_Extension
{
    /**
     *
     * @var \Symbb\Core\SiteBundle\Manager\SiteManager
     */
    protected $siteManager;
    
    public function __construct(SiteManager $siteManager) {
        $this->siteManager = $siteManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbSiteManager', array($this, 'getSymbbSiteManager')),
            new \Twig_SimpleFunction('getSymbbSite', array($this, 'getSymbbSite'))
        );
    }
    
    public function getSymbbSite(){
        return $this->siteManager->getSite();
    }
    
    public function getSymbbSiteManager(){
        return $this->siteManager;
    }
    
    public function getName()
    {
        return 'symbb_core_site_extension';
    }
}