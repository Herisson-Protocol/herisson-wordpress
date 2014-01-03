<?php
/**
 * Friend Format extension
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */


/**
 * Class to handle Friend bookmarks transfer
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class HerissonFormatFriend extends HerissonFormat
{

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->name    = "Friend transfer";
        $this->type    = "friend";
        $this->keyword = "friend";
    }


    /**
     * Print the form to select friend and tags
     *
     * @return void
     */
    public function getForm()
    {

        $friends = WpHerissonFriendsTable::getActives();
        ?>
        <select name="friendId">
            <option value=""><?php echo __('Choose one of your active friend', HERISSON_TD); ?></option>
        <?php
        foreach ($friends as $friend) { ?>
            <option value="<?php echo $friend->id; ?>"><?php echo $friend->name ?> (<?php echo $friend->alias ?>)</option>
        <?php
        } ?>
        </select>
        <br/><br/>
    
        <label>
            <?php echo __('Keyword (optional)', HERISSON_TD); ?>:<br/>
            <input type="text" name="keyword" placeholder="add a keyword to be more specific" style="width: 300px" />
        </label>
        <br/>

        <?php
    }

    /**
     * Handle the importation of bookmarks from a friend
     *
     * @return a list of WpHerissonBookmarks
     */
    public function import()
    {
        $friendId = post('friendId');
        if (! $friendId) {
            throw new HerissonFormatException("Missing friend Id");
        }
        $friend = WpHerissonFriendsTable::get(post('friendId'));
        if (! $friend->id) {
            throw new HerissonFormatException("Unknown friend");
        }
        $bookmarks = $friend->retrieveBookmarks();

        return $bookmarks;


    }



}


