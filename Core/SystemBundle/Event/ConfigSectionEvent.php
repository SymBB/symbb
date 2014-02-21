<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigSectionEvent extends Event
{
    protected $key;
    protected $section = 'default';


    public function __construct($key) {
        $this->key = $key;
    }
    
    public function getKey(){
        return $this->key;
    }
    
    public function setSection($section){
        $this->section = $section;
    }
    
    public function getSection(){
        return $this->section;
    }
}
