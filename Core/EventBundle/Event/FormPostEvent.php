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
use \SymBB\Core\ForumBundle\Entity\Post;
use Symfony\Component\Form\FormBuilderInterface;

class FormPostEvent extends Event
{
    
    /**
     * @var Post 
     */
    protected $post;
    
    /**
     *
     * @var FormBuilderInterface 
     */
    protected $builder;
    protected $translator;


    public function __construct(Post $post, FormBuilderInterface $builder, $translator) {
        $this->post     = $post;
        $this->builder = $builder;
        $this->translator = $translator;
    }
    
    public function getPost(){
        return $this->post;
    }
    
    public function getTranslator(){
        return $this->translator;
    }
    
    public function getBuilder(){
        return $this->builder;
    }
    
}
