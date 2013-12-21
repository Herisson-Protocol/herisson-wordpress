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
# if (! is_array($str) && !get_magic_quotes_gpc()) { return addslashes($str); }
 return $str; #esc_sql($str);
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

/**
 * Tableau des erreurs
 */
$ERRORS = array();

/**
 * Ajoute une erreur à la liste.
 */
function errors_add($e) {
 global $ERRORS;
	echo "<span class=\"herisson-errors\">$e</span><br>";
 array_push($ERRORS,$e);
}

/**
 * Récupère le tableau des erreurs
 */
function errors_get() {
 global $ERRORS;
 return $ERRORS;
}

/**
 * Vérifie si des erreurs existent
 */
function errors_length() {
 global $ERRORS;
 return sizeof($ERRORS);
}

/**
 * Tableau des succes
 */
$SUCCESS = array();

/**
 * Ajoute une erreur à la liste.
 */
function success_add($e) {
 global $SUCCESS;
 array_push($SUCCESS,$e);
}

/**
 * Récupère le tableau des succes
 */
function success_get() {
 global $SUCCESS;
 return $SUCCESS;
}

/**
 * Vérifie si des succes existent
 */
function success_length() {
 global $SUCCESS;
 return sizeof($SUCCESS);
}

function herisson_messages() {
 if (success_length()) { ?>
<p class="herisson-success">
<? foreach (success_get() as $success) { echo $success."<br>"; } ?>
</p>
<?  }
 if (errors_length()) { ?>
<p class="herisson-errors">
<? foreach (errors_get() as $errors) { echo $errors."<br>"; } ?>
</p>
<?  }
}


function errors_dispatch($content,$errors) {
 $error_code = $content->get_error_data("herisson");
 foreach ($errors as $code=>$message) {
  if ($error_code == $code) {
   errors_add($message);
  }
 }
 errors_add(__($content->get_error_message("herisson"),HERISSON_TD));

}

function format_size($size) {
 $units = array(' B', ' KB', ' MB', ' GB', ' TB', 'PB');
 for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
 return round($size, 2).$units[$i];
}


/**
 * Pagination 
	*/
function pagination_get_vars() {
 $options = get_option('HerissonOptions');
	#print_r($options);
 return array(
	 'offset' => param('offset'),
	 'limit' => $options['bookmarksPerPage'],
	);
}


