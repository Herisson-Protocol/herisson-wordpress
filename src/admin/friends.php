<?php
/**
 * The admin interface for managing and editing friends.
 * @package herisson
 */

function herisson_friend_actions() {

    $action = param('action');
    switch ($action) {
        case 'add': herisson_friend_add();
        break;
        case 'edit': herisson_friend_edit();
        break;
        case 'submitedit': herisson_friend_submitedit();
        break;
        case 'list': herisson_friend_list();
        break;
        case 'delete': herisson_friend_delete();
        break;
        case 'approve': herisson_friend_approve();
        break;
        default: herisson_friend_list();
    }

}


/*
function herisson_friend_list() {
    require __DIR__."/views/friends-list.php";
}

function herisson_friend_list_custom($title,$friends) {
    require __DIR__."/views/friends-list-custom.php";
}
*/

function herisson_friend_add() {
    herisson_friend_edit(0);
}

function herisson_friend_edit($id=0) {


    $options = get_option('HerissonOptions');
    $dateTimeFormat = 'Y-m-d H:i:s';

    if ($id == 0) {
        $id = intval(param('id'));
    }
    if ($id == 0) {
        $existing = new WpHerissonFriends();
    } else {
        $existing = herisson_friend_get($id);
    }

    require __DIR__."/views/friends-edit.php";

}

function herisson_friend_submitedit() {

    $id = intval(post('id'));
    $url = post('url');
    $alias = post('alias');

    $new = $id == 0 ? true : false;
    if ($new) {
        $friend = new WpHerissonFriends();
        $friend->is_active = 0;
    } else {
        $friend = herisson_friend_get($id);
    }
    $friend->alias = $alias;
    $friend->url = $url;
    if ($new) {
        $friend->getInfo();
        $friend->askForFriend();
    }
    $friend->save();
    if ($new) { 
        if ($new && $friend->is_active) {
            success_add(__("Friend has been added and automatically validated"));
        } else {
            success_add(__("Friend has been added, but needs to be validated by him"));
        }
    } else {
        success_add(__("Friend saved"));
    }

    # Redirect to Friend edition
    herisson_friend_edit($friend->id);

}

function herisson_friend_delete() {
    $id = intval(param('id'));
    if ($id>0) {
        $friend = herisson_friend_get($id);
        $friend->delete();
    }

    # Redirect to Friends list
    herisson_friend_list();
}


function herisson_friend_approve() {
    $id = intval(param('id'));
    if ($id>0) {
        $friend = herisson_friend_get($id);
        if ($friend->validateFriend()) {
            success_add(__("Friend has been notified of your approvement"));
        } else {
            errors_add(__("Something went wrong while adding friendFriend has been notified of your approvement"));
        }
    }
    # Redirect to Friends list
    herisson_friend_list();
}


function herisson_friend_import() {
    if ( !empty($_POST['login']) && !empty($_POST['password'])) {
    }
}

