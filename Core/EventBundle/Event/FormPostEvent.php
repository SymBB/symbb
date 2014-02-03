<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \SymBB\Core\ForumBundle\Entity\Post;
use Symfony\Component\Form\FormBuilderInterface;
use \SymBB\Core\UserBundle\DependencyInjection\UserManager;
use \SymBB\Core\UserBundle\DependencyInjection\GroupManager;

class FormPostEvent extends Event
{

    /**
     * @var Post 
     */
    protected $post;

    /**
     *
     * @var FormBuilderInterface 
     */
    protected $builder;

    /**
     *
     * @var \Symfony\Component\Translation\Translator 
     */
    protected $translator;

    /**
     *
     * @var \Doctrine\ORM\EntityManager 
     */
    protected $em;

    /**
     * @var \SymBB\Core\UserBundle\DependencyInjection\UserManager 
     */
    protected $userManager;

    /**
     *
     * @var \SymBB\Core\UserBundle\DependencyInjection\GroupManager 
     */
    protected $groupManager;
    

    public function __construct(Post $post, FormBuilderInterface $builder, $translator, $em, UserManager $userManager, GroupManager $groupManager)
    {
        $this->post = $post;
        $this->builder = $builder;
        $this->translator = $translator;
        $this->em = $em;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;

    }

    public function getPost()
    {
        return $this->post;

    }

    public function getBuilder()
    {
        return $this->builder;

    }

    /**
     * 
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator()
    {
        return $this->translator;

    }

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;

    }

    /**
     * 
     * @return \SymBB\Core\UserBundle\DependencyInjection\GroupManager 
     */
    public function getGroupManager()
    {
        return $this->groupManager;

    }

    /**
     * 
     * @return \SymBB\Core\UserBundle\DependencyInjection\UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;

    }
}
