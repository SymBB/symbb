<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\BBCodeBundle\EventListener;

use \SymBB\Extension\BBCodeBundle\Form\Type\BBEditorType;

class FormListener
{
    
    public function getPostFormType(\SymBB\Core\EventBundle\Event\FormPostEvent $event)
    {
       $builder = $event->getBuilder();
       $builder->add('text', new BBEditorType(), array('attr' => array(
           'placeholder' => $event->getTranslator()->trans('Give Your text here', array(), 'symbb_frontend')
           )));
    }
    
    public function getTopicFormType(\SymBB\Core\EventBundle\Event\FormTopicEvent $event)
    {
       //$builder = $event->getBuilder();
       //$builder->add('text', new BBEditorType(), array('attr' => array('class' => 'symbb_bbcode_editor_textarea')));
    }
    
}