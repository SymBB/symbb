<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\RatingBundle\Acl\MaskBuilder;

use \Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

class Basic extends BaseMaskBuilder {
    
    const MASK_VIEW         = 1;          // 1 << 0
    const MASK_EDIT         = 2;          // 1 << 2
    const MASK_MASTER       = 4;         // 1 << 6
    const MASK_OWNER        = 8;        // 1 << 7
    const MASK_IDDQD        = 1073741823; // 1 << 0 | 1 << 1 | ... | 1 << 30

    const CODE_VIEW         = 'V';
    const CODE_EDIT         = 'E';
    const CODE_MASTER       = 'M';
    const CODE_OWNER        = 'N';
    
}