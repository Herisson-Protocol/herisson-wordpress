<?php
/**
 * Updates our options
 * @package herisson
 */

if ( !empty($_POST['update']) ) {
    require '../../../../wp-config.php';

    check_admin_referer('herisson-update-options');

    $_POST = stripslashes_deep($_POST);

    $append = '';

	$options['AWSAccessKeyId']  = trim($_POST['AWSAccessKeyId']);
    $options['SecretAccessKey'] = trim($_POST['SecretAccessKey']);
    $options['formatDate']		= trim($_POST['format_date']);
    $options['associate']		= trim($_POST['associate']);
    $options['ignoreTime']		= trim($_POST['ignore_time']);
    $options['hideAddedDate']	= trim($_POST['hide_added_date']);
    $options['domain']			= trim($_POST['domain']);
    $options['debugMode']		= trim($_POST['debug_mode']);
    $options['useModRewrite']   = trim($_POST['use_mod_rewrite']);
    $options['menuLayout']		= ( trim($_POST['menu_layout']) == 'single' ) ? HERISSON_MENU_SINGLE : HERISSON_MENU_MULTIPLE;
    $options['proxyHost']		= trim($_POST['proxy_host']);
    $options['proxyPort']		= trim($_POST['proxy_port']);
    $options['booksPerPage']    = trim($_POST['books_per_page']);
    $options['defBookCount']    = trim($_POST['def_book_count']);
    $options['hideCurrentBooks'] = trim($_POST['hide_current_books']);
    $options['hidePlannedBooks'] = trim($_POST['hide_planned_books']);
    $options['hideFinishedBooks'] = trim($_POST['hide_finished_books']);
    $options['hideBooksonHold'] = trim($_POST['hide_books_on_hold']);
	$options['hideViewLibrary'] = trim($_POST['hide_view_library']);
    $options['permalinkBase']   = trim($_POST['permalink_base']);
	$options['templateBase']	= trim($_POST['template_base']);
    $options['multiuserMode']   = trim($_POST['multiuser_mode']);

    $herisson_url->load_scheme($options['menuLayout']);

    switch ( $_POST['image_size'] ) {
        case 'Small':
        case 'Medium':
        case 'Large':
            $options['imageSize'] = $_POST['image_size'];
            break;
        default:
            $append .= '&imagesize=1';
            $options['imageSize'] = __('Small', HERISSON_TD);
            break;
    }

// Added Begin
    switch ( $_POST['limage_size'] ) {
        case 'Small':
        case 'Medium':
        case 'Large':
            $options['limageSize'] = $_POST['limage_size'];
            break;
        default:
            $append .= '&limagesize=1';
            $options['limageSize'] = __('Medium', HERISSON_TD);
            break;
    }
// Added End

    if ( $_POST['http_lib'] == 'curl' ) {
        if ( !function_exists('curl_init') ) {
            $options['httpLib'] = 'snoopy';
            $append .= '&curl=1';
        } else {
            $options['httpLib'] = 'curl';
        }
    } else {
        $options['httpLib'] = 'snoopy';
    }

    update_option('HerissonOptions', $options);

    global $wp_rewrite;
    if ($wp_rewrite->using_mod_rewrite_permalinks() ) {
        herisson_mod_rewrite($wp_rewrite->rewrite_rules() );
        $wp_rewrite->flush_rules();
    }

    wp_redirect($herisson_url->urls['options'] . "&updated=1$append");
    die;
}

?>
