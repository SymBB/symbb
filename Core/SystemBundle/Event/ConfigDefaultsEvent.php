<?
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigDefaultsEvent extends Event
{
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    protected $configList;


    public function __construct(\Doctrine\Common\Collections\ArrayCollection $configList) {
        $this->configList = $configList;
    }
    
    public function getConfigList(){
        return $this->configList;
    }
    
    
    public function setDefaultConfig($key, $value){
        $this->configList->set($key, $value);
    }
}
