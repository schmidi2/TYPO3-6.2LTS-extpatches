<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:sh_coinslider/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_shcoinslider_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_shcoinslider_pi1_wizicon.php';
}

$TCA['tx_shcoinslider_images'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:sh_coinslider/locallang_db.xml:tx_shcoinslider_images',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_shcoinslider_images.gif',
	),
);
if (is_file(t3lib_extMgm::extPath($_EXTKEY).'ext_tables_advanced.php')) include t3lib_extMgm::extPath($_EXTKEY).'ext_tables_advanced.php';

?>