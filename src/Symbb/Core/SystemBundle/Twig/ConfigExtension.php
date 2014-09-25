<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Twig;

class ConfigExtension extends \Twig_Extension
{

    /**
     *
     * @var \SymBB\Core\SystemBundle\Manager\ConfigManager
     */
    protected $configManager;

    public function __construct(\SymBB\Core\SystemBundle\Manager\ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbConfigManager', array($this, 'getSymbbConfigManager'))
        );
    }

    public function getSymbbConfigManager()
    {
        return $this->configManager;
    }

    public function getName()
    {
        return 'symbb_twig_config';
    }
}