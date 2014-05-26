<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Security\Authorization;

use SymBB\Core\SystemBundle\DependencyInjection\AccessManager;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractVoter implements VoterInterface
{
    /**
     * @var \SymBB\Core\SystemBundle\DependencyInjection\AccessManager
     */
    protected $accessManager;

    protected $supportedClasses = array();

    const GROUP_USER = 'UserAccess';
    const GROUP_MOD = 'ModAccess';
    const GROUP_DEFAULT = 'DefaultAccess';

    /**
     * @param AccessManager $accessManager
     */
    public function __construct(AccessManager $accessManager){
        $this->accessManager = $accessManager;
    }

    abstract public function getGroupedAttributes();

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        $attribute = strtolower($attribute);
        $groupedAttributes = $this->getGroupedAttributes();
        foreach($groupedAttributes as $attributes){
            if(in_array($attribute, $attributes)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        foreach($this->supportedClasses as $supportedClass){
            if($supportedClass === $class || is_subclass_of($class, $supportedClass)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param TokenInterface $token
     * @param object $object
     * @param array $attributes
     * @return int
     * @throws InvalidArgumentException
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if(1 !== count($attributes)) {
            throw new InvalidArgumentException(
                'Only one attribute is allowed'
            );
        }

        // set the attribute to check against
        $attribute = strtolower($attributes[0]);

        // get current logged in user
        $user = $token->getUser();

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            default:
                $this->accessManager->addAccessCheck($attribute, $object);
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }
    }
}