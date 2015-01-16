<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\ForumBundle\Twig;

use \Symbb\Core\ForumBundle\DependencyInjection\ForumManager;
use \Symbb\Core\ForumBundle\DependencyInjection\ForumFlagHandler;

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

    /**
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     *
     */
    protected $dispatcher;

    public function __construct($container)
    {
        $this->flagHandler = $container->get('symbb.core.forum.flag');
        $this->forumManager = $container->get('symbb.core.forum.manager');
        $this->translator = $container->get('translator');
        $this->dispatcher = $container->get('event_dispatcher');
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkSymbbForForumIgnoreFlag', array($this, 'checkSymbbForIgnoreFlag')),
            new \Twig_SimpleFunction('checkSymbbForForumNewFlag', array($this, 'checkSymbbForNewPostFlag')),
            new \Twig_SimpleFunction('checkSymbbForForumFlag', array($this, 'checkForFlag')),
            new \Twig_SimpleFunction('checkSymbbForumLabels', array($this, 'getLabels')),
        );
    }

    public function checkSymbbForNewPostFlag($element)
    {
        $check = $this->checkForFlag($element, 'new');
        return $check;
    }

    public function checkSymbbForIgnoreFlag($element)
    {
        return $this->checkForFlag($element, 'ignore');
    }

    public function checkForFlag(\Symbb\Core\ForumBundle\Entity\Forum $element, $flag)
    {
        return $this->flagHandler->checkFlag($element, $flag);
    }


    public function getLabels(\Symbb\Core\ForumBundle\Entity\Forum $element)
    {
        $labels = array();


        if ($this->checkSymbbForNewPostFlag($element)) {
            $labels[] = array(
                'title' => 'new',
                'type' => 'success'
            );
        }

        $event = new \Symbb\Core\EventBundle\Event\ForumLabelsEvent($element, $labels);
        $this->dispatcher->dispatch('symbb.forum.labels', $event);

        foreach ($labels as $key => $label) {
            $labels[$key]['title'] = $this->translator->trans($label['title']);
        }

        return $labels;
    }

    public function getName()
    {
        return 'symbb_forum_data';
    }

}