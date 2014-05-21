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

/**
 * @ORM\Table(name="access", indexes={@ORM\Index(name="search_idx", columns={"object", "objectId", "identity", "identityId"}), @ORM\Index(name="search_idx_2", columns={"object", "identity", "identityId"}), @ORM\Index(name="search_idx_2", columns={"identity", "identityId"})})
 * @ORM\Entity()
 */
class Access
{
    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $object;

    /**
     * @ORM\Column(type="integer")
     */
    protected $objectId;

    /**
     * @ORM\Column(type="string")
     */
    protected $identity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $identityId;

    /**
     * @ORM\Column(type="string")
     */
    protected $extension;

    /**
     * @ORM\Column(type="string")
     */
    protected $access;

    /**
     * @param string $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @param string $extension
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
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
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param int $identityId
     */
    public function setIdentityId($identityId)
    {
        $this->identityId = $identityId;
    }

    /**
     * @return int
     */
    public function getIdentityId()
    {
        return $this->identityId;
    }

    /**
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->objectId;
    }


}