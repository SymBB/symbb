<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\SystemBundle\EventListener;

class DoctrineNameListener
{

    protected $prefix;

    public function __construct($container)
    {
        $config = $container->getParameter('symbb_config');
        $this->prefix = $config['database']['table_prefix'];
    }

    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $classMetadata->setPrimaryTable(array('name' => $this->prefix . $classMetadata->getTableName()));

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY && !empty($classMetadata->associationMappings[$fieldName]['joinTable'])) {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
}