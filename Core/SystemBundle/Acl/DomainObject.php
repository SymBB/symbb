<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\Acl;

use \Symfony\Component\Security\Acl\Model\DomainObjectInterface as BaseDomainObjectInterfac;

class DomainObject implements BaseDomainObjectInterfac {
    
    protected $object;
    protected $prefix = '';
    
    public function __construct($object, $prefix = '')
    {
        $this->object = $object;
        $this->prefix = $prefix;
    }
    
    public function getObjectIdentifier(){
        return $this->prefix.$this->object->getId();
    }
}