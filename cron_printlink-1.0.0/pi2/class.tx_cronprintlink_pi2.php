<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005 Ernesto Baschny (ernst@cron-it.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * Plugin 'URL for print version' for the 'cron_printlink' extension.
 *
 * @author	Ernesto Baschny <ernst@cron-it.de>
 */


require_once(PATH_tslib."class.tslib_pibase.php");
require_once(t3lib_extMgm::extPath('cron_printlink') . 'class.tx_cronprintlink_utils.php');

class tx_cronprintlink_pi2 extends tslib_pibase {
	var $prefixId = "tx_cronprintlink_pi2";		// Same as class name
	var $scriptRelPath = "pi2/class.tx_cronprintlink_pi2.php";	// Path to this script relative to the extension dir.
	var $extKey = "cron_printlink";	// The extension key.
	
	/**
	 * Renders a nice "URL" that is included in the print version
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_loadLL();
		// Prefix
		if ($conf['prefix.'] && $conf['prefix']) {
			$prefix = $this->cObj->cObjGetSingle($conf['prefix'], $conf['prefix.']);
		} elseif (!$conf['noPrefix']) {
			$prefix = $this->pi_getLL('home_at') . ': ';
		}
		if ($conf['stdWrapPrefix.']) {
			$prefix = $this->cObj->stdWrap($prefix, $conf['stdWrapPrefix.']);
		}

		// Suffix
		if ($conf['suffix.'] && $conf['suffix']) {
			$suffix = $this->cObj->cObjGetSingle($conf['suffix'], $conf['suffix.']);
		}

		// URL
		$url = urldecode(tx_cronprintlink_utils::getCurrentUrl($conf));
		if ($conf['stdWrapURL.']) {
			$url = $this->cObj->stdWrap($url, $conf['stdWrapURL.']);
		}

		// Put it all together
		$content = $prefix . $url . $suffix;
		if ($conf['stdWrap.']) {
			$content = $this->cObj->stdWrap($content, $conf['stdWrap.']);
		}
		return $content;
	}
}


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/cron_printlink/pi2/class.tx_cronprintlink_pi2.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/cron_printlink/pi2/class.tx_cronprintlink_pi2.php"]);
}

?>