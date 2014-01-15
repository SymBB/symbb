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
use \Symfony\Component\Form\Form;

class EditTopicEvent extends Event
{
    
    /**
     * @var Topic 
     */
    protected $topic;
    
    /**
     *
     * @var Form 
     */
    protected $form;

    public function __construct(Topic $topic, Form $form) {
        $this->topic     = $topic;
        $this->form      = $form;
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
     * @return Form
     */
    public function getForm(){
        return $this->form;
    }
    
}
