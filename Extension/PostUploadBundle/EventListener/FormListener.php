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
    
    protected $vichUploadHelper;
    protected $em;
    
    public function __construct($vichUploadHelper, $em)
    {
        $this->vichUploadHelper = $vichUploadHelper;
        $this->em = $em;
    }

    public function addPostFormPart($event)
    {
        $builder = $event->getBuilder();
        $builder->add('files', 'collection', array(
            'type' => new FileType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ));
    }

    public function afterSave(\SymBB\Core\EventBundle\Event\EditPostEvent $event)
    {

        $post = $event->getPost();
        $text = $post->getText();
        $matches = array();
        preg_match_all('/#IMG#([0-9]+)/', $text, $matches);
        $files = $post->getFiles();
        foreach ($matches[1] as $imageNumber) {
            if (isset($files[$imageNumber])) {
                $url = $this->vichUploadHelper->asset($files[$imageNumber], 'image');
                $text = \str_replace('#IMG#' . $imageNumber . '#', $url, $text);
            }
        }
        $post->setText($text);
        $this->em->persist($post);
        $this->em->flush();
    }
}