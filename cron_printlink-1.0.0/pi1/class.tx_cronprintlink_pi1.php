<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Jens Ellerbrock <je@hades.org>
*      javascript stuff by Dominic Brander <dbrander@snowflake.ch>
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
 * Plugin 'Print Link' for the 'cron_printlink' extension.
 *
*  @authors     Jens Ellerbrock <je@hades.org>, Dominic Brander <dbrander@snowflake.ch>
 * @author	Ernesto Baschny <ernst@cron-it.de>
 */

require_once(PATH_tslib."class.tslib_pibase.php");
require_once(t3lib_extMgm::extPath('cron_printlink') . 'class.tx_cronprintlink_utils.php');

class tx_cronprintlink_pi1 extends tslib_pibase {
	var $prefixId = "tx_cronprintlink_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_cronprintlink_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "cron_printlink";	// The extension key.

	/**
	 * Renders a link to the print version of the current page.
	 *
	 */
	function main($content,$conf)	{
		$this->conf = $conf;
		$this->pi_loadLL();

		$conf['type'] = (isset($conf['type'])) ? $conf['type'] : 98;
		$conf['printParam'] = ($conf['printParam']) ? $conf['printParam'] : '';
		$uri = tx_cronprintlink_utils::getCurrentUrl($conf);
		$uri = htmlspecialchars($uri);

		$target = ($conf['target']) ? ' target="'.$conf['target'].'"' : '';
		$atags = '';
		if ($conf['ATagParams.']) {
			$atags = ' ' . $this->cObj->stdWrap($conf['ATagParams'], $conf['ATagParams.']);
		} else {
			// Backwards compatibility:
			$atags = $conf['aTagParams'] ? ' ' . $conf['aTagParams'] . ' ' : '';	
			$atags = $conf['ATagParams'] ? ' ' . $conf['ATagParams'] . ' ' : '';	
		}
		$js = $conf['noBlur'] ? '' : ' onfocus="blurLink(this);"';

		if ($conf['popup']) {
			$conf['popupWindowname'] = ($conf['popupWindowname']) ? $conf['popupWindowname'] : 'print';
			$conf['popupWindowparams'] = ($conf['popupWindowparams']) ? $conf['popupWindowparams'] : 'resizable=yes,toolbar=yes,scrollbars=yes,menubar=yes,width=500,height=500';
			$js .= ' onclick="window.open(\''.$uri.'\',\''.$conf['popupWindowname'].'\',\''.$conf['popupWindowparams'].'\'); return false;"';
			if ($conf['popupHref'])	{
				$target = '';
				$uri = $conf['popupHref'];
			}
		}
		// User provided linkContent, instead of default text:
		if ($conf['linkContent'] && $conf['linkContent.']) {
			$content = $this->cObj->cObjGetSingle($conf['linkContent'], $conf['linkContent.']);
		}
		// Defaults to our translated "Print" text
		if (!$content) {
			$content = $this->pi_getLL('print_version');
		}
		if ($conf['stdWrapContent.']) {
			$content = $this->cObj->stdWrap($content, $conf['stdWrapContent.']);
		}
		$link = '<a href="' . $uri . '" ' . $js . $target . $atags . '>' . $content . '</a>';
		if ($conf['stdWrap.']) {
			$link = $this->cObj->stdWrap($link, $conf['stdWrap.']);
		}
		return $link;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/cron_printlink/pi1/class.tx_cronprintlink_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/cron_printlink/pi1/class.tx_cronprintlink_pi1.php"]);
}

?>
