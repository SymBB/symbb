<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Event;

use Symbb\Core\ForumBundle\Entity\Post;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PostFormSaveEvent extends Event
{

    /**
     * @var Post
     */
    protected $post;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Post $post, Request $request)
    {
        $this->post = $post;
        $this->request = $request;

    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }



}
