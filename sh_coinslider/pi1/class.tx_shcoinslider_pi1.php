<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Samuel Heinz <>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'jQuery Coin Slider' for the 'sh_coinslider' extension.
 *
 * @author	Samuel Heinz <>
 * @package	TYPO3
 * @subpackage	tx_shcoinslider
 */
class tx_shcoinslider_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_shcoinslider_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_shcoinslider_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'sh_coinslider';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;

		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm(); // Flexform initialisieren

		$sliderEffect = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'effect', 'tab1');
		$sliderNavi = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'navibuttons', 'tab1');
		$sliderWidth = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sliderwidth', 'tab1') !='' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sliderwidth', 'tab1') : $this->conf['sliderwidth']);
		$sliderHeight = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sliderheight', 'tab1') != '' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'sliderheight', 'tab1') : $this->conf['sliderheight']);
		$squeresperwidth = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'squeresperwidth', 'tab1') != '' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'squeresperwidth', 'tab1') : $this->conf['squeresperwidth']);
        $squeresperheight = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'squeresperheight', 'tab1') != '' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'squeresperheight', 'tab1') : $this->conf['squeresperheight']);
        $delayimages = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'delayimages', 'tab1') != '' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'delayimages', 'tab1') : $this->conf['delayimages']);
        $delaysquares = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'delaysquares', 'tab1') != '' ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'delaysquares', 'tab1') : $this->conf['delaysquares']);
		$storage = ($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'imagerecords', 'tab1') ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'imagerecords', 'tab1') : '1');

        $GLOBALS['TSFE']->additionalFooterData[$this->prefixId] .= '<script type="text/javascript" src="typo3conf/ext/sh_coinslider/js/coin-slider.min.js"></script>'."\n";
        $GLOBALS['TSFE']->additionalFooterData[$this->prefixId] .= '
			<script type="text/javascript">
			    jQuery.noConflict();
				jQuery(document).ready(function($) {
					$(\'#coinslider\').coinslider(
						{ effect: \''.$sliderEffect.'\', navigation: '.$sliderNavi.', width: '.$sliderWidth.', height: '.$sliderHeight.', spw: '.$squeresperwidth.', sph: '.$squeresperheight.', delay: '.$delayimages.', sDelay: '.$delaysquares.', hoverPause: true }
					);
				});
			</script>'."\n";
		
		$images = $this->model_getImages($storage);
		$content = $this->view_renderSlider($images,$sliderWidth,$sliderHeight);
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	function model_getImages($storage){
		$images = array();

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_shcoinslider_images',
			'pid ='.$storage.''.$this->cObj->enableFields('tx_shcoinslider_images'),
			'',
			'sorting',
			''
		);

		if ($GLOBALS["TYPO3_DB"]->sql_num_rows($res)>0) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$images[]=$row;
			}
		}
		return $images;
	}
	
	function view_renderSlider($images,$sliderWidth,$sliderHeight){

		$content = '
			<div id="coinslider">';
		foreach($images as $image){
			$imgconf['file'] = 'uploads/tx_shcoinslider/'.$image["image"];
			$imgconf['file.']['width'] = ''.$sliderWidth.'c';
			$imgconf['file.']['height'] = ''.$sliderHeight.'c-100';
			$imgconf['altText'] = $image["title"];
			$imgconf['titleText'] = $image["title"];
			$imgTag = $this->cObj->cObjGetSingle('IMAGE', $imgconf);

			$content .='
				<a href="'.htmlspecialchars($this->pi_linkTP_keepPIvars_url(array(),1,1,$image['link'])).'">
					'.$imgTag.'
					<span><b>'.$image['description'].'</b></span>
				</a>';
		}
		$content .='
			</div>';
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sh_coinslider/pi1/class.tx_shcoinslider_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sh_coinslider/pi1/class.tx_shcoinslider_pi1.php']);
}

?>
