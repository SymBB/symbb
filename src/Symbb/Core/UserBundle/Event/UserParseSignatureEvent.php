<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \SymBB\Core\UserBundle\Entity\UserInterface;
    
class UserParseSignatureEvent extends Event
{

    /**
     * @var \SymBB\Core\UserBundle\Entity\UserInterface 
     */
    protected $user;

    /**
     * @var string
     */
    protected $signature;


    public function __construct(UserInterface $user, $signature)
    {
        $this->user = $user;
        $this->signature = $signature;

    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;

    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;

    }

    public function setSignature($text)
    {
        $this->signature = $text;

    }
}
