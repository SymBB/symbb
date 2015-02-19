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
    const UPLOAD_FILE = 'upload_file';

    /**
     * this will define the groups for the acl form later
     * @return array
     */
    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_USER => array(
                self::VIEW,
                self::CREATE_POST,
                self::CREATE_TOPIC,
                self::UPLOAD_FILE
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

    /**
     * this are the list of default access lists
     * @return array
     */
    public function getAccessSets()
    {

        $default = array();

        $guest = array(
            self::VIEW
        );

        $user = array_merge($guest,
            array(
                self::CREATE_POST,
                self::CREATE_TOPIC
            )
        );
        
        $userFull = array_merge($user,
            array(
                self::UPLOAD_FILE
            )
        );

        $mod = array_merge($userFull,
            array(
                self::EDIT_POST,
                self::EDIT_TOPIC,
                self::DELETE_POST,
                self::DELETE_TOPIC,
                self::MOVE_POST,
                self::MOVE_TOPIC,
                self::SPLIT_TOPIC,
            )
        );

        return array(
            // no access
            "default_1" => $default,
            // readonly
            "default_2" => $guest,
            // normal
            "default_3" => $user,
            // full (add extension access)
            "default_4" => $userFull,
            // moderator
            "default_5" => $mod
        );
    }
}