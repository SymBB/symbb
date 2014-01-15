<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\RatingBundle\Acl\PermissionMap;

use SymBB\Extension\RatingBundle\Acl\MaskBuilder\Basic as MaskBuilder;

class Basic extends \SymBB\Core\SystemBundle\Acl\AbstractPermissionMap {
    
    const PERMISSION_VIEW        = 'VIEW';
    const PERMISSION_EDIT        = 'EDIT';
    const PERMISSION_MASTER      = 'MASTER';
    const PERMISSION_OWNER       = 'OWNER';
    
    public function __construct()
    {
        $this->map = array(
            self::PERMISSION_VIEW => array(
                MaskBuilder::MASK_VIEW,
                MaskBuilder::MASK_EDIT,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_EDIT => array(
                MaskBuilder::MASK_EDIT,
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_MASTER => array(
                MaskBuilder::MASK_MASTER,
                MaskBuilder::MASK_OWNER,
            ),

            self::PERMISSION_OWNER => array(
                MaskBuilder::MASK_OWNER,
            ),
        );
    }
}