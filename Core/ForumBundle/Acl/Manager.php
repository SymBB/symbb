<?php

/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\Acl;

use \SymBB\Core\ForumBundle\Acl\MaskBuilder\Forum\Basic as ForumMaskBuilder;
use \SymBB\Core\ForumBundle\Acl\PermissionMap\Forum\Basic as ForumPermissionMap;
use \SymBB\Core\ForumBundle\Acl\MaskBuilder\Forum\Mod as ForumModMaskBuilder;
use \SymBB\Core\ForumBundle\Acl\PermissionMap\Forum\Mod as ForumModPermissionMap;
use \SymBB\Core\ForumBundle\Acl\MaskBuilder\Post\Basic as PostMaskBuilder;
use \SymBB\Core\ForumBundle\Acl\PermissionMap\Post\Basic as PostPermissionMap;
use \SymBB\Core\ForumBundle\Acl\MaskBuilder\Topic\Basic as TopicMaskBuilder;
use \SymBB\Core\ForumBundle\Acl\PermissionMap\Topic\Basic as TopicPermissionMap;
use \SymBB\Core\ForumBundle\Entity\Forum;
use \SymBB\Core\ForumBundle\Entity\Post;
use \SymBB\Core\ForumBundle\Entity\Topic;
use \SymBB\Core\SystemBundle\Acl\AbstractManager;

class Manager extends AbstractManager {
    
    const SYMBB_FORUM_BASIC =  'SYMBB_FORUM#';
    const SYMBB_FORUM_MOD =  'SYMBB_FORUM_MOD#';
    const SYMBB_TOPIC_BASIC =  'SYMBB_TOPIC#';
    const SYMBB_POST_BASIC =  'SYMBB_POST#';
    
    public function __construct()
    {

        $this->maskBuilders = array(
            self::SYMBB_FORUM_BASIC => new ForumMaskBuilder(),
            self::SYMBB_FORUM_MOD => new ForumModMaskBuilder(),
            self::SYMBB_TOPIC_BASIC => new TopicMaskBuilder(),
            self::SYMBB_POST_BASIC => new PostMaskBuilder()
        );
        
        $this->permissionMaps = array(
            self::SYMBB_FORUM_BASIC => new ForumPermissionMap(),
            self::SYMBB_FORUM_MOD => new ForumModPermissionMap(),
            self::SYMBB_TOPIC_BASIC => new TopicPermissionMap(),
            self::SYMBB_POST_BASIC => new PostPermissionMap()
        );
        
    }
    
    public function validateObject($prefix, $object){
        if($prefix == self::SYMBB_FORUM_BASIC && $object instanceof Forum){
            return true;
        } else if($prefix == self::SYMBB_FORUM_MOD && $object instanceof Forum){
            return true;
        } else if($prefix == self::SYMBB_TOPIC_BASIC && $object instanceof Topic){
            return true;
        } else if($prefix == self::SYMBB_POST_BASIC && $object instanceof Post){
            return true;
        }
        return false;
    }
    
    /**
     * insert additional checks, in this case we need to add some checks for Edit/delete
     * if we check for "post" edit and delete we must also check if we have acces to "edit post" or "delete post" in the complete forum ( mod access as example )
     * @param string $permission
     * @param type $object
     * @return array
     */
    public function getAdditionalAccessCheck($permission, $object){
        
        $checks = array();
        
        if(
            $permission === self::SYMBB_POST_BASIC.PostPermissionMap::PERMISSION_EDIT && 
            $object instanceof \SymBB\Core\ForumBundle\Entity\Post
        ){
            $checks[] = array(
                'object' => $object->getTopic()->getForum(),
                'permission' => self::SYMBB_FORUM_MOD.ForumModPermissionMap::PERMISSION_EDIT_POST
            );
        }
        
        if(
            $permission === self::SYMBB_POST_BASIC.PostPermissionMap::PERMISSION_DELETE && 
            $object instanceof \SymBB\Core\ForumBundle\Entity\Post
        ){
            $checks[] = array(
                'object' => $object->getTopic()->getForum(),
                'permission' => self::SYMBB_FORUM_MOD.ForumModPermissionMap::PERMISSION_DELETE_POST
            );
        }
        
        if(
            $permission === self::SYMBB_TOPIC_BASIC.PostPermissionMap::PERMISSION_EDIT && 
            $object instanceof \SymBB\Core\ForumBundle\Entity\Post
        ){
            $checks[] = array(
                'object' => $object->getTopic()->getForum(),
                'permission' => self::SYMBB_FORUM_MOD.ForumModPermissionMap::PERMISSION_EDIT_TOPIC
            );
        }
        
        if(
            $permission === self::SYMBB_TOPIC_BASIC.PostPermissionMap::PERMISSION_DELETE && 
            $object instanceof \SymBB\Core\ForumBundle\Entity\Post
        ){
            $checks[] = array(
                'object' => $object->getTopic()->getForum(),
                'permission' => self::SYMBB_FORUM_MOD.ForumModPermissionMap::PERMISSION_DELETE_TOPIC
            );
        }
        
        if(
            $permission === self::SYMBB_TOPIC_BASIC.PostPermissionMap::PERMISSION_VIEW && 
            $object instanceof \SymBB\Core\ForumBundle\Entity\Post
        ){
            $checks[] = array(
                'object' => $object->getTopic()->getForum(),
                'permission' => self::SYMBB_FORUM_BASIC.ForumPermissionMap::PERMISSION_VIEW
            );
        }
        
        return $checks;
    }
    
}