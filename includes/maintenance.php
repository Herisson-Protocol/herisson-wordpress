<?

function herisson_export_firefox($options) {
	$bookmarks = herisson_bookmark_all();
	$now = time();
	$name = "Herisson bookmarks";
 $content = '
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks menu</H1>

<DL><p>
    <DT><H3 ADD_DATE="'.$now.'" LAST_MODIFIED="'.$now.'">'.$name.'</H3>
    <DL><p>';
	foreach ($bookmarks as $bookmark) {
        $content .= '<DT><A HREF="'.$bookmark->url.'" ADD_DATE="'.$now.'" LAST_MODIFIED="'.$now.'" ICON_URI="'.$bookmark->favicon_url.'" ICON="data:image/png;base64,'.$bookmark->favicon_image.'">'.$bookmark->title.'</A>
								<DD>'.$bookmark->description.'
								';

	 }
		$content .= '</DL>
		</DL>
		';

	herisson_force_download($content);
}

function herisson_force_download($content) {

}


