<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\Entity;

interface GroupInterface
{

    public function getId();
    
    public function getName();
    
    public function setName($name);

    public function getType();

    public function setType($value);

    public function getParent();
    
    public function __toString();
    
}