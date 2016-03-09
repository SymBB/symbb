<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * @ORM\Table(name="flags", indexes={
 *      @ORM\Index(name="findOne", columns={"objectId", "objectClass", "user_id", "flag"}),
 *      @ORM\Index(name="findAll", columns={"objectId", "objectClass", "user_id"}),
 *      @ORM\Index(name="findFlagsByObjectAndFlag", columns={"objectId", "objectClass", "flag"}),
 *      @ORM\Index(name="findFlagsByClassAndFlag", columns={"objectClass", "flag", "user_id"})
 * })
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Flag
{
    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $objectId;
    /**
     * @ORM\Column(type="string")
     */
    private $objectClass;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $flag = 'ignore';

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;


    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getFlag()
    {
        return $this->flag;
    }

    public function setFlag($value)
    {
        $this->flag = $value;
    }

    public function setObject($object)
    {
        $this->objectClass = ClassUtils::getRealClass($object);
        $this->objectId = $object->getId();
    }

    public function getObjectClass()
    {
        return $this->objectClass;
    }

    public function getObjectId()
    {
        return $this->objectId;
    }

    public function setUserId($object)
    {
        if(is_object($object)){
            $object = $object->getId();
        }
        $this->userId = $object;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getCreated()
    {
        return $this->created;
    }
    ############################################################################

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}