<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\EventBundle\Event;

use \Symbb\Core\ForumBundle\Entity\Post;

class TemplatePostEvent extends BaseTemplateEvent
{
    /**
     * @var Post
     */
    protected $post;

    public function __construct($env, $post)
    {
        parent::__construct($env);
        $this->post = $post;
    }

    public function getPost()
    {
        return $this->post;
    }
}
