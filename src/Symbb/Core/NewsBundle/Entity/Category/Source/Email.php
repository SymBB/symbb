<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Entity\Category\Source;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\NewsBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_category_sources_email")
 */
 class Email extends Category\Source
{

    /**
    * @ORM\Column(type="string", nullable=false)
    * @var string
    */
    protected $server;

    /**
    * @ORM\Column(type="string", nullable=false)
    * @var string
    */
    protected $username;

    /**
    * @ORM\Column(type="string", nullable=false)
    * @var string
    */
    protected $password;


    /**
    * @ORM\Column(type="integer", nullable=false)
    * @var integer
    */
    protected $port = 143;

    /**
    * @ORM\Column(type="boolean", nullable=false, name="is_ssl")
    * @var boolean
    */
    protected $ssl = false;

     /**
      * @return string
      */
     public function getServer()
     {
         return $this->server;
     }

     /**
      * @param string $server
      */
     public function setServer($server)
     {
         $this->server = $server;
     }

     /**
      * @return string
      */
     public function getUsername()
     {
         return $this->username;
     }

     /**
      * @param string $username
      */
     public function setUsername($username)
     {
         $this->username = $username;
     }

     /**
      * @return string
      */
     public function getPassword()
     {
         return $this->password;
     }

     /**
      * @param string $password
      */
     public function setPassword($password)
     {
         $this->password = $password;
     }

     /**
      * @return int
      */
     public function getPort()
     {
         return $this->port;
     }

     /**
      * @param int $port
      */
     public function setPort($port)
     {
         $this->port = $port;
     }

     /**
      * @return boolean
      */
     public function isSsl()
     {
         return $this->ssl;
     }

     /**
      * @param boolean $ssl
      */
     public function setSsl($ssl)
     {
         $this->ssl = $ssl;
     }


}