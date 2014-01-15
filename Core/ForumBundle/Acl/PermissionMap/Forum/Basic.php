<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Acl\PermissionMap\Forum;

use \SymBB\Core\ForumBundle\Acl\MaskBuilder\Forum\Basic as MaskBuilder;

class Basic extends \SymBB\Core\SystemBundle\Acl\AbstractPermissionMap {
    
    const PERMISSION_VIEW        = 'VIEW';
    const PERMISSION_CREATE_TOPIC= 'CREATE_TOPIC';
    const PERMISSION_CREATE_POST = 'CREATE_POST';
    
    public function __construct()
    {
        $this->map = array(
            self::PERMISSION_VIEW => array(
                MaskBuilder::MASK_VIEW
            ),

            self::PERMISSION_CREATE_TOPIC => array(
                MaskBuilder::MASK_CREATE_TOPIC
            ),

            self::PERMISSION_CREATE_POST => array(
                MaskBuilder::MASK_CREATE_POST
            ),

        );
    }
    
}