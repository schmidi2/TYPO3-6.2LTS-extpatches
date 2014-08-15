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
 * Utils for the 'cron_printlink' extension.
 *
 * @author	Ernesto Baschny <ernst@cron-it.de>
 */

class tx_cronprintlink_utils {

	function add_vars2($vars,$path) {
		$res = '';
		foreach ($vars as $key => $val) {
			if (!is_array($val)) {
				$res .= '&'.$path.'['.rawurlencode($key).']'.'='.rawurlencode($val);
			} else {
				$res .= tx_cronprintlink_utils::add_vars2($val, $path.'['.rawurlencode($key).']');
			}
		}
		return $res;
	}

	function add_vars($vars, $conf=array()) {
		$res = '';
		// These are set by linkData(), if needed:
		$ignore = $GLOBALS['TSFE']->config['config']['linkVars'] . ',no_cache,id,type'.($conf['ignore_post_vars'] != '' ? ','.$conf['ignore_post_vars'] : '');
		// Get variables to add to our URL
		foreach ($vars as $key => $val) {
			if (is_array($val)) {
				$res .= tx_cronprintlink_utils::add_vars2($val, rawurlencode($key));
			} else {
				if (t3lib_div::inList($ignore, $key)) {
					continue;
				}
				$res .= '&' . rawurlencode($key) . '=' . rawurlencode($val);
			}
		}
		return $res;
	}

	function getCurrentUrl($conf) {
		$params = tx_cronprintlink_utils::add_vars($_GET);
		if ($conf['include_post_vars']) {
			$params .= tx_cronprintlink_utils::add_vars($_POST, $conf);
		}
		$type = $conf['type'];
		if ($conf['printParam']) {
			$type = '';
			$params .= '&' . $conf['printParam'];
		}
		$link = $GLOBALS['TSFE']->tmpl->linkData($GLOBALS['TSFE']->page, '', $GLOBALS['TSFE']->no_cache, '', '', $params, $type);
		return t3lib_div::locationHeaderUrl($link['totalURL']);
	}

	/**
	 * Post process content to generate footer links
	 *
	 * @param string $content
	 */
	function contentPostProc(&$params) {
		$conf = $params['pObj']->config['config'];
		if (!$conf['footerUrls']) {
			return; 
		}
		$conf = $conf['footerUrls.'];
		list($prependTemplate, $appendTemplate) = t3lib_div::trimExplode('|', $conf['linkWrap']); 
		$content = $params['pObj']->content;
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['cron_printlink']);
		$result = '';
		$urls = array();
		// Collect all links on this page
		while (preg_match('/(<a[^>]*href=\"(.*)\"[^>]*>.*<\/a>)/siU', $content, $match)) {
			$tag = $match[0];
			$url = $match[2];
			
			$cont = explode($tag, $content, 2);
			$before = $cont[0];
			$after = $cont[1];
			
			// Clean up url, make it fully qualified
			if (preg_match('/#$/', $url)) {
				$url = '';
			} elseif (preg_match('/^javascript:/', $url)) {
				$url = '';
			} else {
				$url = t3lib_div::locationHeaderUrl($url);
			}

			if ($url) {
				// Mark this url for the footnote
				if (!isset($urls[$url])) {
					$urls[$url] = 'FOOTER'.md5(rand());
				}
				$uniqueId = $urls[$url];
				$prepend = str_replace('###NUMBER###', $uniqueId, $prependTemplate);
				$append = str_replace('###NUMBER###', $uniqueId, $appendTemplate);
				$result .= $before . $prepend . $tag . $append;
			} else {
				// No marking for this url
				$result .= $before . $tag;
			}
			$content = $after;
		}
		if (empty($urls)) {
			// No urls, nothing to do
			return;
		}
		// Remaining content:
		$result .= $content;
		
		$i = 0;
		foreach ($urls as $url => $uniqueId) {
			$i++;
			$result = str_replace($uniqueId, $i, $result);
		}

		$footerWrap = $conf['footerWrap'] ? $conf['footerWrap'] : '<ol>|</ol>';
		list ($prepend, $append) = t3lib_div::trimExplode('|', $footerWrap);
		$footer = $prepend;
		$i = 0;
		$footerEntryTemplate = $conf['footerEntryTemplate'] ? $conf['footerEntryTemplate'] : '<li>###URL###</li>';
		foreach ($urls as $url => $uniqueId) {
			$i++;
			$item = str_replace('###NUMBER###', $i, $footerEntryTemplate);
			$item = str_replace('###URL###', $url, $item);
			$footer .= $item;
		}
		$footer .= $append;

		$result = str_replace('</body>', $footer.'</body>', $result);
		$params['pObj']->content = $result;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cron_printlink/class.tx_cronprintlink_utils.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cron_printlink/class.tx_cronprintlink_utils.php']);
}

?>