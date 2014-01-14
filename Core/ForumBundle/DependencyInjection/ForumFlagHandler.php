<?
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\ForumBundle\DependencyInjection;

use \SymBB\Core\UserBundle\Entity\UserInterface;

class ForumFlagHandler extends \SymBB\Core\ForumBundle\DependencyInjection\AbstractFlagHandler
{
    
    public function findOne($flag, $object, UserInterface $user = null)
    {
        
        if(!$user){
            $user = $this->getUser();
        }
        
        $flagObject = $this->em->getRepository('SymBBCoreForumBundle:Forum\Flag', 'symbb')->findOneBy(array(
                'forum' => $object,
                'user' => $user,
                'flag' => $flag
            ));
        
        return $flagObject;
    }
    
    public function createNewFlag($object, UserInterface $user, $flag)
    {
        $flagObject = new \SymBB\Core\ForumBundle\Entity\Forum\Flag();
        $flagObject->setForum($object);
        $flagObject->setUser($user);
        $flagObject->setFlag($flag);
        return $flagObject;
    }
    
    protected function getMemcacheKey($flag, $forum){
        $key = 'symbb.forum.'.$forum->getId().'.flag.'.$flag;
        return $key;
    }
    
    public function insertFlag($object, $flag, UserInterface $user = null, $flushEm = true)
    {
        
        $ignore = false;
        
        // if we add a topic "new" flag, we need to check if the user has read access to the forum
        // an we must check if the user has ignore the forum
        if($flag === 'new'){
            $this->accessManager->addAccessCheck('SYMBB_FORUM#VIEW', $object, $user);
            $access = $this->accessManager->hasAccess();
            if($access){
                $ignore = $this->checkFlag($object, 'ignore', $user);
            } else {
                $ignore = true;
            }
        }
        
        if(!$ignore){
            parent::insertFlag($object, $flag, $user, $flushEm);
            $parent = $object->getParent();
            if(is_object($parent)){
                $this->insertFlag($parent, $flag, $user, $flushEm);
            }
        }
    }
    
    public function removeFlag($object, $flag, UserInterface $user = null)
    {
        parent::removeFlag($object, $flag, $user);
        
        // remove flag also from parent
        // but only if the parent has not the same flag for a other child
        $parent = $object->getParent();
        if(is_object($parent)){
            $subforums = $parent->getChildren();
            $otherFlag = false;
            foreach($subforums as $subforum){
                $otherFlag = $this->findOne($flag, $subforum, $user);
                if($otherFlag){
                    break;
                }
            }
            if(!$otherFlag){
                $this->removeFlag($parent, $flag, $user);
            }
        }
    }

    /**
     * 
     * @param object $object
     * @param string $flag
     * @return \SymBB\Core\ForumBundle\Entity\Forum\Flag
     */
    public function findFlagsByObjectAndFlag($object, $flag)
    {
        $flags = $this->em->getRepository('SymBBCoreForumBundle:Forum\Flag', 'symbb')->findBy(array(
                'forum' => $object,
                'flag' => $flag
            ));
        return $flags;
    }
}
