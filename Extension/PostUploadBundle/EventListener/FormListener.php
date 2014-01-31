<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\PostUploadBundle\EventListener;

use \SymBB\Extension\PostUploadBundle\Form\Type\FileType;

class FormListener
{

    public function addPostFormPart($event)
    {
        $builder = $event->getBuilder();
        $builder->add('files', 'collection', array(
            'type'          => new FileType(),
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference' => false,
        ));
    }
}