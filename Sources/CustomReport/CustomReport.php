<?php

/**
* @package manifest file for Custom Report Mod
* @author Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111), original author
* @author Francisco "d3vcho" Domínguez (https://www.simplemachines.org/community/index.php?action=profile;u=422971)
* @copyright Copyright (c) 2019, Francisco Domínguez
* @version 2.0.4
* @license http://www.mozilla.org/MPL/MPL-1.1.html
*/

/*
* Version: MPL 1.1
*
* The contents of this file are subject to the Mozilla Public License Version
* 1.1 (the "License"); you may not use this file except in compliance with
* the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS" basis,
* WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* for the specific language governing rights and limitations under the
* License.
*
* The Initial Developer of the Original Code is
*  Joker (http://www.simplemachines.org/community/index.php?action=profile;u=226111)
* Portions created by the Initial Developer are Copyright (C) 2012
* the Initial Developer. All Rights Reserved.
*
* Contributor(s):
*
*/

if (!defined('SMF'))
	die('Hacking attempt...');

class CustomReport {
	protected static $instance;

	public static $CustomReportAdmin;
	public static $CustomReportRouter;
	public static $CustomReportCore;

	public static $sourceFolder = '/CustomReport/';

	/**
	 * Singleton method
	 *
	 * @return CustomReport
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new CustomReport();
			self::loadClass('CustomReportCore');
			loadLanguage('CustomReport');
		}
		return self::$instance;
	}

	public function __construct() {}

	/**
	 * @param string $className
	 */
	public static function loadClass($className) {
		global $sourcedir;

		switch($className) {
			case 'CustomReportAdmin':
				if (self::$CustomReportAdmin === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$CustomReportAdmin = new CustomReportAdmin();
				}
				break;

			case 'CustomReportRouter':
				if (self::$CustomReportRouter === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$CustomReportRouter = new CustomReportRouter();
				}
				break;

			case 'CustomReportDB':
				require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
				break;


			case 'CustomReportCore':
				if (self::$CustomReportCore === null) {
					require_once ($sourcedir . self::$sourceFolder . '/' . $className . '.php');
					self::$CustomReportCore = new CustomReportCore();
				}
				break;

			default:
				break;
		}
	}

	public static function addActionContext(&$actions) {
		self::loadClass('CustomReportRouter');
		$actions['report_solved'] = array(self::$sourceFolder . 'CustomReportRouter.php', 'CustomReportRouter::reportSolved');
	}

	public static function addAdminPanel(&$admin_areas) {
		global $txt;

		$admin_areas['config']['areas']['customreport'] = array(
			'label' => $txt['cr_admin_panel_title'],
			'file' => '/CustomReport/CustomReportRouter.php',
			'function' => 'routeCustomReportAdmin',
			'icon' => 'administration.gif',
			'subsections' => array(),
		);
	}

	public static function customReportOb($buffer) {
		global $modSettings, $context;

		if (!empty($modSettings['cr_enable_mod']) && !empty($modSettings['cr_enable_large_report_field'])) {
			$buffer = preg_replace('~(' . preg_quote('<input type="text" id="report_comment" name="comment" size="50" value="" maxlength="255" />') . ')~', '<textarea id="report_comment" name="comment" cols="60" rows="6" tabindex="'. $context['tabindex']++ . '" value=""></textarea>', $buffer);
		}
		return $buffer;
	}
}
CustomReport::getInstance();

?>
