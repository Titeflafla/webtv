<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");

global $nuked, $language, $user;
translate('modules/Web_tv/lang/'. $language .'.lang.php');
include('modules/Web_tv/config.php');

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1) {

    	compteur('Web_tv');

        echo '<script type="text/javascript" src="modules/Web_tv/web_tv.js"></script>';

    	function index() {
    		global $bgcolor1, $nuked, $visiteur;

    		opentable();

                if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
                $sql_nbtv = mysql_query("SELECT id FROM ". WEB_TV);
        	$nb_tv = mysql_num_rows($sql_nbtv);

        	$nb_max_tv = 9;

                echo '<br /><div class="webtv_title">Liste des Web TV du site '. $nuked['name'] .'</div><br />';

                $start = $_REQUEST['p'] * $nb_max_tv - $nb_max_tv;
                if ($nb_tv > $nb_max_tv) {                	echo "<table class=\"g2_cadre_table_page\" style=\"background: ". $bgcolor1 .";\" cellspacing=\"5\" cellpadding=\"5\">"
            		. "<tr><td>";
                	number($nb_tv, $nb_max_tv, 'index.php?file=Web_tv');
                	echo "</td></tr></table>";
                }

                echo "<table style=\"width:98%;margin:auto;\" cellspacing=\"10\" cellpadding=\"10\">";
                $test = 0;

                $sql_all_tv = mysql_query("SELECT id, nom, statut, description FROM ". WEB_TV ." ORDER BY id asc LIMIT ". $start .", ". $nb_max_tv);
                while ($r_sql = mysql_fetch_array($sql_all_tv, MYSQL_ASSOC)) {
                	$test++;
                	if ($test == 1) echo "<tr>";

                	/*if($visiteur >= 9) {
                		if ($r_sql['statut'] == 'off') $img_tv = '<a href="index.php?file=Web_tv&amp;nuked_nude=admin&amp;page=admin&amp;op=active_tv&amp;id='. $r_sql['id'] .'" title="Stream On"><img src="modules/Web_tv/images/bullet_'. $r_sql['statut'] .'.png" style="vertical-align:middle;" alt="" /></a>';
                		else $img_tv = '<a href="index.php?file=Web_tv&amp;nuked_nude=admin&amp;page=admin&amp;op=desactive_tv&amp;id='. $r_sql['id'] .'" title="Stream Off"><img src="modules/Web_tv/images/bullet_'. $r_sql['statut'] .'.png" style="vertical-align:middle;" alt="" /></a>';
			} else $img_tv = '<img src="modules/Web_tv/images/bullet_'. $r_sql['statut'] .'.png" style="vertical-align:middle;" alt="" />'; */

                        $stream_on_off = check_twitch_channel();
                        $img_tv = '<img src="modules/Web_tv/images/bullet_'. $stream_on_off .'.png" style="vertical-align:middle;" alt="" title="Stream '. $stream_on_off .'">';

                	echo '<td valign="top" class="g2_cadre_table g2_gradient"><div style="position:relative;min-height:100px;width:98%;margin:auto;">'. $img_tv .' <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['id'] .'">Web TV de : '. $r_sql['nom'] .'</a>'
                	. '<p>'. stripslashes($r_sql['description']) .'</p>';

                	$sql_programme_day = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure, description FROM ". WEB_TV_PROGRAMME ." WHERE date = '". mktime(0, 0, 0, date('n'), date('j'), date('Y')) ."' AND web_tv = '". $r_sql['id'] ."' LIMIT 0,2");
			while ($r_sql = mysql_fetch_array($sql_programme_day, MYSQL_ASSOC)) {
				$date = strftime("%d", $r_sql['date']);
				echo '<div class="tv_td_content"><div class="tv_td_title" style="border-bottom: 0px!important;"><img style="vertical-align:bottom;" src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" /> '. $r_sql['heure'] .'h : '. stripslashes($r_sql['titre']) .'</div></div>';
			}

                        if(mysql_num_rows($sql_programme_day) == 0) echo '<div class="tv_td_no_content"><img style="vertical-align:bottom;" src="modules/Web_tv/images/bullet_error.png" alt="" /> '. _NOEVENTBDDTODAYINDEXTV .'</div>';

                	echo '</div></td>';

	                if ($test == 3) {
	                        $test = 0;
	                        echo "</tr>";
	                }

                }

                if ($test == 1) echo "<td style=\"width:33%;\"></td><td style=\"width:33%;\"></td></tr>";
            	if ($test == 2) echo "<td style=\"width:33%;\"></td></tr>";
            	echo "</table>";

                if (mysql_num_rows($sql_all_tv) == 0) echo '<br /><br /><div class="g2_cadre_table g2_gradient" style="width:90%!important;padding:10px;">Aucune Web TV !</div><br /><br />';

        	closetable();    	}

	//http://www.incendiarymedia.org/twitch/status.php
	function check_twitch_channel() {
		if(extension_loaded('openssl')) {
			$channelName = htmlspecialchars(WEBTV_NAME, ENT_QUOTES);
			$clientId = CLIENT_ID;
			$json_array = json_decode(file_get_contents('https://api.twitch.tv/kraken/streams/'. strtolower($channelName) .'?client_id='. $clientId), true);

			if($json_array['stream'] != NULL) {
				return 'on';
			} else {
				return 'off';
			}
		} else {
			return 'L\'extension openssl n\'est pas activé !';
		}
	}

	function view_tv($id) {
    		global $user, $nuked, $tv;

    		if ($user) $pseudo = $user[2];
		else $pseudo = 'web_tv_'. rand(1, 300);

                if (!$id) {                	$sql_first_tv = mysql_query("SELECT id FROM ". WEB_TV ." ORDER BY id asc limit 1");
                	list($first_tv) = mysql_fetch_array($sql_first_tv);
                	$tv = $first_tv;
                } else $tv = $id;

                $sql_tv = mysql_query("SELECT nom, type, url, w, h, chan_irc, code_chan_irc, ircw, irch, historique FROM ". WEB_TV ." WHERE id = '". $tv ."'");
        	list($nom, $type, $url, $w, $h, $chan_irc, $code_chan_irc, $ircw, $irch, $historique) = mysql_fetch_array($sql_tv);

                if ($nom == '') $nom = 'Web TV du site '. $nuked['name'];
                else $nom = $nom;

    		opentable();

                echo '<br /><div class="webtv_title">Web TV de : '. $nom .'</div><br />'
    		. '<div class="centeredmenu"><div class="nav l_g"><ul>'
    		. '<li><a href="index.php?file=Web_tv"><span>Retour index</span></a></li>'
    		. '</ul></div></div><div class="clear"></div><p style="height:15px;"></p>';

                if ($historique == 'popup') $link_historique = '<a href="javascript:void(0);" onclick="javascript:window.open(\'index.php?file=Web_tv&amp;nuked_nude=index&amp;op=historique_programme&amp;id='. $id .'\',\'p_tv\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0\');return(false);">';
                else $link_historique = '<a href="javascript:void(0);" onclick="historique_programme_tv(\''. $id .'\',\'1\');document.getElementById(\'hpt\').style.display = \'block\';">';

                echo '<div class="programme_on_off"><img src="modules/Web_tv/images/date.png" alt="" /> <div id="p_on" style="display:inline;"><a href="javascript:void(0);" onclick="toggle_programme_tv(\'show\');">Afficher le programme</a></div><div id="p_off" style="display:none;"><a href="javascript:void(0);" onclick="toggle_programme_tv(\'hide\');">Cacher le programme</a></div> / '. $link_historique .'Voir l\'historique</a></div>'
                . '<div id="programme_du_jour" class="tv_tableau" style="display:none;">'
                . '<div class="tv_tr">';

                $sql_programme_day = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure, description FROM ". WEB_TV_PROGRAMME ." WHERE date >= '". mktime(0, 0, 0, date('n'), date('j'), date('Y')) ."' ");
		while ($r_sql = mysql_fetch_array($sql_programme_day, MYSQL_ASSOC)) {
			$date = nkDate($r_sql['date']);
			echo '<div class="tv_td_content"><div class="tv_td_title"><img style="vertical-align:bottom;" src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" />Le '. $date .' : '. $r_sql['heure'] .'h : '. stripslashes($r_sql['titre']) .'</div>'
			. stripslashes(html_entity_decode($r_sql['description'])) .'</div>';

		}

                if(mysql_num_rows($sql_programme_day) == 0) echo '<div class="tv_td_content"><img style="vertical-align:bottom;" src="modules/Web_tv/images/bullet_error.png" alt="" /> '. _NOEVENTBDDTODAYINDEX .'</div>';

		echo '</div></div><div id="hpt" style="display:block;"></div><p style="height:5px;"></p>';

        	affichage_web_tv($nom, $type, $url, $w, $h);

		if($chan_irc != '') affichage_irc($chan_irc, $pseudo, $ircw, $irch);
                if($code_chan_irc != '') affichage_code_irc($code_chan_irc, $pseudo, $ircw, $irch);

    		closetable();	}

        function affichage_irc($chan_irc, $pseudo, $ircw, $irch) {

	        global $global_web_tv;

	        if($chan_irc != '') {
		        echo '<div id="irc" style="margin:auto;width:'. $ircw .'px;height:'. $irch .'px;"></div><br />'
			. '<script type="text/javascript">
			//<![CDATA[
			url = \''. $chan_irc .'\';
			b = document.createElement(\'iframe\');
			b.setAttribute(\'src\', "http://webchat.quakenet.org/?nick='. $pseudo .'&channels="+url+"&uio=MTA9dHJ1ZSYxMT0xMDM7a");
			b.setAttribute(\'width\', \''. $ircw .'\');
			b.setAttribute(\'height\', \''. $irch .'\');
			b.setAttribute(\'style\', \'border:0px;\');
			document.getElementById(\'irc\').appendChild(b);
			//]]>
			</script>';
		}
	}

        function affichage_code_irc($chan_irc, $pseudo, $ircw, $irch) {

	        global $global_web_tv;

	        if($chan_irc != '') {
		        echo '<div id="irc" style="margin:auto;width:'. $ircw .'px;height:'. $irch .'px;"></div><br />'
			. '<script type="text/javascript">
			//<![CDATA[
			a = document.createElement(\'div\');
			a.setAttribute(\'width\', \''. $ircw .'\');
			a.setAttribute(\'height\', \''. $irch .'\');
			a.innerHTML = \''. html_entity_decode($chan_irc) .'\';
			document.getElementById(\'irc\').appendChild(a);
			//]]>
			</script>';
		}
	}

	function affichage_web_tv($nom, $type, $url, $w, $h) {

	        if($nom != '') {
		        echo '<div id="web_tv" style="margin:auto;width:'. $w .'px;height:'. $h .'px;"></div><br />'
			. '<script type="text/javascript">
			//<![CDATA[
			var type_tv = \''. $type .'\';
			if(type_tv == "iframe") {
				c = document.createElement(\'iframe\');
				c.setAttribute(\'src\', \''. $url .'\');
				c.setAttribute(\'width\', \''. $w .'\');
				c.setAttribute(\'height\', \''. $h .'\');
				c.setAttribute(\'style\', \'border:0px;\');
			} else {
				c = document.createElement(\'div\');
				c.innerHTML = \''. html_entity_decode($url) .'\';
			}
			document.getElementById(\'web_tv\').appendChild(c);
			//]]>
			</script>';
		}
	}

        function historique_programme($id) {

        	global $theme, $bgcolor2;
                //sleep(2); // Si jamais on veut faire genre le serveur cherche ;) on lui fait faire une pause de 2 sec
        	if(!is_numeric($id)) die('Error !');

        	$nb_page_historique = '5';
        	if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        	$start = $_REQUEST['p'] * $nb_page_historique - $nb_page_historique;
                $sql_c = mysql_query("SELECT id FROM ". WEB_TV_PROGRAMME);
                $count = mysql_num_rows($sql_c);

        	$sql_tv = mysql_query("SELECT nom, historique FROM ". WEB_TV ." WHERE id = '". $id ."'");
        	list($titre, $historique) = mysql_fetch_array($sql_tv);

                if ($historique == 'popup') {
        		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
	           	. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
	           	. "<head><title>" . _HISTORIQUEEVENTTV . " : " . $titre . "</title>\n"
	           	. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
	           	. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
	           	. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
	           	. '<script type="text/javascript" src="modules/Web_tv/web_tv.js"></script>'
	           	. "<body style=\"background: " . $bgcolor2 . ";\">";
	           	if ($count > $nb_page_historique) number($count, $nb_page_historique, "index.php?file=Web_tv&amp;nuked_nude=index&amp;op=historique_programme&amp;id=". $id);
	           	echo '<div id="programme_du_jour" class="tv_tableau">'
                	. '<div class="tv_tr">';
        	} else {        		if ($count > $nb_page_historique) number_ajax($count, $nb_page_historique, $id);
        	}

        	$sql_programme_day = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure, description FROM ". WEB_TV_PROGRAMME ." WHERE web_tv = '". $id ."' AND date < ". time() ." LIMIT " . $start . ", " . $nb_page_historique);
		while ($r_sql = mysql_fetch_array($sql_programme_day, MYSQL_ASSOC)) {
			$date = nkDate($r_sql['date']);
			echo '<div class="tv_td_content"><div class="tv_td_title"><img style="vertical-align:bottom;" src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" /> le '. $date .' à '. $r_sql['heure'] .'h : '. stripslashes($r_sql['titre']) .'</div>'
			. stripslashes(html_entity_decode($r_sql['description'])) .'</div>';

		}

                if(mysql_num_rows($sql_programme_day) == 0) echo '<div class="tv_td_content">'. _NOEVENTBDDALLINDEX .'</div>';

                if ($historique == 'popup') {                	echo '</div></div></body></html>';
                } else {
			echo '</div></div><a href="javascript:void(0);" onclick="document.getElementById(\'hpt\').style.display = \'none\';">Cacher l\'historique</a></body></html>';
                }
        }

	// DISPLAYS THE NUMBER OF PAGES WITH ASYNCHRONOUS JAVASCRIPT ;o
	function number_ajax($count, $each, $id){

	    $current = $_REQUEST['p'];

	    if ($each > 0){
	        if ($count <= 0)     $count   = 1;
	        if (empty($current)) $current = 1; // On renormalise la page courante...
	        // Calcul du nombre de pages
	        $n = ceil($count / intval($each)); // on arrondit à  l'entier sup.
	        // Début de la chaine d'affichage
	        $output = '<b class="pgtitle">' . _PAGE . ' :</b> ';

	        for ($i = 1; $i <= $n; $i++){
	            if ($i == $current){
	                $output .= sprintf('<b class="pgactuel">[%d]</b> ',$i    );
	            }
	            // On est autour de la page actuelle : on affiche
	            elseif (abs($i - $current) <= 4){
	                $output .= sprintf('<a href="javascript:void(0);" class="pgnumber" onclick="historique_programme_tv(\''. $id .'\',\'%d\');">%d</a> ',$i, $i);
	            }
	            // On affiche quelque chose avant d'omettre les pages inutiles
	            else{
	                // On est avant la page courante
	                if (!isset($first_done) && $i < $current){
	                    $output .= sprintf('...<a href="javascript:void(0);" title="' . _PREVIOUSPAGE . '" class="pgback" onclick="historique_programme_tv(\''. $id .'\',\'%d\');">&laquo;</a> ',$current-1);
	                    $first_done = true;
	                }
	                // Après la page courante
	                elseif (!isset($last_done) && $i > $current){
	                    $output .= sprintf('<a href="javascript:void(0);" title="' . _NEXTPAGE . '" class="pgnext" onclick="historique_programme_tv(\''. $id .'\',\'%d\');">&raquo;</a>... ',$current+1);
	                    $last_done = true;
	                }
	                // On a dépassé les cas qui nous intéressent : inutile de continuer
	                elseif ($i > $current)
	                    break;
	            }
	        }
	        $output .= '<br />';
	        echo $output;
	    }
	}

    	switch ($_REQUEST['op']) {
        	case "index":
            	index();
            	break;

                case "view_tv":
            	view_tv($_REQUEST['id']);
            	break;

                case "historique_programme":
            	historique_programme($_REQUEST['id']);
            	break;

        	default:
            	index();
            	break;
    	}

} else if ($level_access == -1) {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    	closetable();
} else if ($level_access == 1 && $visiteur == 0) {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    	closetable();
} else {
    	opentable();
    	echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    	closetable();
}

?>