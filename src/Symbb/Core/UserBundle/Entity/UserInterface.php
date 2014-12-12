<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Entity;
use \Symfony\Component\Security\Core\User\AdvancedUserInterface;

interface UserInterface extends AdvancedUserInterface
{
    // removed because php 5.3.3 has a error if 2 interfaces implement the same method ( FOS\MessageBundle\Model\ParticipantInterface )
    //public function getId();

    public function getEmail();

    public function getTopics();

    public function getPosts();

    public function getGroups();

    public function getSymbbType();
    
    public function getFieldValues();
    
    public function getFieldValue(\Symbb\Core\UserBundle\Entity\Field $field);

    /**
     * @return \Symbb\Core\UserBundle\Entity\User\Data
     */
    public function getSymbbData();

    public function setSymbbData(\Symbb\Core\UserBundle\Entity\User\Data $value);
    
    /**
     * this method need to set some other data e.g a "changed" field
     * if not than doctrine will not save the entity if only the PW is changed ( because the postUpdate event are not called )
     * @param string $pw
     */
    public function setPlainPassword($pw);
    
    public function isEnabled();
    
    public function enable();
    
    public function disable();

    public function setChangedValue();

    public function setCreatedValue();

    public function getCreated();

    public function getChanged();

    public function setGroups($groups);

    public function addGroup(\FOS\UserBundle\Model\GroupInterface $group);

}