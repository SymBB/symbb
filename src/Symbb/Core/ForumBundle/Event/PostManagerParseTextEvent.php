<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \Symbb\Core\ForumBundle\Entity\Post;

class PostManagerParseTextEvent extends Event
{

    /**
     * @var Post 
     */
    protected $post;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $section;

    public function __construct(Post $post, $text, $section = "post")
    {
        $this->post = $post;
        $this->text = $text;
        $this->section = $section;

    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;

    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;

    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->section;

    }

    public function setText($text)
    {
        $this->text = $text;

    }
}
