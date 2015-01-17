<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\MessageBundle\Twig;


use Symbb\Core\MessageBundle\DependencyInjection\MessageManager;

class ManagerExtension extends \Twig_Extension
{

    /**
     * @var MessageManager
     */
    protected $messageManager;

    /**
     * @param MessageManager $messageManager
     * @param GroupManager $groupManager
     */
    public function __construct($messageManager)
    {

        $this->messageManager = $messageManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbMessageManager', array($this, 'getSymbbMessageManager'))
        );
    }

    public function getSymbbMessageManager()
    {
        return $this->messageManager;
    }


    public function getName()
    {
        return 'symbb_message_managers';
    }
}