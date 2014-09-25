<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\EventBundle\Event;

use \SymBB\Core\ForumBundle\Entity\Topic;

class TemplateTopicEvent extends BaseTemplateEvent
{
    
    /**
     * @var Post 
     */
    protected $topic;
    protected $env;
    protected $html = '';


    public function __construct($env, Topic $topic) {
        $this->topic = $topic;
        $this->env = $env;
    }
    
    public function getTopic(){
        return $this->topic;
    }
    
}
