<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik_Plugins
 * @package CustomOptOut
 */
namespace Piwik\Plugins\CustomOptOut;
use Piwik\Common;
use Piwik\Db;
use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;

/**
 * @package CustomOptOut
 */
class CustomOptOut extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'Menu.Admin.addItems' => 'addMenuItems',
        );
    }

    public function addMenuItems() {

    // Piwik >= 2.1
    if(method_exists('Piwik\Piwik', 'hasUserSuperUserAccess')) {
        $superUserAccess = Piwik::hasUserSuperUserAccess();

        // Piwik < 2.1
    } else {
        $superUserAccess = Piwik::isUserIsSuperUser();
    }

        MenuAdmin::getInstance()->add(
            'General_Settings',
            'Custom Opt-Out',
            array('module' => 'CustomOptOut', 'action' => 'index'),
            $superUserAccess,
            $order = 6
        );
    }

    public function install() {
        try {
            $sql = sprintf('ALTER TABLE %s
	                ADD COLUMN `custom_css` TEXT NULL AFTER `keep_url_fragment`,
	                ADD COLUMN `custom_css_file` VARCHAR(255) NULL AFTER `custom_css`;', Common::prefixTable('site'));

            Db::exec($sql);
        } catch(\Exception $exp) {
            if(!Db::get()->isErrNo($exp, '1060')) {
                throw $exp;
            }
        }
    }

    public function uninstall() {
        try {
            $sql = sprintf('ALTER TABLE %s
	                  DROP COLUMN `custom_css`,
	                  DROP COLUMN `custom_css_file`;', Common::prefixTable('site'));

            Db::exec($sql);
        } catch(\Exception $exp) {
            if(!Db::get()->isErrNo($exp, '1091')) {
                throw $exp;
            }
        }
    }
}
