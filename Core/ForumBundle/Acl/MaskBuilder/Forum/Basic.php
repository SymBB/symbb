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

class Basic extends BaseMaskBuilder {
    
    const MASK_VIEW         = 1;          
    const MASK_CREATE_TOPIC = 2;          
    const MASK_CREATE_POST  = 4;          
    const MASK_IDDQD        = 1073741823;

    const CODE_VIEW         = 'V_T';
    const CODE_CREATE_TOPIC = 'C_T';
    const CODE_CREATE_POST  = 'C_P';
    
}