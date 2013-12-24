<?php


class HerissonRouter
{

    /**
     * Handles routing in Wordpress Plugin Admin Zone
     *
     * @return void
     */
    function route()
    {
        $pageName = ucfirst(str_replace("herisson_", "", get('page')));
        $baseController = "HerissonControllerAdmin";
        $controllerName = $baseController.$pageName;
        $defaultName = "Bookmark";
        if ($pageName == "Menu" || !class_exists($controllerName)) {
            $defaultController = $baseController.$defaultName;
            $controller = new $defaultController;
        } else {
            $controller = new $controllerName;
        }
        $controller->route();
    }

    /**
     * Handles routing in Wordpress Plugin Admin Zone, in raw format.
     * 
     * This is useful to generate and downloads files (eg: .gzip files).
     * It handles the routing the same way as route() except it exit right
     * after to avoid display of wordpress template (with menus and stuff)
     *
     * @return void
     */
    function routeRaw()
    {
        $this->route();
        exit;
    }

}

