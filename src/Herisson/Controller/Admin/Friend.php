<?

require __DIR__."/../Admin.php";

class HerissonControllerAdminFriend extends HerissonControllerAdmin {


    function __construct() {
        parent::__construct();
        $this->name = "friend";
    }

    function indexAction() {

        $this->view->actives  = WpHerissonFriendsTable::getWhere("is_active=1");
        $this->view->youwant  = WpHerissonFriendsTable::getWhere("b_youwant=1");
        $this->view->wantsyou = WpHerissonFriendsTable::getWhere("b_wantsyou=1");
        $this->view->errors   = WpHerissonFriendsTable::getWhere("b_wantsyou!=1 and b_youwant!=1 and is_active!=1");

    }
/*
    function listAction($title,$friends) {
#        require __DIR__."/views/friends-list-custom.php";
    }


    function listActiveAction() {
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where('is_active=1')
            ->execute();
        $this->Action(__("Active friends", HERISSON_TD), $friends);
}

    function listYouwantAction() {
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where('b_youwant=1')
            ->execute();
        $this->listAction(__("Waiting for friend approval", HERISSON_TD), $friends);
    }

    function listWantsyouAction() {
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where('b_wantsyou=1')
            ->execute();
        $this->listAction(__("Asking your permission", HERISSON_TD), $friends);
    }

    function listErrorAction() {
        $friends = Doctrine_Query::create()
            ->from('WpHerissonFriends')
            ->where('b_wantsyou!=1 and b_youwant!=1 and is_active!=1')
            ->execute();
        $this->listAction(__("Others", HERISSON_TD), $friends);
    }
*/

 
}


