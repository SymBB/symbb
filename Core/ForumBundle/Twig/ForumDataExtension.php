<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\ForumBundle\Twig;

use \SymBB\Core\ForumBundle\DependencyInjection\ForumManager;
use \SymBB\Core\ForumBundle\DependencyInjection\ForumFlagHandler;

class ForumDataExtension extends \Twig_Extension
{
    /**
     * @var ForumFlagHandler
     */
    protected $flagHandler;
    /**
     *
     * @var ForumManager
     */
    protected $forumManager;

    public function __construct(ForumFlagHandler $forumFlagHandler, ForumManager $forumManager) {
        $this->flagHandler = $forumFlagHandler;
        $this->forumManager = $forumManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkSymbbForForumIgnoreFlag', array($this, 'checkSymbbForIgnoreFlag')),
            new \Twig_SimpleFunction('checkSymbbForForumNewFlag', array($this, 'checkSymbbForNewPostFlag')),
            new \Twig_SimpleFunction('checkSymbbForForumFlag', array($this, 'checkForFlag')),
            new \Twig_SimpleFunction('getNewestPost', array($this, 'getNewestPost')),
        );
    }
    
    public function checkSymbbForNewPostFlag($element)
    {
        return $this->checkForFlag($element, 'new');
    }
    
    public function getNewestPost($parent, $limit = 10)
    {
        return $this->forumManager->findNewestPosts($parent, $limit);
    }
    
    public function checkSymbbForIgnoreFlag($element)
    {
        return $this->checkForFlag($element, 'ignore');
    }
    
    public function checkForFlag(\SymBB\Core\ForumBundle\Entity\Forum $element, $flag)
    {
        return $this->flagHandler->checkFlag($element, $flag);
    }
        
    public function getName()
    {
        return 'symbb_forum_data';
    }
        
}