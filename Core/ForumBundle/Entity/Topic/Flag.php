<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Entity\Topic;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_topic_flags")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Flag
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="SymBB\Core\ForumBundle\Entity\Topic", inversedBy="flags")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", onDelete="cascade")
     */
    private $topic;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;
    
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=10)
     */
    private $flag = 'new';

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;



    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getFlag(){return $this->flag;}
    public function setFlag($value){$this->flag = $value;}
    public function setTopic($object){$this->topic = $object;}
    public function getTopic(){return $this->topic;}
    public function setUser($object){$this->user = $object;}
    public function getUser(){return $this->user;}
    public function getCreated(){return $this->created;}
    ############################################################################
    
    /**
    * @ORM\PrePersist
    */
    public function setCreatedValue()
    {
       $this->created = new \DateTime();
    }
}