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
                    ->children()
                        ->scalarNode('user_class') 
                            ->defaultValue('SymBB\Core\ForumBundle\Entity\User')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('groupmanager') 
                    ->children()
                        ->scalarNode('group_class') 
                            ->defaultValue('SymBB\Core\ForumBundle\Entity\Group')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('system') 
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
                    ->children()
                        ->arrayNode('table_prefix') 
                            ->children()
                                ->scalarNode('prod') 
                                    ->defaultValue('symbb_')
                                ->end()
                                ->scalarNode('dev') 
                                    ->defaultValue('symbb_')
                                ->end()
                                ->scalarNode('test') 
                                    ->defaultValue('symbb_')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('template') 
                    ->children()
                        ->scalarNode('acp') 
                            ->defaultValue('SymBBTemplateAcpBundle')
                        ->end()
                        ->scalarNode('forum') 
                            ->defaultValue('SymBBTemplateSimpleBundle')
                        ->end()
                        ->scalarNode('portal') 
                            ->defaultValue('SymBBTemplateSimpleBundle')
                        ->end()
                        ->scalarNode('email') 
                            ->defaultValue('SymBBTemplateSimpleBundle')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}