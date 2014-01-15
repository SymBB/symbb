<?php

/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\RatingBundle\Acl;

use \SymBB\Extension\RatingBundle\Acl\MaskBuilder\Basic as MaskBuilder;
use \SymBB\Extension\RatingBundle\Acl\PermissionMap\Basic as PermissionMap;
use \SymBB\Core\SystemBundle\Acl\AbstractManager;
use \SymBB\Core\ForumBundle\Entity\Forum;

class Manager extends AbstractManager {
    
    const BASIC =  'SYMBB_FORUM_RATING#';
    
    public function __construct()
    {

        $this->maskBuilders = array(
            self::BASIC => new MaskBuilder()
        );
        
        $this->permissionMaps = array(
            self::BASIC => new PermissionMap()
        );
        
    }
    
    public function validateObject($prefix, $object){
        if($object instanceof Forum){
            return true;
        }
        return false;
    }
    
}