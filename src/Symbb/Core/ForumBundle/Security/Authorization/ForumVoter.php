<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Security\Authorization;

use Symbb\Core\SystemBundle\Security\Authorization\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ForumVoter extends AbstractVoter implements VoterInterface
{
    protected $supportedClasses = array('Symbb\Core\ForumBundle\Entity\Forum');

    const VIEW = 'view';
    const CREATE_POST = 'create_post';
    const CREATE_TOPIC = 'create_topic';
    const EDIT_POST = 'edit_post';
    const EDIT_TOPIC = 'edit_topic';
    const DELETE_POST = 'delete_post';
    const DELETE_TOPIC = 'delete_topic';
    const MOVE_POST = 'move_post';
    const MOVE_TOPIC = 'move_topic';
    const SPLIT_TOPIC = 'split_topic';

    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_USER => array(
                self::VIEW,
                self::CREATE_POST,
                self::CREATE_TOPIC,
            ),
            AbstractVoter::GROUP_MOD => array(
                self::EDIT_POST,
                self::EDIT_TOPIC,
                self::DELETE_POST,
                self::DELETE_TOPIC,
                self::MOVE_POST,
                self::MOVE_TOPIC,
                self::SPLIT_TOPIC,
            )
        );
    }
}