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

global $nuked, $language;

translate('modules/Web_tv/lang/'. $language .'.lang.php');
include('modules/Web_tv/config.php');

// on regarde si une tv est en ligne si oui on change le logo, faut faire une petition pour avoir une api publique ;)
/*$sql_tv = mysql_query("SELECT nom FROM ". WEB_TV ." WHERE statut = 'on'");
list($nom) = mysql_fetch_array($sql_tv);
if($nom != '') $img_tv = 'programme_tv_on.gif';
else $img_tv = 'programme_tv.png';*/

//http://www.incendiarymedia.org/twitch/status.php
if(extension_loaded('openssl')) {
	$channelName = htmlspecialchars(WEBTV_NAME, ENT_QUOTES);
	$clientId = CLIENT_ID;
	$json_array = json_decode(file_get_contents('https://api.twitch.tv/kraken/streams/'. strtolower($channelName) .'?client_id='. $clientId), true);

	if($json_array['stream'] != NULL) {
		$img_tv = 'programme_tv_on.gif';
	} else {
		$img_tv = 'programme_tv.png';
	}
} else {
	echo 'L\'extension openssl n\'est pas activé !';
}

echo '<script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>'
. '<script type="text/javascript" src="modules/Web_tv/web_tv.js"></script>';

$lundi    = mktime(0, 0, 0, date('n'), date('j'), date('Y')) - ((date('N')-1)*3600*24); // le lundi de la semaine en cours, le date(N) est dispo que depuis php 5.1
$mardi    = $lundi + 86400;
$mercredi = $mardi + 86400;
$jeudi    = $mercredi +86400;
$vendredi = $jeudi + 86400;
$samedi   = $vendredi + 86400;
$dimanche = $samedi + 86400;

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4) {
	echo '<div class="webtv_programme">'
	. '<div style="float:left;display:block;margin-left:20px;"><img src="modules/Web_tv/images/'. $img_tv .'" alt="" title="Programme WebTV" /><br />'
	. 'Du '. strftime("%d-%m-%Y", $lundi) .' au '. strftime("%d-%m-%Y", $dimanche) .'</div>'
	. '<div id="webtv" style="margin-left:250px!important;width:250px!important;">';
} else {
	echo '<div class="webtv_programme">'
	. '<div style="text-align:center;"><img src="modules/Web_tv/images/'. $img_tv .'" alt="" title="Programme WebTV" /><br />'
	. 'Du '. strftime("%d-%m-%Y", $lundi) .' au '. strftime("%d-%m-%Y", $dimanche) .'</div>'
	. '<div id="webtv">';
}

// on met en cache vue que ça fait beaucoup de requette sql :o
$cache = 'modules/Web_tv/block_cache.html';
$expire = time() - CACHE_TIME; // valable 1 heure 3600

if(file_exists($cache) && filemtime($cache) > $expire) {
	readfile($cache);
} else {
	ob_start();

	echo '<ul>';

	//on affiche le programme de la semaine en cours , y a pas un truc plus simple ? une boucle chaque jour �a fait beaucoup XD quoi que une grosse boucle puis en faire une chaque jour ca reviens au m�me non ?

	echo '<li class="toggleTv">';

	$sql_programme_lundi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $lundi ."' ");
	$count_event_lundi = mysql_num_rows($sql_programme_lundi);
	echo '<span>Lundi ('. $count_event_lundi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '1') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_lundi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_lundi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_mardi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $mardi ."' ");
	$count_event_mardi = mysql_num_rows($sql_programme_mardi);
	echo '<span>Mardi ('. $count_event_lundi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '2') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_mardi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_mardi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_mercredi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $mercredi ."' ");
	$count_event_mercredi = mysql_num_rows($sql_programme_mercredi);
	echo '<span>Mercredi ('. $count_event_mercredi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '3') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_mercredi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_mercredi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_jeudi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $jeudi ."' ");
	$count_event_jeudi = mysql_num_rows($sql_programme_jeudi);
	echo '<span>Jeudi ('. $count_event_jeudi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '4') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_jeudi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_jeudi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_vendredi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $vendredi ."' ");
	$count_event_vendredi = mysql_num_rows($sql_programme_vendredi);
	echo '<span>Vendredi ('. $count_event_vendredi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '5') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_vendredi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_vendredi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_samedi = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $samedi ."' ");
	$count_event_samedi = mysql_num_rows($sql_programme_samedi);
	echo '<span>Samedi ('. $count_event_samedi .')</span>'
	. '<ul class="subMenu'; if(date('w') == '6') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_samedi, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_samedi == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>';

	echo '<li class="toggleTv">';
	$sql_programme_dimanche = mysql_query("SELECT id, web_tv, jeux, titre_jeux, titre, date, heure FROM ". WEB_TV_PROGRAMME ." WHERE date = '". $dimanche ."' ");
	$count_event_dimanche = mysql_num_rows($sql_programme_dimanche);
	echo '<span>Dimanche ('. $count_event_dimanche .')</span>'
	. '<ul class="subMenu'; if(date('w') == '0') echo ' this_day'; echo '">';
	while ($r_sql = mysql_fetch_array($sql_programme_dimanche, MYSQL_ASSOC)) {
		$date = strftime("%d", $r_sql['date']);
		echo '<li><img src="modules/Web_tv/images/jeux/16/'. $r_sql['jeux'] .'" alt="" title="'. $r_sql['titre_jeux'] .'" style="display:inline;" /> '. $r_sql['heure'] .' : <a href="index.php?file=Web_tv&amp;op=view_tv&amp;id='. $r_sql['web_tv'] .'" style="display:inline;">'. stripslashes($r_sql['titre']) .'</a></li>';

	}
	if($count_event_dimanche == 0) echo '<li>'. _NOEVENTWEBTV .'</li>';
	echo '</ul></li>'

	. '</ul></div></div>';
	$page = ob_get_contents();
	ob_end_clean();
	file_put_contents($cache, $page);
	echo $page;
}

?>



