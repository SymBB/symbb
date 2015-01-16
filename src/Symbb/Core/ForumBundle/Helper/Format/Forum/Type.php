<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Helper\Format\Forum;

class Type
{

    protected $translator;
    static protected $array;

    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    public function getArray()
    {
        if (self::$array === null) {
            self::$array = array(
                'forum' => $this->translator->trans('Forum', array(), 'options'),
                'link' => $this->translator->trans('Link', array(), 'options'),
                'category' => $this->translator->trans('Kategorie', array(), 'options')
            );
        }
        return self::$array;
    }

    public function format($value)
    {
        $array = $this->getArray();
        if (isset($array[$value])) {
            return $array[$value];
        } else {
            return $this->translator->trans('Unbekannt', array(), 'options');
        }
    }

}