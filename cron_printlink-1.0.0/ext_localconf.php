<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_cronprintlink_pi1.php', '_pi1', '', 1);
t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_cronprintlink_pi2.php', '_pi2', '', 1);
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'Printlink Setup');

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][] = 'EXT:cron_printlink/class.tx_cronprintlink_utils.php:tx_cronprintlink_utils->contentPostProc';

?>