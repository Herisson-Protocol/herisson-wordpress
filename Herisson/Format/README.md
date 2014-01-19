Create a new format
===================

This Herisson wordpress plugin allows to easily create an import/export file format for bookmarks.

If you feel like creating a new file, format, please do.

Here are some useful examples :

File import
-----------



    class MyFormat extends Herisson\Format
    {

        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {
            $this->name      = "MyFormat";
            $this->type      = "file";
            $this->keyword   = "your_keyword";
        }

        /**
         * Generate MyFormat bookmarks file and send it to the user
         *
         * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
         *
         * @see WpHerissonBookmarks
         *
         * @return void
         */
        public function export($bookmarks)
        {
            // Create your file
            foreach ($bookmarks as $bookmark) {
                // feed it
            }
            // force file download
            Herisson\Export::forceDownloadGzip($filename, "herisson-bookmarks.extension");
            // delete file
            unlink($filename);

        }


        /**
         * Handle the importation of MyFormat file
         *
         * @return a list of WpHerissonBookmarks
         */
        public function import()
        {
            $this->preImport();

            // get file
            $filename  = $_FILES['import_file']['tmp_name'];

            // create a new array of bookmarks
            $bookmarks = array();

            foreach ($parse_your_file) {
                $bookmark = new WpHerissonBookmarks();
                // feed bookmark object
                $bookmarks[] = $bookmark;
            }

            // return an array of bookmarks
            return $bookmarks;
        }
    }


Other import
------------



    class MyFormat extends Herisson\Format
    {

        /**
         * Constructor
         *
         * @return void
         */
        public function __construct()
        {
            $this->name      = "MyFormat";
            $this->type      = "any_other_type";
            $this->keyword   = "your_keyword";
        }

        /**
         * Generate MyFormat bookmarks file and send it to the user
         *
         * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
         *
         * @see WpHerissonBookmarks
         *
         * @return void
         */
        public function export($bookmarks)
        {
            foreach ($bookmarks as $bookmark) {
                // send them where you want
            }
        }


        /**
         * Print the form to get your data
         *
         * @return void
         */
        public function getForm()
        {
            // create your form as you want, with the needed fields named like you want
            ?>
            <?php echo __('Login', HERISSON_TD); ?> :<input type="text" name="username_delicious" /><br/>
            <?php echo __('Password', HERISSON_TD); ?> :<input type="password" name="password_delicious" /><br/>
            <?php
        }


        /**
         * Handle the importation of MyFormat file
         *
         * @return a list of WpHerissonBookmarks
         */
        public function import()
        {
            $this->preImport();

            // create a new array of bookmarks
            $bookmarks = array();

            foreach ($get_data_from_where_you_want) {
                $bookmark = new WpHerissonBookmarks();
                // feed bookmark object
                $bookmarks[] = $bookmark;
            }

            // return an array of bookmarks
            return $bookmarks;
        }
    }

