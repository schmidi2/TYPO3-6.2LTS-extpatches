<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Steve Ryan (stever@syntithenai.com)
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
 * Plugin 'RSS feed of content' for the 'stever_rsscontent' extension.
 *
 * @author      Steve Ryan <stever@syntithenai.com>
 */




class tx_steverrsscontent_pi1 extends tslib_pibase {
        var $prefixId = 'tx_steverrsscontent_pi1';            // Same as class name
        var $scriptRelPath = 'pi1/class.tx_steverrsscontent_pi1.php';   // Path to this script relative to the extension dir.
        var $extKey = 'stever_rsscontent';      // The extension key.
        var $pi_checkCHash = TRUE;
        var $debug=false;
        /**
         * [Put your description here]
         */
        function main($content,$conf)   {
        // hack to force conf which is not loading on tbv?
        // works fine at home on dev ??
        // I hate typo3 caching sometimes
        if (strlen($conf['singleViewPid'])==0) $conf['singleViewPid'] = 60;
        if (strlen($conf['url'])==0)$conf['url'] = 'http://thebegavalley.com/';
        if (strlen($conf['title'])==0)$conf['title'] = 'thebegavalley.com Recent Updates';
        if (strlen($conf['desc'])==0)$conf['desc'] = 'thebegavalley.com Recent Updates';
        if (strlen($conf['language'])==0)$conf['language'] = 'en';
        if (strlen($conf['copy'])==0)$conf['copy'] = 'copyleft';
        if (strlen($conf['category'])==0)$conf['category'] = 'blog';
        if (strlen($conf['rssfile'])==0)$conf['rssfile'] = 'recentupdates.xml';
       
        if (!$this->debug) {
                Header('Content-type: text/xml');
        } else {
                $GLOBALS['TYPO3_DB']->debugOutput=true;
                debug($conf);
                //exit;
               
        }
        $baseUrl = $conf['url'];

                // RSS-Plugin gibt nur Seiten unterhalb der im Ausgangspunkt gewählte(n) Seite(n) aus
                // Suchen der Ausgangspunkte
                $q = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pages','tt_content','pid = '.$GLOBALS["TSFE"]->id.' and hidden = 0','','sorting DESC','1');
                while ($r = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($q)) {
                        $subtreeRoots = $r['pages'];
                }
                // falls Root-Seite oder keine ausgewählt wurde:
                if (!$subtreeRoots || $subtreeRoots == 1)
                        $where_clause = '';
                // falls spezielle Unterbäume mittels Ausgangspunkt ausgewählt wurden
                else {
                        // alle Unterseiten holen
                        $pid_list = $this->pi_getPidList($subtreeRoots,$recursive=10); // alle Unterseiten suchen
                        $where_clause = 'uid in ('.$pid_list.') and uid not in ('.$subtreeRoots.') and ';
                }
               
                // nur sichtbare Seiten mit richtigem doktype usw. suchen
        $q = $GLOBALS['TYPO3_DB']->exec_SELECTquery('pid, uid, title, abstract, tstamp, crdate','pages',$where_clause.'hidden = 0 and nav_hide = 0','','crdate DESC',20);
            
         $items = '';
                 while ($r = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($q)) {
			// r: page - tbl pages
			// r2: page - tbl pages
			// r3: content element - tbl tt_content
                                       
                       // Title der Parent-Page holen         
                $q2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, title AS parent_title, SYS_LASTCHANGED','pages',$r['pid'].' = uid','','','');
                        $r2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($q2);

         // PROCESS BODY TEXT - REMOVE TAGS
                            $r['title'] = ereg_replace('</?[^>]*>','',$r['title']);
                            $r['title'] = htmlspecialchars_decode($r['title']);
                            $r['title'] = str_replace(' ',' ',$r['title']);
                            $r['title'] = str_replace('&',' ',$r['title']);
                            $r['title'] = str_replace("'",' ',$r['title']);
                                       
                            $r2['parent_title'] = ereg_replace('</?[^>]*>','',$r2['parent_title']);
                            $r2['parent_title'] = htmlspecialchars_decode($r2['parent_title']);
                            $r2['parent_title'] = str_replace(' ',' ',$r2['parent_title']);
                            $r2['parent_title'] = str_replace('&',' ',$r2['parent_title']);
                            $r2['parent_title'] = str_replace("'",' ',$r2['parent_title']);
                            


                            // Hole den Inhalt der Seite, alle Records
                            $q3 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('bodytext','tt_content','pid='. $r[uid].' AND deleted=0 AND hidden=0 AND bodytext != ""','','sorting',1);
                            while ($r3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($q3)) {
                                    $r['abstract']=$r3['bodytext'];
                                    $r['abstract'] = ereg_replace('</?[^>]*>','',$r['abstract']);
                                    $r['abstract'] = htmlspecialchars_decode($r['abstract']);
                                    $r['abstract'] = str_replace(' ',' ',$r['abstract']);
                                    $r['abstract'] = str_replace('&',' ',$r['abstract']);
                                    $r['abstract'] = str_replace("'",' ',$r['abstract']);
                            }

                            // Hole ZeitDatum der letzten Änderung der Page
/*                            $q3 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tstamp','tt_content','pid='. $r[uid].' AND deleted=0 AND hidden=0','','tstamp DESC',1);
                            while ($r3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($q3)) {
                                    $r['tstamp']=$r3['tstamp'];
                            }*/
                          
                                        // Link bauen
                                        $rdfName=$baseUrl.'index.php?id='.$r['uid'];           
                                                                               
                            // BUILD LIST OF ITEMS
                                        $items .= '<item>'."\n";
                            		$items .= '<title>'.$r['title'].'</title>'."\n";

                                       
                            $url = $baseUrl.str_replace('&','&',$this->cObj->typoLink_URL(Array('parameter'=>$conf['singleViewPid'],'additionalParams'=>'&tx_eeblog[showUid]='.$r['uid'])));
                            $items .= '<link>'.$rdfName.'</link>'."\n";
                            $items .= '<description>'.$this->cObj->crop($r['abstract'],'250|...|1').'</description>'."\n";
                            $items .= '<pubDate>'.date("r",$r['crdate']).'</pubDate>'."\n";
                            $items .= '</item>'."\n";
                                       
        }
               
        // STATIC BLOB AT HEADER / FOOTER
        $c = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>'.$conf['title'].'</title>
    <link>'.$conf['url'].'</link>
    <description>'.$conf['desc'].'</description>
    <language>'.$conf['language'].'</language>
    <copyright>'.$conf['copy'].'</copyright>
    <lastBuildDate>'.date("r").'</lastBuildDate>
'.$items.'
 </channel>
</rss>';

        $f = FOpen($conf['rssfile'],'wb');
        if ($f) {
            FWrite($f,$c);
            FClose($f);
        }

        echo $c;
        exit;
        }
        
        function enableFields($table) {
		if (count($GLOBALS['TCA'][$table])>0) {
			return $GLOBALS['TSFE']->sys_page->enableFields($table, 0);
		} else return '';	
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/stever_rsscontent/pi1/class.tx_steverrsscontent_pi1.php'])     {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/stever_rsscontent/pi1/class.tx_steverrsscontent_pi1.php']);
}

?>