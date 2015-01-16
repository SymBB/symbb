<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */
namespace Symbb\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BaseTemplateEvent extends Event
{

    protected $env;
    protected $form;
    protected $html = '';


    public function __construct($env, $form = null)
    {
        $this->env = $env;
        $this->form = $form;
    }

    public function render($templateName, $params)
    {
        $html = $this->env->render(
            $templateName,
            $params
        );
        $this->html .= $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getForm()
    {
        return $this->form;
    }
}
