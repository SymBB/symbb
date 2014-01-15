<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\EventBundle\Event;

use \SymBB\Core\ForumBundle\Entity\Post;

class TemplatePostEvent extends BaseTemplateEvent
{
    
    /**
     * @var Post 
     */
    protected $post;


    public function __construct($env, Post $post) {
        $this->post = $post;
        $this->env = $env;
    }
    
    public function getPost(){
        return $this->post;
    }
}
