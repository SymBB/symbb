<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\ConfigBundle\DependencyInjection; 

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface 
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sym_bb_core_config');

        $rootNode
            ->children()
                ->arrayNode('usermanager') 
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user_class') 
                            ->defaultValue('SymBB\Core\UserBundle\Entity\User')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('groupmanager') 
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('group_class') 
                            ->defaultValue('SymBB\Core\UserBundle\Entity\Group')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('system') 
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name') 
                            ->defaultValue('SymBB Test System')
                        ->end()
                        ->scalarNode('email') 
                            ->defaultValue('alpha@symbb.de')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('database') 
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('table_prefix') 
                            ->defaultValue('symbb_')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('directory')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}