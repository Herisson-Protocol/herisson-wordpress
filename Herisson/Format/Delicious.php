<?php
/**
 * Delicious Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson\Format;

/**
 * Class to handle Basic Delicious format
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Delicious extends \Herisson\Format
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name    = "Delicious transfer";
        $this->type    = "delicious";
        $this->keyword = "delicious";
    }


    /**
     * Generate Delicious bookmarks file and send it to the user
     *
     * @param array $bookmarks a bookmarks array, made of WpHerissonBookmarks items
     *
     * @see WpHerissonBookmarks
     *
     * @return void
     */
    public function export($bookmarks)
    {
    }


    /**
     * Print the form to get Login / Password information for Delicious Connect
     *
     * @return void
     */
    public function getForm()
    {
        ?>

        <?php echo __('Login', HERISSON_TD); ?> :<input type="text" name="username_delicious" /><br/>
        <?php echo __('Password', HERISSON_TD); ?> :<input type="password" name="password_delicious" /><br/>
        <?php echo __("These credentials are not stored.", HERISSON_TD); ?>

        <?php
    }


    /**
     * Handle the importation of Delicious bookmarks, from username/password provided by the user
     *
     * Use external library DeliciousBrownies to talk to Delicious API
     *
     * @see DeliciousBrownies
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $username = post('username_delicious');
        $password = post('password_delicious');
        if (!$username || !$password) {
            echo __("Delicious login and password not complete.", HERISSON_TD);
            $this->indexAction();
            $this->setView('index');
            exit;
        }
        include HERISSON_VENDOR_DIR."delicious/DeliciousBrownies.php";
        $d = new DeliciousBrownies;
        $d->setUsername($username);
        $d->setPassword($password);
        // Call https://api.del.icio.us/v1/posts/all
        $deliciousBookmarks = $d->getAllPosts();

        if (!$deliciousBookmarks) {
            echo __("Someting went wrong while fetching Delicious bookmarks. (Eg. Wrong login/password, no bookmarks etc)", HERISSON_TD);
            exit;
        }

        $list = array();

        $page_title = __("Importation results from Delicious bookmarks", HERISSON_TD);

        foreach ($deliciousBookmarks as $b) {
            $bookmark = array();
            $bookmark['url'] = $b['href'];
            $bookmark['title'] = $b['description'];
            $bookmark['description'] = $b['extended'];
            $bookmark['is_public'] = $b['private'] == 'yes' ? 0 : 1;
            $bookmark['tags'] = preg_replace("/ +/", ",", $b['tag']);
            $bookmark['prefix'] = false;
            $bookmark['favicon_url'] = "";
            $bookmark['favicon_image'] = "";

            $list[] = $bookmark;
        }
        unset($deliciousBookmarks);
        return $list;

    }


}


