<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Acl\MaskBuilder\Forum;

use \Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

class Mod extends BaseMaskBuilder {
    
    
    const MASK_EDIT_TOPIC   = 1;          // 1 << 2
    const MASK_EDIT_POST    = 2;          // 1 << 3
    const MASK_DELETE_TOPIC = 4;         // 1 << 4
    const MASK_DELETE_POST  = 8;         // 1 << 5
    const MASK_IDDQD        = 1073741823; // 1 << 0 | 1 << 1 | ... | 1 << 30

    const CODE_EDIT_TOPIC   = 'E_T';
    const CODE_EDIT_POST    = 'E_P';
    const CODE_DELETE_TOPIC = 'D_T';
    const CODE_DELETE_POST  = 'D_P';
    
}