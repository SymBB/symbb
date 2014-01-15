<?php

/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\Acl;

abstract class AbstractManager  {
    
    
    protected $maskBuilders     = array();
    protected $permissionMaps    = array();

    abstract public function __construct();
    abstract public function validateObject($prefix, $object);
    
    public function getPrefixFromMask($mask){
        $mask       = strtoupper($mask);
        $prefixes   = $this->getPrefixes();
        foreach($prefixes as $prefix){
            if(\strpos($mask, $prefix) === 0){
                return $prefix;
            }
        }
        return '';
    }
    
    public function checkPrefix($prefix)
    {
        $prefix     = strtoupper($prefix);
        
        $prefixes   = $this->getPrefixes();
        foreach($prefixes as $prefixCurr){
            if($prefix === $prefixCurr){
                return true;
            }
        }
        return false;
    }
    
    public function getPrefixes()
    {
        $prefixes = array_keys($this->maskBuilders);
        foreach($prefixes as $key => $prefix){
            $prefixes[$key] = strtoupper($prefix);
        }
        return $prefixes;
    }

    public function getMaskBuilder($prefix)
    {
        $prefix = strtoupper($prefix);
        if(isset($this->maskBuilders[$prefix])){
            return $this->maskBuilders[$prefix];
        }
        return null;
    }

    public function getPermissionMap($prefix)
    {
        $prefix = strtoupper($prefix);
        if(isset($this->permissionMaps[$prefix])){
            return $this->permissionMaps[$prefix];
        }
        return null;
    } 
    
    public function createDomainObject($prefix, $object){
        $prefix = strtoupper($prefix);
        if(
            $this->validateObject($prefix, $object)
        ){
            return new \SymBB\Core\SystemBundle\Acl\DomainObject($object, $prefix);
        }
        
        return null;
    }
    
    public function getAdditionalAccessCheck($permission, $object){
        $checks = array();
        
//        $checks = array(
//            array(
//                'object' => '',
//                'permission' => '';
//            )
//        );
        
        return $checks;
    }
    
    public function createAccessChecks($permission, $object, $indentity){
        $checks             = array();
        $permission         = explode('#', $permission);
        $prefix             = reset($permission).'#';
        $finalPermission    = end($permission);
        if($this->checkPrefix($prefix)){
            $finalObject     = $this->createDomainObject($prefix, $object);
            $permissionMap   = $this->getPermissionMap($prefix);
            if(
                is_object($indentity) && 
                is_object($finalObject) && 
                is_object($permissionMap) &&
                !empty($finalPermission)
            ){
                
                $checks[] = array(
                    'object'            => $finalObject,
                    'permission'        => $finalPermission,
                    'permissionMap'     => $permissionMap,
                    'indentity'         => $indentity
                );
            }
        }
        return $checks;
    }
}