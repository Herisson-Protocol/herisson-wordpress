<?


class HerissonPagination {

    /**
     * singleton
     * @var HerissonPagination
     */
    public static $i;

    /**
     * Creating singleton
     * @return HerissonPagination instance
     */
    public static function i()
    {
        if(is_null(self::$i)) {
            self::$i = new HerissonPagination();
        }
        return self::$i;
    }

    /**
     * retrieve pagination parameters
     *
     * @return array with 2 parameters : offset (current pagination offset), and limit (maximum items per pages)
     */
    public static function getVars() {
        $options = get_option('HerissonOptions');
        return array(
            'offset'    => param('offset'),
            'page'      => param('page'),
            'limit'     => 10, #$options['bookmarksPerPage'],
        );
    }





}


