<?php
/**
 * Functions for theming and templating.
 * @package herisson
 */

/**
 * Récupération et escaping d'une variable en POST
 *
 * @param var le nom de la variable
 * @return la variable POST escapée
 */
function post($var) {
    return (! isset($_POST[$var]) ? '' : escape($_POST[$var]));
}


/**
 * Récupération et escaping d'une variable en GET
 *
 * @param var le nom de la variable
 * @return la variable GET escapée
 */
function get($var) {
    return (! isset($_GET[$var]) ? '' : escape($_GET[$var])); 
}

/**
 * Récupération et escaping d'une variable en POST (ou GET si pas de POST)
 *
 * @param var le nom de la variable
 * @return la variable escapée
 */
function param($var) {
    $p = post($var);
    return $p ? $p : get($var);
}

/**
 * Escaping en fonction du type de la variable, et de l'environnement.
 *
 * @param str la variable string a escapée
 * @return la variable str escapée correctement
 */
function escape($str) {
    return $str; #esc_sql($str);
}

function errors_dispatch($content, $errors) {
    $error_code = $content->get_error_data("herisson");
    foreach ($errors as $code=>$message) {
        if ($error_code == $code) {
            HerissonMessage::i()->addError($message);
        }
    }
    HerissonMessage::i()->addError(__($content->get_error_message("herisson"), HERISSON_TD));
}

function format_size($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) {
    $size /= 1024;
   }
    return round($size, 2).$units[$i];
}

function include_partial($view, $data) {
    foreach ($data as $var=>$value) {
        $$var = $value;
    }
    require $view;
}



