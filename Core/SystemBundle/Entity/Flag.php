<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * @ORM\Table(name="flags")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Flag
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $objectClass;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $objectId;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=10)
     */
    private $flag = 'ignore';

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;



    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getFlag(){return $this->flag;}
    public function setFlag($value){$this->flag = $value;}
    public function setObject($object){
        $this->objectClass = ClassUtils::getRealClass($object);
        $this->objectId = $object->getId();
    }
    public function getObjectClass(){return $this->objectClass;}
    public function getObjectId(){return $this->objectId;}
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