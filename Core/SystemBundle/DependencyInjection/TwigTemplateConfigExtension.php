<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\DependencyInjection;

class TwigTemplateConfigExtension extends \Twig_Extension
{
    /**
     *
     * @var \SymBB\Core\SystemBundle\DependencyInjection\ConfigManager 
     */
    protected $configManager;

    public function __construct($container) {
        $this->configManager   = $container->get('symbb.core.config.manager');
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbTemplateConfig', array($this, 'getSymbbTemplateConfig'))
        );
    }
    
    public function getSymbbTemplateConfig($config)
    {
        return $this->configManager->get('template.'.$config);
    }

    public function getName()
    {
        return 'symbb_config_template';
    }
}