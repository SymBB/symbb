<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Manager;

use SymBB\Core\SystemBundle\Entity\Access;
use SymBB\Core\SystemBundle\Security\Authorization\AbstractVoter;
use SymBB\Core\UserBundle\Entity\UserInterface;
use \Symfony\Component\Security\Core\Util\ClassUtils;

class AccessVoterManager
{

    /**
     * @var AbstractVoter[]
     */
    protected $voterList = array();


    public function getAccessList($object){
        $list = array();

        foreach($this->voterList as $voter){
            if($voter->supportsClass(ClassUtils::getRealClass($object))){
                $attributes = $voter->getGroupedAttributes();
                $list = array_merge_recursive($list, $attributes);
            }
        }

        return $list;
    }

    public function addVoter($voter){
        if($voter instanceof AbstractVoter){
            $this->voterList[] = $voter;
        }
    }
}