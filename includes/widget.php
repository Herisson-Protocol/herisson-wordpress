<?php
/**
 * Adds our widget.
 * @package herisson
 */

function herisson_widget($args) {
    extract($args);

    $options = get_option('HerissonWidget');
    $title = $options['title'];

    echo $before_widget . $before_title . $title . $after_title;
    if( !defined('HERISSON_VERSION') || floatval(HERISSON_VERSION) < 1.0 ) {
        echo "<p>" . _e("You don't appear to have the Herisson plugin installed, or have an old version; you'll need to install or upgrade before this widget can display your data.", HERISSONTD) . "</p>";
    } else {
        herisson_load_template('sidebar.php');
    }
    echo $after_widget;
}

function herisson_widget_control() {
    $options = get_option('HerissonWidget');

    if ( !is_array($options) )
        $options = array('title' => 'Herisson');

    if ( $_POST['HerissonSubmit'] ) {
        $options['title'] = htmlspecialchars(stripslashes($_POST['HerissonTitle']), ENT_QUOTES, 'UTF-8');
        update_option('HerissonWidget', $options);
    }

    $title = htmlspecialchars($options['title'], ENT_QUOTES, 'UTF-8');

    echo '
		<p style="text-align:right;">
			<label for="HerissonTitle">Title:
				<input style="width: 200px;" id="HerissonTitle" name="HerissonTitle" type="text" value="'.$title.'" />
			</label>
		</p>
	<input type="hidden" id="HerissonSubmit" name="HerissonSubmit" value="1" />
	';
}

function herisson_widget_init() {
    if ( !function_exists('register_sidebar_widget') )
        return;

    register_sidebar_widget(__('Herisson', HERISSONTD), 'herisson_widget', null, 'herisson');
    register_widget_control(__('Herisson', HERISSONTD), 'herisson_widget_control', 300, 100, 'herisson');
}

add_action('plugins_loaded', 'herisson_widget_init');

?>
