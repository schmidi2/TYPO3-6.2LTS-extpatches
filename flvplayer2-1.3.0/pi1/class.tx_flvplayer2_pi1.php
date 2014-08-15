<?php
	/***************************************************************
	 * Copyright notice
	 *
	 * (c) 2004 macmade.net
	 * All rights reserved
	 *
	 * This script is part of the TYPO3 project. The TYPO3 project is 
	  * free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * The GNU General Public License can be found at
	 * http://www.gnu.org/copyleft/gpl.html.
	 *
	 * This script is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/
	
	/** 
	 * Plugin 'FLV Player' for the 'flvplayer2' extension.
	 *
	 * @author		Jean-David Gadina (macmade@gadlab.net)
	 * @version		1.0
	 */
	
	
	// Typo3 FE plugin class
	require_once(PATH_tslib.'class.tslib_pibase.php');
	
	// Developer API class
	require_once(t3lib_extMgm::extPath('api_macmade').'class.tx_apimacmade.php');
	
	class tx_flvplayer2_pi1 extends tslib_pibase {
		
				
		
		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/
		
		// Same as class name
		var $prefixId = 'tx_flvplayer2_pi1';
		
		// Path to this script relative to the extension dir
		var $scriptRelPath = 'pi1/class.tx_flvplayer2_pi1.php';
		
		// The extension key
		var $extKey = 'flvplayer2';
		
		// Upload directory
		var $uploadDir = 'uploads/tx_flvplayer/';
		
		// Version of the Developer API required
		var $apimacmade_version = 1.9;
		
		
		
		
		
		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/
		
		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_flvplayer2_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			setConfig
		 * @see			buildFlashCode
		 */
		function main($content,$conf) {
						
			// New instance of the macmade.net API
			$this->api = new tx_apimacmade($this);
			
			// Set class confArray TS from the function
			$this->conf = $conf;
			
			// Init flexform configuration of the plugin
			$this->pi_initPIflexForm();
			
			// Store flexform informations
			$this->piFlexForm = $this->cObj->data['pi_flexform'];
			
			// Set final configuration (TS or FF)
			$this->setConfig();
			
			// Build content
			if($this->conf['useFlowPlayer']){
				$content = $this->buildFlashCodeFlowplayer();
			} else {
				$content = $this->buildFlashCode();
			}
			
			// Return content
			return $this->pi_wrapInBaseClass($content);
		}
		
		/**
		 * Set configuration array.
		 * 
		 * This function is used to set the final configuration array of the
		 * plugin, by providing a mapping array between the TS & the flexform
		 * configuration.
		 * 
		 * @return		Void
		 */
		function setConfig() {
			
			// Mapping array for PI flexform
			$flex2conf = array(
				'url' => 'sDEF:url',
				'file' => 'sDEF:file',
				'image' => 'sDEF:image',
				'splashImageMode' => 'sDEF:splashImageMode',
				'playerParams.' => array(
					'autoStart' => 'sPLAYER:autostart',
					'fullScreen' => 'sPLAYER:fullscreen',
					'controlbar' => 'sPLAYER:controlbar',
				),
				'width' => 'sFLASH:width',
				'height' => 'sFLASH:height',
				'version' => 'sFLASH:version',
			);
			
			// Ovverride TS setup with flexform
			$this->conf = $this->api->fe_mergeTSconfFlex($flex2conf,$this->conf,$this->piFlexForm);
			
			// DEBUG ONLY - Output configuration array
			#$this->api->debug($this->conf,'FLV Player: configuration array');
		}
		
		/**
		 * Returns the code for the flash file.
		 * 
		 * This function creates a flash plugin object.
		 * 
		 * @return		The complete HTML code used to display the flash file.
		 * @see			writeFlashObjectParams
		 */
		function buildFlashCode() {
			
			// Creating valid pathes for the MP3 player
			$swfPath = str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->conf['flvplayer']));
			
			// Autostart 
			$autoStart = ($this->conf['playerParams.']['autoStart']) ? 'true' : 'false';
			
			// Allow fullscreen mode
			$fullScreen = ($this->conf['playerParams.']['fullScreen']) ? 'true' : 'false';
			
			if($this->conf['url']){
				$filePath =	$this->cObj->stdWrap($this->conf['url'],$this->conf['url.']);
			} else {
				
				// File path
				$filePath = t3lib_div::getIndpEnv('TYPO3_SITE_URL').str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->uploadDir . $this->conf['file']));
				$filePath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'),'',$filePath);
			}
						
			$extPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL').str_replace(PATH_site,'',t3lib_extMgm::extPath('flvplayer2'));
			$extPath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'),'',$extPath);
			
			// Storage
			$htmlCode = array();
			
			// Include Adobe Flash Player Version Detection
			$GLOBALS['TSFE']->additionalHeaderData [$this->pi1->prefixId] = '<script type="text/JavaScript" src="'.t3lib_extMgm::siteRelPath("flvplayer2").'pi1/AC_OETags.js"></script>';
	
			// Allow <params> set from TS
			$paramsString = "";
			if(is_array($this->conf['swfParams.'])){
				foreach($this->conf['swfParams.'] as $name => $value) {
					$paramsString .= '"' . $name . '", "' . $value . '",';
				}
			}
			
			if($this->conf['base']){
				$base = $this->conf['base'];
			}  else {
				$base = t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR');
			}
			
			// Create the flash stuff
			$htmlCode[] .= '
				<script type="text/javascript">
					/*<![CDATA[*/
				<!--
					var hasRightVersion = DetectFlashVer('.$this->conf['version'].', 0, 0);
					if (hasRightVersion) {  // if we\'ve detected an acceptable version
						AC_FL_RunContent(
							"movie", "'.$extPath.'pi1/mediaplayer",
							"width", "'.$this->conf['width'].'",
							"height", "'.$this->conf['height'].'",
							"quality", "high",
							"base", "'.$base.'",
							"flashvars","width='.$this->conf['width']
								.'&height='.$this->conf['height']
								.'&file='.$filePath
								.'&autostart='.$autoStart
								.'&image='.$this->getSplashImageUrl()
								.'&controlbar='.$this->conf['playerParams.']['controlbar']
								.'&fullscreen='.$fullScreen.'",
							"allowScriptAccess","always",
							"allowfullscreen","'.$fullScreen.'",
							"type", "application/x-shockwave-flash",
							"codebase", "http'.(t3lib_div::getIndpEnv('TYPO3_SSL')?'s':'').'://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab",
							'.$paramsString.'
							"pluginspage", "http://www.adobe.com/go/getflashplayer"
						);
					} else {  // flash is too old or we can\'t detect the plugin
						document.write("You need to upgrade or install your Flash Player installation!");
					}
				// -->
					/*]]>*/
				</script>
				<noscript><p>You need to install Flash Player!</p></noscript>
			';

			// Return content
			return implode(chr(10),$htmlCode);
		}
		
		function buildFlashCodeFlowplayer(){
			
			$uid = $this->cObj->data['uid'];
						
			$jsUrl = t3lib_extMgm::siteRelPath('flvplayer2').'pi1/flowplayer-3.1.4.min.js';
			$swfUrl = t3lib_extMgm::siteRelPath('flvplayer2').'pi1/flowplayer-3.1.5.swf';
			$GLOBALS['TSFE']->additionalHeaderData['flvplayer2-flowplayer'] = '<script type="text/javascript" src="'.$jsUrl.'"></script>';
			
			/**
			 * Getting the config..
			 */
			// Autostart 
			$autoStart = ($this->conf['playerParams.']['autoStart']) ? 'true' : 'false';
			
			// Allow fullscreen mode
			$fullScreen = ($this->conf['playerParams.']['fullScreen']) ? 'true' : 'false';
			
			if($this->conf['url']){
				$filePath =	$this->cObj->stdWrap($this->conf['url'],$this->conf['url.']);
			} else {
				// File path
				$filePath = t3lib_div::getIndpEnv('TYPO3_SITE_URL').str_replace(PATH_site,'',t3lib_div::getFileAbsFileName($this->uploadDir . $this->conf['file']));
				$filePath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'),'',$filePath);
			}			
			
			$autoBuffering = 'true';
						
			// Create the flash stuff
			if($this->conf['image']){
				$splashImgUrl = $this->getSplashImageUrl();
				$splashImg = '
				    canvas: { 
				        backgroundImage: "url('.$splashImgUrl.')",
				        backgroundGradient: "none",
				    }, 					
				';
				$autoBuffering = 'false';
			} else {
				$splashImg = "";
			}
	
			
			$htmlCode[]  = '
			<a 
				 href="'.$filePath.'"  
				 style="display:block;width:'.$this->conf['width'].'px;height:'.$this->conf['height'].'px;"  
				 id="flowplayer-'.$uid.'"> 
			</a>
			<script type="text/javascript">
			/*<![CDATA[*/
				flowplayer("flowplayer-'.$uid.'", "'.$swfUrl.'",  { 
					'.$splashImg.'
				    clip: { 
				         
				        // these two configuration variables does the trick 
				        autoPlay: '.$autoStart.',  
				        autoBuffering: '.$autoBuffering.' // <- do not place a comma here   
				    }
			
				});
			/*]]>*/	
			</script>';
			
			return implode(chr(10),$htmlCode);
		}		
		
		
		function getSplashImageUrl(){
			
			$local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.

			if ($this->conf['splashImageMode']) {
				switch ($this->conf['splashImageMode']) {
					case 'resize2max' :
						$suf = 'm';
						break;
					case 'crop' :
						$suf = 'c';
						break;
					case 'resize' :
						$suf = '';
						break;
				}
			}			
			
			$lConf = array(
				'image.' => array(
					'file' => 'uploads/tx_flvplayer/'.$this->conf['image'],
					'file.' => array(
						'width' => $this->conf['width'].$suf,
						'height' => $this->conf['height']			
					)

				)
			);

			return $local_cObj->IMG_RESOURCE($lConf['image.']);			
		}

	}
	
	/**
	 * XCLASS inclusion
	 */
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flvplayer2/pi1/class.tx_flvplayer2_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/flvplayer2/pi1/class.tx_flvplayer2_pi1.php']);
	}
?>
