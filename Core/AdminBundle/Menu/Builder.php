<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav'));
        $menu->setExtra('translation_domain', 'symbb_acp_menu');
        
        $menu->addChild('Sites', array('route' => '_symbbcoresitebundle_site_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Forummanagment', array('route' => '_symbbcoreforumbundle_forum_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Usermanagment', array('route' => '_symbbcoreuserbundle_group_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('User and Groupaccess', array('route' => '_symbbcoreuserbundle_group_access', 'routeParameters' => array('step' => 1)))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Options', array('route' => '_symbbcoresystembundle_config'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Extensions', array('route' => '_symbbcoresystembundle_extensions'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Maintenance', array('route' => '_symbbcoresystembundle_maintenance'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Translations', array('route' => 'jms_translation_index'))->setExtra('translation_domain', 'symbb_acp_menu');
        return $menu;

    }
    
    
    
    public function forumMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $parent = $options['parent'];

        if(!$parent){
            $menu->addChild('Forum List', array('route' => '_symbbcoreforumbundle_forum_list'))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Forum', array('route' => '_symbbcoreforumbundle_forum_new'))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Category', array('route' => '_symbbcoreforumbundle_forum_new_category'))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Link', array('route' => '_symbbcoreforumbundle_forum_new_link'))->setExtra('translation_domain', 'symbb_acp_menu');
        } else {
            $menu->addChild('Forum List', array('route' => '_symbbcoreforumbundle_forum_list_child', 'routeParameters' => array('parent' => $parent)))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Forum', array('route' => '_symbbcoreforumbundle_forum_new', 'routeParameters' => array('parent' => $parent)))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Category', array('route' => '_symbbcoreforumbundle_forum_new_category', 'routeParameters' => array('parent' => $parent)))->setExtra('translation_domain', 'symbb_acp_menu');
            $menu->addChild('New Link', array('route' => '_symbbcoreforumbundle_forum_new_link', 'routeParameters' => array('parent' => $parent)))->setExtra('translation_domain', 'symbb_acp_menu');
        }
        
        
        return $menu;

    }
    
    public function accessMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Groupaccess', array('route' => '_symbbcoreuserbundle_group_access', 'routeParameters' => array('step' => 1), 'class' => 'list-group-item'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Useraccess', array('route' => '_symbb_acp', 'class' => 'list-group-item'))->setExtra('translation_domain', 'symbb_acp_menu');

        return $menu;

    }
    
    
    
    public function userManagmentMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Group List', array('route' => '_symbbcoreuserbundle_group_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('User List', array('route' => '_symbbcoreuserbundle_user_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Custom Fields', array('route' => '_symbbcoreuserbundle_field_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        
        return $menu;

    }
    
    
    
    public function systemMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->addChild('Einstellungen', array('route' => '_symbbcoresystembundle_config'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('BBCodes', array('route' => '_symbbcorebbcodebundle_bbcode_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        $menu->addChild('Topic Tags', array('route' => '_symbbcoreforumbundle_topictag_list'))->setExtra('translation_domain', 'symbb_acp_menu');
        return $menu;

    }
}