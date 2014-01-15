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
use Symfony\Component\Form\FormBuilderInterface;

class FormTopicEvent extends Event
{
    
    /**
     * @var Topic 
     */
    protected $topic;
    
    /**
     *
     * @var FormBuilderInterface 
     */
    protected $builder;
    protected $translator;


    public function __construct(Topic $topic, FormBuilderInterface $builder, $translator) {
        $this->topic     = $topic;
        $this->builder = $builder;
        $this->translator = $translator;
    }
    
    public function getTopic(){
        return $this->topic;
    }
    
    public function getTranslator(){
        return $this->translator;
    }
    
    public function getBuilder(){
        return $this->builder;
    }
    
}
