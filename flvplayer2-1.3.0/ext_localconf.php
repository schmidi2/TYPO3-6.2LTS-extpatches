<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
	// Add plugin
	t3lib_extMgm::addPItoST43('flvplayer','pi1/class.tx_flvplayer2_pi1.php','_pi1','list_type',0);
?>
