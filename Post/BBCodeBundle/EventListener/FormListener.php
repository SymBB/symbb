<?php
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Post\BBCodeBundle\EventListener;

use \SymBB\Post\BBCodeBundle\Form\Type\BBEditorType;

class FormListener
{
    
    public function getPostFormType(\SymBB\Core\EventBundle\Event\PostFormEvent $event)
    {
       $builder = $event->getBuilder();
       $builder->add('text', new BBEditorType(), array('attr' => array('class' => 'symbb_bbcode_editor_textarea')));
    }
    
    public function getTopicFormType(\SymBB\Core\EventBundle\Event\PostFormEvent $event)
    {
       $builder = $event->getBuilder();
       $builder->add('text', new BBEditorType(), array('attr' => array('class' => 'symbb_bbcode_editor_textarea')));
    }
    
}