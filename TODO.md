Front
-----
* Improve search with keyword/tags in the frontend, and factorize it with backoffice search
* css

Maintenance
-----------
* Optionaly export private bookmarks
* Optionaly export tags / search

Refactoring
-----------
* Refactor Network with nice try/catch Exceptions mechanism
* Refactor to avoid Message singletons

Backups
-------
* Add a backup feature to backup my bookmarks with my public key to my friends
* Configuration of authorization from friend backups
* Add backup size limit in local folder

Bookmarks
---------
* Enable multiple deletes

Divers
------
* Translate in french
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

Doctrine
--------
* Update to Doctrine 2

Install
-------
* Set chmod 755 to directories: log, data, screenshots, Herisson/Models/generated/
* Add a check for permissions on special folders
* Check that WpHerisson* Models class are correctly named if DB prefix != wp_

