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

define('WEB_TV', $nuked['prefix'] .'_web_tv');
define('WEB_TV_PROGRAMME', $nuked['prefix'] .'_web_tv_programme');

// changer le temps de la mise en cache du block, valeur en seconde
define('CACHE_TIME', '3600');

?>