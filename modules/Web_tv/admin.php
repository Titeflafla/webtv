<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// et tutute pouette pouette la vala la totomobile !                        //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");

global $user, $language;

translate("modules/Web_tv/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
require("modules/Web_tv/config.php");

if($_REQUEST['op'] != 'show_icon' && $_REQUEST['op'] != 'check_url' && $_REQUEST['op'] != 'active_tv' && $_REQUEST['op'] != 'desactive_tv') {
	admintop();
}

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1) {

    	function index() {

	        global $nuked, $language;

                $nb_webtv_admin = '10';

                $sql_c = mysql_query("SELECT id FROM ". WEB_TV_PROGRAMME);
                $count = mysql_num_rows($sql_c);

                if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        	$start = $_REQUEST['p'] * $nb_webtv_admin - $nb_webtv_admin;

                $sql = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure, description FROM ". WEB_TV_PROGRAMME ." ORDER BY date desc LIMIT " . $start . ", " . $nb_webtv_admin);

                echo"<script type=\"text/javascript\">\n"
        	. "<!--\n"
        	. "function del_event(titre, id){\n"
        	. "if (confirm('" . _EVENTDELETE . " '+titre+' ! " . _CONFIRM . "')){\n"
        	. "document.location.href = 'index.php?file=Web_tv&page=admin&op=del_event&id='+id;}\n"
        	. "}\n"
        	. "//-->\n"
        	. "</script>\n"
                . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/". $language ."/Webtv.php\" rel=\"modal\">\n" // Quoi vous avez besoin d'aide ? 118 218 il trouve tout hein ^^ au pire y a google
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">". _WEBTV ." <b>| "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_event\">" . _ADDEVENT . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=main_tv\">" . _VOIRTV . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_tv\">" . _ADDTV . "</a></b></div><br />\n";

	        if ($count > $nb_webtv_admin) number($count, $nb_webtv_admin, "index.php?file=Web_tv&amp;page=admin");

	        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        	. "<tr>\n"
        	. "<td style=\"width: 5%;\" align=\"center\"><b>" . _JEUX . "</b></td>\n"
        	. "<td style=\"width: 30%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _WEBTVID . "</b></td>\n"
        	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

	        while (list($id, $web_tv, $jeux, $titre_jeux, $titre, $date, $heure, $description) = mysql_fetch_array($sql)) {
	        	$titre = stripslashes($titre);
            		$titre_jeux = stripslashes($titre_jeux);
            		$date = nkDate($date);
                        $sql_titre_tv = mysql_query("SELECT nom FROM ". WEB_TV ." WHERE id = '". $web_tv ."' ");
                        list($titre_tv) = mysql_fetch_array($sql_titre_tv);
                        $titre_tv = stripslashes($titre_tv);

            		echo "<tr>\n"
            		. "<td><img src=\"modules/Web_tv/images/jeux/16/" . $jeux . "\" alt=\"\" title=\"" . $titre_jeux . "\" /></td>\n"
            		. "<td align=\"center\">" . $titre . "</td>\n"
            		. "<td align=\"center\">" . $titre_tv . "</td>\n"
            		. "<td align=\"center\">" . $date ." : ". $heure. "h</td>\n"
            		. "<td align=\"center\"><a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=edit_event&amp;id=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISSCREEN . "\" /></a></td>\n"
            		. "<td align=\"center\"><a href=\"javascript:del_event('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISSCREEN . "\" /></a></td></tr>\n";
            	}

                if ($count == 0) echo "<tr><td colspan=\"6\" align=\"center\">" . _NOEVENTBDD . "</td></tr>";

        	echo "</table>";

                if ($count > $nb_webtv_admin) number($count, $nb_webtv_admin, "index.php?file=Web_tv&amp;page=admin");

	        echo "<br /></div></div>\n";
    	}

        function add_event() {

	        global $nuked, $language;

                echo '<script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>'
                . '<script type="text/javascript" src="media/js/jquery-ui/jquery-ui-1.8.21.custom.min.js"></script>' //jquery c'est moche na !!
                . '<link title="style" type="text/css" rel="stylesheet" href="media/js/jquery-ui/css/smoothness/jquery-ui-1.8.21.custom.css" />' // Mais non c'est pas moche d'inclure du css comme �a � l'arrache XD
                . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Webtv.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Web_tv&amp;page=admin\">". _WEBTV ."</a> | "
	        . "</b>" . _ADDEVENT . "</a> <b>| "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=main_tv\">" . _VOIRTV . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_tv\">" . _ADDTV . "</a></b></div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Web_tv&amp;page=admin&amp;op=send_event\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" /></td></tr>\n"
	        . "<tr><td><b>" . _SELECTTV . "</b> : <select name=\"tv\">\n";
                $sql_all_tv = mysql_query("SELECT id, nom, statut FROM ". WEB_TV);
                while ($r_sql = mysql_fetch_array($sql_all_tv, MYSQL_ASSOC)) {
                	echo '<option value="'. $r_sql['id'] .'">'. stripslashes($r_sql['nom']) .'</option>';
                }
	        echo "</select></td></tr>"
                . "<tr><td><b>" . _IMGGAME . " :</b><input type=\"hidden\" id=\"jeux\" name=\"jeux\" value=\"\" /> <img id=\"img_jeux\" src=\"images/del.gif\" alt=\"\" /> <a href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon','jeux','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\">". _SELECTJEUX ."</a></td></tr>\n"
	        . "<tr><td><b>" . _TITLEGAME . " :</b> <input type=\"text\" name=\"titre_jeux\" /></td></tr>\n"
	        . "<tr><td><b>". _DATE ."</b> : <input type=\"text\" id=\"datepicker\" name=\"date\" value=\"\" /> &nbsp;&nbsp;". _DE ." <input type=\"text\" name=\"day_1\" size=\"2\" value=\"\" />h ". _AT ." <input type=\"text\" name=\"day_2\" size=\"2\" value=\"\" />h</td></tr>"
	        . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
	        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr>\n"
	        . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDEVENT . "\" /></td></tr></table>\n"
	        . "</div></div>\n"
	        . "<script type=\"text/javascript\">
		//<![CDATA[
		$(function() {
			$(\"#datepicker\").datepicker({
				dateFormat: \"d-mm-yy\",
				dayNames: [\"Dimanche\", \"Lundi\", \"Mardi\", \"Mercredi\", \"Jeudi\", \"Vendredi\", \"Samedi\"],
				dayNamesMin: [\"Di\", \"Lu\", \"Ma\", \"Me\", \"Je\", \"Ve\", \"Sa\"],
				dayNamesShort: [\"Dim\", \"Lun\", \"Mar\", \"Mer\", \"Jeu\", \"Ven\", \"Sam\"],
				monthNames: [\"Janvier\",\"Fevrier\",\"Mars\",\"Avril\",\"Mai\",\"Juin\",\"Juillet\",\"A�ut\",\"Septembre\",\"Octobre\",\"Novembre\",\"D�cembre\"],
				nextText: \"Suivant\", prevText: \"Pr�c�dent\",
				firstDay: 1
			});
		});
		//]]>
		</script>";
        }

        function send_event($titre, $tv, $jeux, $titre_jeux, $date, $day_1, $day_2, $description) {

        	global $nuked, $user;

                $titre = mysql_real_escape_string(stripslashes($titre));
                $titre_jeux = mysql_real_escape_string(stripslashes($titre_jeux));
                $description = mysql_real_escape_string(stripslashes($description));
                $p_e = explode('-', $date);
                $date = mktime(0,0,0,$p_e[1],$p_e[0],$p_e[2]);
                $heure = $day_1 .'-'. $day_2;

        	$sql = mysql_query("INSERT INTO ". WEB_TV_PROGRAMME ." ( `id`, `web_tv`, `jeux`, `titre_jeux`, `titre`, `date`, `heure`, `description` ) VALUES ('', '". $tv ."', '". $jeux ."', '". $titre_jeux ."', '". $titre ."', '". $date ."', '". $heure ."', '". $description ."')");
	        // Action
	        $texteaction = _ACTIONADDEVENT ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _EVENTADD ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
        }

        function edit_event($id) {

	        global $nuked, $language;

                $sql_event = mysql_query("SELECT web_tv, jeux, titre_jeux, titre, date, heure, description FROM ". WEB_TV_PROGRAMME ." WHERE id = '". $id ."'");
        	list($web_tv, $jeux, $titre_jeux, $titre, $date, $heure, $description) = mysql_fetch_array($sql_event);

                $date = strftime("%d-%m-%Y", $date);
                $h = explode('-', $heure);

        	echo '<script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>'
                . '<script type="text/javascript" src="media/js/jquery-ui/jquery-ui-1.8.21.custom.min.js"></script>'
                . '<link title="style" type="text/css" rel="stylesheet" href="media/js/jquery-ui/css/smoothness/jquery-ui-1.8.21.custom.css" />'
                . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Webtv.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Web_tv&amp;page=admin\">". _WEBTV ."</a> | "
	        . "</b>" . _ADDEVENT . "</a> <b>| "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=main_tv\">" . _VOIRTV . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_tv\">" . _ADDTV . "</a></b></div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Web_tv&amp;page=admin&amp;op=modif_event\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" value=\"". stripslashes($titre) ."\" /></td></tr>\n"
	        . "<tr><td><b>" . _SELECTTV . "</b> : <select name=\"tv\">\n";
                $sql_all_tv = mysql_query("SELECT id, nom, statut FROM ". WEB_TV);
                while ($r_sql = mysql_fetch_array($sql_all_tv, MYSQL_ASSOC)) {
                	if($web_tv == $r_sql['id']) $selected = ' selected="selected"';
                	else $selected = '';
                	echo '<option value="'. $r_sql['id'] .'"'. $selected .'>'. stripslashes($r_sql['nom']) .'</option>';
                }
	        echo "</select></td></tr>"
                . "<tr><td><b>" . _IMGGAME . " :</b><input type=\"hidden\" id=\"jeux\" name=\"jeux\" value=\"". $jeux ."\" /> <img id=\"img_jeux\" src=\"modules/Web_tv/images/jeux/16/". $jeux ."\" alt=\"\" /> <a href=\"javascript:void(0);\" onclick=\"javascript:window.open('index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon','jeux','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=650,height=300,top=30,left=0');return(false)\">". _SELECTJEUX ."</a></td></tr>\n"
	        . "<tr><td><b>" . _TITLEGAME . " :</b> <input type=\"text\" name=\"titre_jeux\" value=\"". stripslashes($titre_jeux) ."\" /></td></tr>\n"
	        . "<tr><td><b>". _DATE ."</b> : <input type=\"text\" id=\"datepicker\" name=\"date\" value=\"". $date ."\" /> &nbsp;&nbsp;". _DE ." <input type=\"text\" name=\"day_1\" size=\"2\" value=\"". $h[0] ."\" />h ". _AT ." <input type=\"text\" name=\"day_2\" size=\"2\" value=\"". $h[1] ."\" />h</td></tr>"
	        . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
	        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . stripslashes($description) . "</textarea></td></tr>\n"
	        . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFEVENT . "\" /><input name=\"id\" type=\"hidden\" value=\"". $id ."\" /></td></tr></table>\n"
                . "</div></div>\n"
                . "<script type=\"text/javascript\">
		//<![CDATA[
		$(function() {
			$(\"#datepicker\").datepicker({
				dateFormat: \"d-mm-yy\",
				dayNames: [\"Dimanche\", \"Lundi\", \"Mardi\", \"Mercredi\", \"Jeudi\", \"Vendredi\", \"Samedi\"],
				dayNamesMin: [\"Di\", \"Lu\", \"Ma\", \"Me\", \"Je\", \"Ve\", \"Sa\"],
				dayNamesShort: [\"Dim\", \"Lun\", \"Mar\", \"Mer\", \"Jeu\", \"Ven\", \"Sam\"],
				monthNames: [\"Janvier\",\"Fevrier\",\"Mars\",\"Avril\",\"Mai\",\"Juin\",\"Juillet\",\"A�ut\",\"Septembre\",\"Octobre\",\"Novembre\",\"D�cembre\"],
				nextText: \"Suivant\", prevText: \"Pr�c�dent\",
				firstDay: 1
			});
		});
		//]]>
		</script>";
        }

        function modif_event($titre, $tv, $jeux, $titre_jeux, $date, $day_1, $day_2, $description, $id) {

        	global $nuked, $user;

                $titre = mysql_real_escape_string(stripslashes($titre));
                $titre_jeux = mysql_real_escape_string(stripslashes($titre_jeux));
                $description = mysql_real_escape_string(stripslashes($description));
                $p_e = explode('-', $date);
                $date = mktime(0,0,0,$p_e[1],$p_e[0],$p_e[2]);
                $heure = $day_1 .'-'. $day_2;

                $sql = mysql_query("UPDATE ". WEB_TV_PROGRAMME ." SET web_tv = '" . $tv . "', jeux = '" . $jeux . "', titre_jeux = '" . $titre_jeux . "', titre = '" . $titre . "', date = '" . $date . "', heure = '". $heure ."', description = '". $description ."' WHERE id = '" . $id . "'");

	        // Action
	        $texteaction = _ACTIONMODIFEVENT ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _EVENTMODIF ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');

        }

        function del_event($id) {

	        global $nuked, $user;

	        $sqlq = mysql_query("SELECT titre FROM ". WEB_TV_PROGRAMME ." WHERE id='". $id ."'");
	        list($titre) = mysql_fetch_array($sqlq);
	        $titre = mysql_real_escape_string($titre);
	        $sql = mysql_query("DELETE FROM ". WEB_TV_PROGRAMME ." WHERE id = '". $id ."'");

	        // Action
	        $texteaction =  _ACTIONDELEVENT ." ". $titre;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _EVENTDELETE ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
        }

	function main_tv() {

		global $nuked, $language;

                echo"<script type=\"text/javascript\">\n"
        	."<!--\n"
        	. "function del_tv(titre, id){\n"
        	. "if (confirm('" . _TVDELETE . " '+titre+' ! " . _CONFIRM . "')){\n"
        	. "document.location.href = 'index.php?file=Web_tv&page=admin&op=del_tv&id='+id;}\n"
        	. "}\n"
        	. "//-->\n"
        	. "</script>\n"
                . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Webtv.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Web_tv&amp;page=admin\">". _WEBTV ."</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_event\">" . _ADDEVENT . "</a> | "
	        . "</b>" . _VOIRTV . "<b> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_tv\">" . _ADDTV . "</a></b></div><br />\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	        . "<tr>\n"
	        . "<td align=\"center\"><b>" . _NOMTV . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _TYPE . "</b></td>\n"
	        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _STATUTTV . "</b></td>\n"
	        . "<td style=\"width: 5%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
	        . "<td style=\"width: 5%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

		$sql_all_tv = mysql_query("SELECT id, nom, type, statut FROM ". WEB_TV);
                while ($r_sql = mysql_fetch_array($sql_all_tv, MYSQL_ASSOC)) {
                	if ($r_sql['statut'] == 'off') $img_actif = '<a href="index.php?file=Web_tv&amp;page=admin&amp;op=active_tv&amp;id='. $r_sql['id'] .'" title="Activer cette TV"><img src="modules/Web_tv/images/off.png" alt="" /></a>';
                        else $img_actif = '<a href="index.php?file=Web_tv&amp;page=admin&amp;op=desactive_tv&amp;id='. $r_sql['id'] .'" title="D&eacute;sactiver cette TV"><img src="modules/Web_tv/images/on.png" alt="" /></a>';

                	echo "<tr>\n"
		        . "<td>". stripslashes($r_sql['nom']) ."</td>\n"
		        . "<td>". $r_sql['type'] ."</td>\n"
                	. "<td align=\"center\">". $img_actif ."</td>\n"
                	. "<td align=\"center\"><a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=edit_tv&amp;id=" . $r_sql['id'] . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISTV . "\" /></a></td>\n"
                	. "<td align=\"center\"><a href=\"javascript:del_tv('" . mysql_real_escape_string(stripslashes($r_sql['nom'])) . "', '" . $r_sql['id'] . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISTV . "\" /></a></td>\n";
                }

                if (mysql_num_rows($sql_all_tv) == 0) echo "<tr><td colspan=\"5\" align=\"center\">" . _NOTVBDD . "</td></tr>";

                echo "</table></div></div>\n";
	}

        function active_tv($id) {
                $bdd = mysql_query("UPDATE ". WEB_TV ." SET statut = 'on' WHERE id = '". $id ."'");
                redirect($_SERVER["HTTP_REFERER"], 0);
                //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
        }

        function desactive_tv($id) {
                $bdd = mysql_query("UPDATE ". WEB_TV ." SET statut = 'off' WHERE id = '". $id ."'");
                redirect($_SERVER["HTTP_REFERER"], 0);
                //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
        }

        function add_tv() {

	        global $nuked, $language;

                echo '<script type="text/javascript" src="modules/Web_tv/web_tv.js"></script>'
                . "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Webtv.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Web_tv&amp;page=admin\">". _WEBTV ."</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_event\">" . _ADDEVENT . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=main_tv\">" . _VOIRTV . "</a> | "
	        . "</b>" . _ADDTV . "</div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Web_tv&amp;page=admin&amp;op=send_tv\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"nom\" size=\"44\" /></td></tr>\n"
	        . "<tr><td><b>" . _TYPE. " :</b> <select size=\"1\" id=\"type\" name=\"type\"><option value=\"iframe\">Iframe</option><option value=\"flash\">Flash</option></select></td></tr>\n"  //pas flash gordon hein
                . "<tr><td><b>" . _URLTV . " :</b> <input type=\"text\" id=\"url\" name=\"url\" size=\"44\" onmouseout=\"check_url();\" /> <div style=\"display:inline;\" id=\"check_url\"></div></td></tr>\n"
                . "<tr><td><b>" . _WTV . " :</b> <input type=\"text\" name=\"w\" size=\"3\" value=\"650\" /></td></tr>\n"
                . "<tr><td><b>" . _HTV . " :</b> <input type=\"text\" name=\"h\" size=\"3\" value=\"400\" /></td></tr>\n"
                . "<tr><td><b>" . _IRCTV . " :</b> <input type=\"text\" name=\"chan_irc\" /></td></tr>\n"
	        . "<tr><td><b>" . _CODEIRCTV . " :</b> <input type=\"text\" name=\"code_chan_irc\" /></td></tr>\n"
	        . "<tr><td><b>" . _IRCW . " :</b> <input type=\"text\" name=\"ircw\" size=\"3\" value=\"650\" /></td></tr>\n"
                . "<tr><td><b>" . _IRCH . " :</b> <input type=\"text\" name=\"irch\" size=\"3\" value=\"400\" /></td></tr>\n"
	        . "<tr><td><b>" . _STATUTTV . " :</b> <select size=\"1\" name=\"statut\"><option value=\"off\">Off</option><option value=\"on\">On</option></select></td></tr>\n"
	        . "<tr><td><b>" . _AFFHISTORIQUE . " :</b> <select size=\"1\" name=\"statut\"><option value=\"\">Ajax</option><option value=\"popup\">Popup</option></select></td></tr>\n"
	        . "<tr><td><b>" . _DESCRIPTIONTV . " :</b></td></tr>\n"
                . "<tr><td><textarea class=\"editor\" name=\"description\" cols=\"66\" rows=\"10\"></textarea></td></tr>\n"
	        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDTV . "\" /></td></tr></table>\n"
	        . "</div></div>\n";
        }

	function send_tv($nom, $type, $url, $w, $h, $chan_irc, $code_chan_irc, $ircw, $irch, $statut, $description, $historique) {

        	global $nuked, $user;

                $nom = mysql_real_escape_string(stripslashes($nom));
                $chan_irc = mysql_real_escape_string(stripslashes($chan_irc));

                //on trouve l'url de la tv iframe :)
                if($type == 'iframe') {
                	//on regarde s'il y a bien la balise iframe
                	if(preg_match('/src="([^"]*)"/', stripslashes($url), $matches)) $url_tv = $matches[1];
                	else $url_tv = stripslashes($url);
                } else $url_tv = $url; //si l'url est en flash on laisse comme �a

                $url_tv = mysql_real_escape_string(stripslashes($url_tv));
                $description = html_entity_decode($description);
	        $description = mysql_real_escape_string(stripslashes($description));

        	$sql = mysql_query("INSERT INTO ". WEB_TV ." ( `id`, `nom`, `type`, `url`, `w`, `h`, `chan_irc`, `code_chan_irc`, `ircw`, `irch`, `statut`, `description` ) VALUES ('', '". $nom ."', '". $type ."', '". $url_tv ."', '". $w ."', '". $h ."', '". $chan_irc ."', '". $code_chan_irc ."', '". $ircw ."', '". $irch ."', '". $statut ."', '". $description ."')");
	        // Action
	        $texteaction = _ACTIONADDTV ." ". $nom;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _TVADD ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin&op=main_tv", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
	}

        function edit_tv($id) {

        	global $language;

        	$sql_tv = mysql_query("SELECT nom, type, url, w, h, chan_irc, code_chan_irc, ircw, irch, statut, description, historique FROM ". WEB_TV ." WHERE id = '". $id ."'");
        	list($nom, $type, $url, $w, $h, $chan_irc, $code_chan_irc, $ircw, $irch, $statut, $description, $historique) = mysql_fetch_array($sql_tv);

        	echo "<div class=\"content-box\">\n"
	        . "<div class=\"content-box-header\"><h3>" . _ADMINWEBTV . "</h3>\n"
	        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Webtv.php\" rel=\"modal\">\n"
	        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	        . "</div>\n"
	        . "</div><div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Web_tv&amp;page=admin\">". _WEBTV ."</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_event\">" . _ADDEVENT . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=main_tv\">" . _VOIRTV . "</a> | "
	        . "<a href=\"index.php?file=Web_tv&amp;page=admin&amp;op=add_tv\">" . _ADDTV . "</a></b></div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Web_tv&amp;page=admin&amp;op=modif_tv\">\n"
	        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
	        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"nom\" size=\"44\" value=\"". $nom ."\" /></td></tr>\n"
	        . "<tr><td><b>" . _TYPE. " :</b> <select size=\"1\" id=\"type\" name=\"type\"><option value=\"iframe\""; if($type == "iframe") echo " selected=\"selected\""; echo ">Iframe</option><option value=\"flash\""; if($type == "flash") echo " selected=\"selected\""; echo ">Flash</option></select></td></tr>\n"  //pas flash gordon hein
                . "<tr><td><b>" . _URLTV . " :</b></td></tr>\n"
                . "<tr><td><textarea name=\"url\" rows=5 cols=10>". stripslashes($url) ."</textarea></td></tr>\n"
                . "<tr><td><b>" . _WTV . " :</b> <input type=\"text\" name=\"w\" size=\"3\" value=\"". $w ."\" /></td></tr>\n"
                . "<tr><td><b>" . _HTV . " :</b> <input type=\"text\" name=\"h\" size=\"3\" value=\"". $h ."\" /></td></tr>\n"
                . "<tr><td><b>" . _IRCTV . " :</b> <input type=\"text\" name=\"chan_irc\" value=\"". $chan_irc ."\" /></td></tr>\n"
	        . "<tr><td><b>" . _CODEIRCTV . " :</b></td></tr>\n"
                . "<tr><td><textarea name=\"code_chan_irc\" rows=5 cols=10>". stripslashes($code_chan_irc) ."</textarea></td></tr>\n"
	        . "<tr><td><b>" . _IRCW . " :</b> <input type=\"text\" name=\"ircw\" size=\"3\" value=\"". $ircw ."\" /></td></tr>\n"
                . "<tr><td><b>" . _IRCH . " :</b> <input type=\"text\" name=\"irch\" size=\"3\" value=\"". $irch ."\" /></td></tr>\n"
	        . "<tr><td><b>" . _STATUTTV . " :</b> <select size=\"1\" name=\"statut\"><option value=\"off\""; if($statut == "off") echo " selected=\"selected\""; echo ">Off</option><option value=\"on\""; if($statut == "on") echo " selected=\"selected\""; echo ">On</option></select></td></tr>\n"
                . "<tr><td><b>" . _AFFHISTORIQUE . " :</b> <select size=\"1\" name=\"historique\"><option value=\"\""; if($historique == "") echo " selected=\"selected\""; echo ">Ajax</option><option value=\"popup\""; if($historique == "popup") echo " selected=\"selected\""; echo ">Popup</option></select></td></tr>\n"
                . "<tr><td><b>" . _DESCRIPTIONTV . " :</b></td></tr>\n"
                . "<tr><td><textarea class=\"editor\" name=\"description\" cols=\"66\" rows=\"10\">". stripslashes($description) ."</textarea></td></tr>\n"
	        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input name=\"id\" type=\"hidden\" value=\"". $id ."\" /><input type=\"submit\" value=\"" . _MODIFTV . "\" /></td></tr></table>\n"
                . "</div></div>\n";
        }

	function modif_tv($nom, $type, $url, $w, $h, $chan_irc, $code_chan_irc, $ircw, $irch, $statut, $id, $description, $historique) {

        	global $nuked, $user;

                $nom = mysql_real_escape_string(stripslashes($nom));
                $chan_irc = mysql_real_escape_string(stripslashes($chan_irc));

                $url_tv = mysql_real_escape_string(stripslashes($url_tv));
                $description = html_entity_decode($description);
	        $description = mysql_real_escape_string(stripslashes($description));

	        $sql = mysql_query("UPDATE ". WEB_TV ." SET nom = '" . $nom . "', type = '" . $type . "', url = '" . $url . "', w = '" . $w . "', h = '" . $h . "', chan_irc = '". $chan_irc ."', code_chan_irc = '". $code_chan_irc ."', ircw = '". $ircw ."', irch = '". $irch ."', statut = '". $statut ."', description = '". $description ."', historique = '". $historique ."'  WHERE id = '" . $id . "'");

	        // Action
	        $texteaction = _ACTIONMODIFTV ." ". $nom;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _TVMODIF ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin&op=main_tv", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
	}

        function del_tv($id) {

	        global $nuked, $user;

	        $sqlq = mysql_query("SELECT nom FROM ". WEB_TV ." WHERE id='". $id ."'");
	        list($nom) = mysql_fetch_array($sqlq);
	        $nom = mysql_real_escape_string($nom);
	        $sql = mysql_query("DELETE FROM ". WEB_TV ." WHERE id = '". $id ."'");

	        // Action
	        $texteaction =  _ACTIONDELTV ." ". $nom;
	        $acdate = time();
	        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('". $acdate ."', '". $user[0] ."', '". $texteaction ."')");
	        //Fin action
	        echo "<div class=\"notification success png_bg\"><div>". _TVDELETE ."</div></div>\n";
	        redirect("index.php?file=Web_tv&page=admin&op=main_tv", 2);
	        //on supprime le fichier en cache
                unlink('modules/Web_tv/block_cache.html');
        }

        function show_icon() {

                global $bgcolor2, $theme, $nuked;

                $a_img  = array();
                $imgdir = 'modules/Web_tv/images/jeux/16';
                $col    = 10;
                $maxrow = 3;
                $dimg   = opendir($imgdir);

                if (isset($_REQUEST['del']) && $_REQUEST['del'] != "") {
                        if (file_exists($imgdir .'/'. $_REQUEST['del'])) {
                                unlink($imgdir .'/'. $_REQUEST['del']);
                                echo '<div class="g2_succes">L\'image '. $_REQUEST['del'] .' a etait supprim&eacute; !</div><br />';
                                redirect("index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon", 1);
                        } else echo '<div class="g2_error">Ce fichier n\'existe plus !</div><br />';
                }

                if (isset($_FILES["fichier"]) && $_FILES["fichier"] != "") {
                        $fichier = basename($_FILES['fichier']['name']);
                        $taille_maxi = 1048576;
                        $taille = filesize($_FILES['fichier']['tmp_name']);
                        $extensions = array('.png', '.gif', '.jpg', '.jpeg', '.PNG', '.GIF', '.JPG', '.JPEG');
                        $extension = strrchr($_FILES['fichier']['name'], '.');
                        if( preg_match('#[\x00-\x1F\x7F-\x9F/\\\\]#', $fichier) ) $erreur = '<div class="g2_error">Nom de fichier non valide !</div>';
                        if(!in_array($extension, $extensions)) $erreur = '<div class="g2_error">Vous devez uploader un fichier de type png, gif, jpg ou jpeg !</div>';
                        if($taille > $taille_maxi) $erreur = '<div class="g2_warn">Le fichier est trop gros !</div>';
                        if(!isset($erreur)) {
                                $ext = pathinfo($fichier, PATHINFO_EXTENSION);
                                $fichier = time() .".". $ext;
                                if(move_uploaded_file($_FILES['fichier']['tmp_name'], $imgdir .'/'. $fichier)) {
                                	echo '<div class="g2_succes">Image Upload&eacute; effectu&eacute; avec succ&egrave;s !</div><br />';
                                	redirect("index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon", 1);
                                } else echo '<div class="g2_error">Echec de l\'upload !</div><br />';
                        } else echo $erreur;
                }

                echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
          	. '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'
          	. '<head><title>Image Web TV</title>'
          	. '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'
          	. '<meta http-equiv="content-style-type" content="text/css" />'
          	. '<link rel="stylesheet" media="screen" type="text/css" href="modules/Web_tv/web_tv_v1.css" />'
          	. '<link title="style" type="text/css" rel="stylesheet" href="themes/'. $theme .'/style.css" /></head><body>'
                . "<body>"
                . "<script type=\"text/javascript\">\n"
                . "//<![CDATA[\n"
                . "function go(img){opener.document.getElementById('jeux').value=img;opener.document.getElementById('img_jeux').src='". $imgdir ."/'+img;}\n"
                . "function del_img(titre, img){if(confirm('Supprimer '+titre+' ! ". _CONFIRM ."')){document.location.href = 'index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon&del='+img+'';}}\n"
                . "//]]>\n"
                . "</script>\n"
                . "<br /><div class=\"g2_info\">Cliquez sur une image pour la s&eacute;lectionner<br />Format .png .gif .jpg et inf&eacute;rieur a 1Mo<br /><br />"
                . '<form action="index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon" method="post" enctype="multipart/form-data">'
                . '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">'
                . '<input type="file" name="fichier" />'
                . '<input type="submit" value="Envoyer" />'
                . '</form></div><br />';

                while($imgfile = readdir($dimg)) {
                        if ((substr($imgfile,-3)=="png") || (substr($imgfile,-3)=="jpg") || (substr($imgfile,-3)=="gif")) {
                                $a_img[count($a_img)] = $imgfile;
                                sort($a_img);
                                reset($a_img);
                        }
                }

                $totimg = count($a_img);
		$totxpage = $col*$maxrow;
		$totpages = $totimg%$totxpage == 0 ? (int)$totimg/$totxpage : (int)($totimg/$totxpage)+1;

                if($_REQUEST['p'] == "" || $_REQUEST['p'] == 1) {
        		$x = 0;
        		$p = 1;
        		$r = 0;
        	} else {
        		$x = ($_REQUEST['p']-1)*$totxpage;
        		$r = 0;
                }
                if ($totimg > $totxpage) number($totimg, $totxpage, "index.php?file=Web_tv&page=admin&nuked_nude=admin&op=show_icon");
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;width:80%;\" cellpadding=\"10\" cellspacing=\"10\">";
                foreach($a_img as $key => $val) {
                        if(($x%$col)==0) echo "<tr>";
                        if(isset($a_img[$x])) {
                        	echo "<td style=\"text-align:center;\" class=\"td_gestion_image\"><a href=\"javascript:void(0);\" onclick=\"javascript:go('". $a_img[$x] ."');self.close();\"><img src=\"modules/Web_tv/images/jeux/16/". $a_img[$x] ."\" alt=\"\" title=\"". $a_img[$x] ."\" /></a>"
                        	. "<br /><a href=\"javascript:del_img('". $a_img[$x] ."','". $a_img[$x] ."');\" title=\"Supprimer ". $a_img[$x] ."\"><img src=\"images/del.gif\" alt=\"\" /></a>"
                        	. "</td>";
                        }
                        if(($x%$col) == ($col-1)) {
                                echo "</tr>";
                        	$r++;
                        }
                        if($r==$maxrow) break;
                        else $x++;
                }
                echo '</table></body></html>';
        }

	function check_url() {
		if($_POST['url'] != '' && $_POST['type'] == 'iframe') {
			sleep(2);  //on fait genre le serveur cherche XD
			if(preg_match('/src="([^"]*)"/', stripslashes($_POST['url']), $matches)) echo '<span style="color:#008000;">Code Tv ok</span>';
			else echo '<span style="color:#B00000;">copier le code embed iframe de votre web tv !</span>';
		} elseif($_POST['url'] != '' && $_POST['type'] == 'flash') {
			sleep(2);  //on fait genre le serveur cherche XD
			// on regarde si il y a bien le code object \o/ peut �tre � am�liorer ;)
			if(preg_match('/object/', stripslashes($_POST['url']), $matches)) echo '<span style="color:#008000;">Code Tv ok</span>';
			else echo '<span style="color:#B00000;">copier le code embed flash de votre web tv !</span>';
		}
	}

   	switch ($_REQUEST['op']) {
        	case "index":
            	index();
            	break;

        	case "edit_event":
            	edit_event($_REQUEST['id']);
            	break;

        	case "del_event":
            	del_event($_REQUEST['id']);
            	break;

                case "add_event":
            	add_event();
            	break;

                case "send_event":
            	send_event($_REQUEST['titre'], $_REQUEST['tv'], $_REQUEST['jeux'], $_REQUEST['titre_jeux'], $_REQUEST['date'], $_REQUEST['day_1'], $_REQUEST['day_2'], $_REQUEST['description']);
            	break;

                case "modif_event":
            	modif_event($_REQUEST['titre'], $_REQUEST['tv'], $_REQUEST['jeux'], $_REQUEST['titre_jeux'], $_REQUEST['date'], $_REQUEST['day_1'], $_REQUEST['day_2'], $_REQUEST['description'], $_REQUEST['id']);
            	break;

                case "main_tv":
            	main_tv();
            	break;

        	case "active_tv":
            	active_tv($_REQUEST['id']);
            	break;

        	case "desactive_tv":
            	desactive_tv($_REQUEST['id']);
            	break;

                case "add_tv":
            	add_tv();
            	break;

                case "send_tv":
            	send_tv($_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['url'], $_REQUEST['w'], $_REQUEST['h'], $_REQUEST['chan_irc'], $_REQUEST['code_chan_irc'], $_REQUEST['ircw'], $_REQUEST['irch'], $_REQUEST['statut'], $_REQUEST['description'], $_REQUEST['historique']);
            	break;

        	case "edit_tv":
            	edit_tv($_REQUEST['id']);
            	break;

        	case "del_tv":
            	del_tv($_REQUEST['id']);
            	break;

                case "modif_tv":
            	modif_tv($_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['url'], $_REQUEST['w'], $_REQUEST['h'], $_REQUEST['chan_irc'], $_REQUEST['code_chan_irc'], $_REQUEST['ircw'], $_REQUEST['irch'], $_REQUEST['statut'], $_REQUEST['id'], $_REQUEST['description'], $_REQUEST['historique']);
            	break;

        	case "show_icon":
            	show_icon();
            	break;

        	case "check_url":
            	check_url();
            	break;

        	default:
            	index();
            	break;
    	}

} else if ($level_admin == -1) {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
} else if ($visiteur > 1) {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
} else {
    	echo "<div class=\"notification error png_bg\">\n"
    	. "<div>\n"
    	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    	. "</div>\n"
    	. "</div>\n";
}

if($_REQUEST['op'] != 'show_icon' && $_REQUEST['op'] != 'check_url' && $_REQUEST['op'] != 'active_tv' && $_REQUEST['op'] != 'desactive_tv') {
	adminfoot();
}
?>
