<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Entity; 

interface UserInterface extends \Symfony\Component\Security\Core\User\AdvancedUserInterface
{

    public function getId();

    public function getUsername();

    public function getEmail();

    public function getTopics();

    public function getPosts();

    public function getGroups();

    public function getSymbbType();
    
    public function getCreated();
    
    public function getPassword();

    /**
     * @return \SymBB\Core\UserBundle\Entity\User\Data 
     */
    public function getSymbbData();

    public function setSymbbData(\SymBB\Core\UserBundle\Entity\User\Data $value);
    
    /**
     * this method need to set some other data e.g a "changed" field
     * if not than doctrine will not save the entity if only the PW is changed ( because the postUpdate event are not called )
     * @param string $pw
     */
    public function setPlainPassword($pw);
}