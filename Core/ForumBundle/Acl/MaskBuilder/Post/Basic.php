<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Acl\MaskBuilder\Post;

use \Symfony\Component\Security\Acl\Permission\MaskBuilder as BaseMaskBuilder;

class Basic extends BaseMaskBuilder {
    
    const MASK_VIEW         = 1;          // 1 << 0
    const MASK_EDIT         = 2;          // 1 << 2
    const MASK_DELETE       = 4;          // 1 << 3
    const MASK_OPERATOR     = 8;         // 1 << 5
    const MASK_MASTER       = 16;         // 1 << 6
    const MASK_OWNER        = 32;        // 1 << 7
    const MASK_IDDQD        = 1073741823; // 1 << 0 | 1 << 1 | ... | 1 << 30

    const CODE_VIEW         = 'V';
    const CODE_EDIT         = 'E';
    const CODE_DELETE       = 'D';
    const CODE_OPERATOR     = 'O';
    const CODE_MASTER       = 'M';
    const CODE_OWNER        = 'N';
    
}