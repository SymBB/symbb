<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\UserBundle\Entity\User;

/**
 * @ORM\Table(name="forum_topic_post_histories")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class History
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime $changed
     */
    protected $changed;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post", inversedBy="history")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="cascade")
     *
     * @var Post $post
     */
    protected $post;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="NO ACTION")
     *
     * @var User $editor
     */
    private $editor;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string $reason
     */
    protected $reason = "";

    /**
     * @ORM\Column(type="text")
     *
     * @var string $reason
     */
    protected $oldText = "";

    /**
     * @param DateTime $changed
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
    }

    /**
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $oldText
     */
    public function setOldText($oldText)
    {
        $this->oldText = $oldText;
    }

    /**
     * @return string
     */
    public function getOldText()
    {
        return $this->oldText;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param User $editor
     */
    public function setEditor(User $editor)
    {
        $this->editor = $editor;
    }

    /**
     * @return User
     */
    public function getEditor()
    {
        return $this->editor;
    }


}