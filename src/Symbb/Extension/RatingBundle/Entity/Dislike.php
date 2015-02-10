<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\RatingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_topic_post_dislikes")
 * @ORM\Entity()
 */
class Dislike
{
    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="cascade")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function setUser($object)
    {
        $this->user = $object;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setPost($object)
    {
        $this->post = $object;
    }

    public function getPost()
    {
        return $this->post;
    }
    ############################################################################

}