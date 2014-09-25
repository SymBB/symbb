<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\UserBundle\EventListener;

class DoctrineMetaDataListener
{

    protected $userClass;
    protected $groupClass;

    public function __construct($container)
    {
        $config = $container->getParameter('symbb_config');
        $userConfig = $config['usermanager'];
        $this->userClass = $userConfig['user_class'];
        $groupConfig = $config['groupmanager'];
        $this->groupClass = $groupConfig['group_class'];
    }

    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['targetEntity'] == 'Symbb\Core\UserBundle\Entity\User') {
                $classMetadata->associationMappings[$fieldName]['targetEntity'] = $this->userClass;
            } else if ($mapping['targetEntity'] == 'Symbb\Core\UserBundle\Entity\Group') {
                $classMetadata->associationMappings[$fieldName]['targetEntity'] = $this->groupClass;
            }
        }

    }
}