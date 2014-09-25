<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \Symbb\Core\ForumBundle\Entity\Forum;

class ForumLabelsEvent extends Event
{
    
    /**
     * @var Forum 
     */
    protected $forum;
    
    /**
     *
     * @var array 
     */
    protected $labels;

    public function __construct(Forum $forum, $labels) {
        $this->forum     = $forum;
        $this->labels    = (array)$labels;
    }
    
    /**
     * 
     * @return Forum
     */
    public function getForum(){
        return $this->forum;
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
