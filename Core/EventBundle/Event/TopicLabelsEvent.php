<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \SymBB\Core\ForumBundle\Entity\Topic;

class TopicLabelsEvent extends Event
{
    
    /**
     * @var Topic 
     */
    protected $topic;
    
    /**
     *
     * @var array 
     */
    protected $labels;

    public function __construct(Topic $topic, $labels) {
        $this->topic     = $topic;
        $this->labels    = (array)$labels;
    }
    
    /**
     * 
     * @return Topic
     */
    public function getTopic(){
        return $this->topic;
    }
    
    /**
     * 
     * @return array
     */
    public function getLabels(){
        return $this->labels;
    }
    
    public function addLabel($label){
        if(!\is_array($label)){
            throw new Exception('addLabel need a array as parameter');
        }
        $this->labels[] = $label;
    }
    
}
