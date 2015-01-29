<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Twig;


class TemplateExtension extends \Twig_Extension
{

    protected $newsManager;
    protected $categoryManager;

    public function __construct($newsManager, $categoryManager)
    {
        $this->newsManager = $newsManager;
        $this->categoryManager = $categoryManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbNewsManager', array($this, 'getSymbbNewsManager')),
            new \Twig_SimpleFunction('getSymbbNewsCategoryManager', array($this, 'getSymbbNewsCategoryManager')),
        );
    }


    public function getSymbbNewsCategoryManager(){
        return $this->categoryManager;
    }

    public function getSymbbNewsManager(){
        return $this->newsManager;
    }

    public function getName()
    {
        return 'symbb_news_twig_extensions';
    }
}