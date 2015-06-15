Front
-----
* Improve search with keyword/tags in the frontend, and factorize it with backoffice search
* sass + webfonts

Maintenance
-----------
* Optionaly export private bookmarks
* Optionaly export tags / search

Refactoring
-----------
* Refactor Network with nice try/catch Exceptions mechanism, and delete globals
* Create a FriendCommunicator with Network and Friend object as parameter
* Create a BookmarkData object
* Refactor to avoid Message singletons

Backups
-------
* Add a backup feature to backup my bookmarks with my public key to my friends
* Configuration of authorization from friend backups
* Add backup size limit in local folder

Bookmarks
---------
* Enable multiple deletes
* Split WpHerissonBookmarks model with new WpHerissonBookmarksData class, to avoid WpHerissonBookmarks complexity

Divers
------
* Translate in french
* Translate in esperanto
* Add package header

Options
-------
* Add the possibility to hide from search engines

Graphism
--------
* Add Herisson Logo for homepage
* Add Herisson Logo for Wordpress Admin
* Beautiful website with CSS transforms

Search 
------
* Maybe cleanup the content of the bookmark and only keep <hX> tags content to quick-search in this short content

Documentation
-------------
* Generate PHPDoc

ORM
---
* Switch to ORM/Framework : Doctrine 2 / Propel / Silen

Install
-------
* Set chmod 755 to directories: log, data, screenshots, Herisson/Models/generated/
* Add a check for permissions on special folders
* Check that WpHerisson* Models class are correctly named if DB prefix != wp_

Security
--------
* Avoid XSS from locally backuped javascript

