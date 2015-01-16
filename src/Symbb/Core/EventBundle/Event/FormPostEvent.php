<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\EventBundle\Event;

use Symbb\Core\ForumBundle\DependencyInjection\PostManager;
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
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     *
     * @var PostManager
     */
    protected $postManager;

    /**
     * @var \Symbb\Core\UserBundle\Manager\UserManager
     */
    protected $userManager;

    /**
     *
     * @var \Symbb\Core\UserBundle\Manager\GroupManager
     */
    protected $groupManager;


    public function __construct(FormBuilderInterface $builder, $translator, PostManager $postManager, UserManager $userManager, GroupManager $groupManager)
    {
        $this->builder = $builder;
        $this->translator = $translator;
        $this->postManager = $postManager;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;

    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return PostManager
     */
    public function getPostManager()
    {
        return $this->postManager;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @return GroupManager
     */
    public function getGroupManager()
    {
        return $this->groupManager;
    }

}
