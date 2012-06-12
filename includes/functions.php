<?php
/**
 * Functions for theming and templating.
 * @package herisson
 */

/**
 * Récupération et escaping d'une variable en POST
 * @param var le nom de la variable
 * @return la variable POST escapée
 */
function post($var) { return (! isset($_POST[$var]) ? '' : escape($_POST[$var])); }

/**
 * Récupération et escaping d'une variable en GET
 * @param var le nom de la variable
 * @return la variable GET escapée
 */
function get($var) { return (! isset($_GET[$var]) ? '' : escape($_GET[$var])); }

/**
 * Récupération et escaping d'une variable en POST (ou GET si pas de POST)
 * @param var le nom de la variable
 * @return la variable escapée
 */
function param($var) { $p = post($var); return $p ? $p : get($var); }

/**
 * Escaping en fonction du type de la variable, et de l'environnement.
 * @param str la variable string a escapée
 * @return la variable str escapée correctement
 */
function escape($str) {
 global $wpdb;
# if (! is_array($str) && !get_magic_quotes_gpc()) { return addslashes($str); }
 return $wpdb->escape($str);
}

#function remove_menus () {
#global $menu;
#	$restricted = array(__('Dashboard'), __('Posts'), __('Media'), __('Links'), __('Pages'), __('Appearance'), __('Tools'), __('Users'), __('Settings'), __('Comments'), __('Plugins'), __('Herisson'));
#	end ($menu);
#	while (prev($menu)){
#		$value = explode(' ',$menu[key($menu)][0]);
#		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
#	}
#}
#add_action('admin_menu', 'remove_menus');

function herisson_download($url,$post=array()) {

 if (function_exists('curl_init')) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		if (sizeof($post)) {
	  curl_setopt($curl, CURLOPT_POST,TRUE);
	  curl_setopt($curl, CURLOPT_POSTFIELDS,$post);
		}
	
 	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
  if($httpCode >= 400) {
 	 return WP_Error('herisson',sprintf(__("The address %s returns a %s error.",HERISSONTD),$url,$httpCode));
		}
  $result =  curl_exec($curl);

		curl_close($curl);
		return $result;
 } else {
	 return WP_Error('herisson',__('php-curl library is missing.',HERISSONTD));
	}

}

?>
