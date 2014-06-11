<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\RatingBundle\Security\Authorization;

use SymBB\Core\ForumBundle\Entity\Post;
use SymBB\Core\ForumBundle\Entity\Topic;
use SymBB\Core\SystemBundle\DependencyInjection\AccessManager;
use SymBB\Core\SystemBundle\Security\Authorization\AbstractVoter;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RatingVoter extends AbstractVoter implements VoterInterface
{
    protected $supportedClasses = array('SymBB\Core\ForumBundle\Entity\Forum');

    const CREATE_RATING = 'create_rating';
    const VIEW_RATING = 'view_rating';

    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_USER => array(
                self::CREATE_RATING,
                self::VIEW_RATING,
            )
        );
    }
}