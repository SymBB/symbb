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
use \Symfony\Component\Form\Form;

class EditPostEvent extends Event
{

    /**
     * @var Post 
     */
    protected $post;
    
    /**
     * @var Form
     */
    protected $form;

    public function __construct(Post $post, Form $form) {
        $this->post      = $post;
        $this->form      = $form;
    }
    
    /**
     * @return Post
     */
    public function getPost(){
        return $this->post;
    }
    
    /**
     * @return Form
     */
    public function getForm(){
        return $this->form;
    }
    
}
