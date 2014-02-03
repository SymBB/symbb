<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\UserBundle\Entity;

interface UserInterface {
    

    public function getId();
    public function getUsername();
    public function getEmail();
    public function getTopics();
    public function getPosts();
    public function getGroups();
    public function getSymbbType();
    public function getTimezone();
    public function setTimezone(\DateTimeZone $tz);
    
    /**
     * @return \SymBB\Core\UserBundle\Entity\User\Data 
     */
    public function getSymbbData();
    public function setSymbbData(\SymBB\Core\UserBundle\Entity\User\Data  $value);
    
}