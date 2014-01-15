<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Acl\PermissionMap\Forum;

use SymBB\Core\ForumBundle\Acl\MaskBuilder\Forum\Mod as MaskBuilder;

class Mod extends \SymBB\Core\SystemBundle\Acl\AbstractPermissionMap {
    
    const PERMISSION_EDIT_TOPIC  = 'EDIT_TOPIC';
    const PERMISSION_EDIT_POST   = 'EDIT_POST';
    const PERMISSION_DELETE_TOPIC= 'DELETE_TOPIC';
    const PERMISSION_DELETE_POST = 'DELETE_POST';
    
    public function __construct()
    {
        $this->map = array(

            self::PERMISSION_EDIT_TOPIC => array(
                MaskBuilder::MASK_EDIT_TOPIC
            ),

            self::PERMISSION_EDIT_POST => array(
                MaskBuilder::MASK_EDIT_POST
            ),

            self::PERMISSION_DELETE_TOPIC => array(
                MaskBuilder::MASK_DELETE_TOPIC
            ),

            self::PERMISSION_DELETE_POST => array(
                MaskBuilder::MASK_DELETE_POST
            ),

        );
    }
    
}