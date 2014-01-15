<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\EventListener;

class DoctrineMetaDataListener
{
    
    protected $userClass;

    public function __construct($container)
    {
        $config                 = $container->getParameter('symbb_config');
        $this->config           = $config['usermanager'];
        $this->userClass        = $this->config['user_class'];
    }
    
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if($mapping['targetEntity'] == 'SymBB\Core\UserBundle\Entity\User') {
                $classMetadata->associationMappings[$fieldName]['targetEntity'] = $this->userClass;
            }
        }
    }
}