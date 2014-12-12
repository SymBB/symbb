<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddForumFieldSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SUBMIT => 'preSubmit', FormEvents::PRE_SET_DATA => 'preSetData');

    }

    /**
     * befor form data bind
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data && $data->getType() == 'link') {
            $this->addLinkFields($form);
        }
        else if ($data && $data->getType() == 'forum') {
            $this->addForumFields($form);
        }
        else if ($data && $data->getType() == 'category') {
            $this->addForumFields($form);
        }

    }

    /**
     * after form data bind
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data && ($data['type'] == 'forum')) {
            $this->addForumFields($form);
        }
        else if ($data && ($data['type'] == 'category')) {
            $this->addCategoryFields($form);
        }
        else if ($data && ($data['type'] == 'link')) {
            $this->addLinkFields($form);
        }

    }

    protected function addLinkFields($form, $hidden = false)
    {
        $type = null;
        if ($hidden) {
            $type = 'hidden';
        }
        $form->add('link', $type);
        $form->add('countLinkCalls', $type);
        $form->add('description');
        $form->remove('showSubForumList');
        $form->remove('entriesPerPage');

    }

    protected function addForumFields($form, $hidden = false)
    {
        $type = null;
        if ($hidden) {
            $type = 'hidden';
        }
        $form->add('description', $type);
        $form->add('showSubForumList', $type);
        $form->add('entriesPerPage', $type);

        $form->remove('link');
        $form->remove('countLinkCalls');

    }

    protected function addCategoryFields($form, $hidden = false)
    {
        $type = null;
        if ($hidden) {
            $type = 'hidden';
        }
        $form->add('description', $type);
        $form->add('entriesPerPage', $type);

        $form->remove('link');
        $form->remove('countLinkCalls');

    }
}