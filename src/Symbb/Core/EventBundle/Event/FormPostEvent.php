<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \Symbb\Core\ForumBundle\Entity\Post;
use Symfony\Component\Form\FormBuilderInterface;
use \Symbb\Core\UserBundle\Manager\UserManager;
use \Symbb\Core\UserBundle\Manager\GroupManager;

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
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\GroupManager
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
     * @return \Symbb\Core\UserBundle\Manager\GroupManager
     */
    public function getGroupManager()
    {
        return $this->groupManager;

    }

    /**
     * 
     * @return \Symbb\Core\UserBundle\Manager\UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;

    }
}
