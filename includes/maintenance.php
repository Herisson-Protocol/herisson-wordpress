<?

function herisson_export_firefox($bookmarks) {
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

	herisson_force_download($content,"herisson-bookmarks-firefox.html");
}

function herisson_export_json($bookmarks) {
	$list= array();
 foreach ($_bookmarks as $bookmark) { 
	 $list[] = $bookmark->toArray();
	}
	herisson_force_download_content(json_encode($list),"herisson-bookmarks.json");
 exit;
}


/** DOWNLOAD **/

function herisson_force_download_content($content,$filename) {
 $temp = tempnam("/tmp","herisson");
 file_put_contents($temp,$content);
	herisson_force_download_gzip($temp,"herisson-bookmarks.json");
 unlink($temp);
}

function herisson_force_download_gzip($filepath,$filename) {
 $gzip = gzcompressfile($filepath);
 if ($gzip) { 
 	herisson_force_download($gzip,$filename.".gz");
  unlink($gzip);
 }
}

function herisson_force_download($filepath,$filename) {
 $file = basename($filepath);

 # Special for IE
 header("Pragma: public");
 header("Cache-control: private");
 header("Content-Type: text/plain; charset=UTF-8");
 header("Content-Length: ". filesize($filepath));
 header("Content-Disposition: attachment; filename=$filename\n");

 flush();

 if (file_exists($filepath)) {
  $fd = fopen($filepath, "r");
  while(!feof($fd))
  {
   echo fread($fd, round(1024));
   flush();
  }
  fclose ($fd);
 }
 return;
}

function gzcompressfile($source,$level=false){
 $dest=$source.'.gz';
 $mode='wb'.$level;
 $error=false;
 if($fp_out=gzopen($dest,$mode)){
  if($fp_in=fopen($source,'rb')){
    while(!feof($fp_in))
        gzwrite($fp_out,fread($fp_in,1024*512));
    fclose($fp_in);
  } else { 
   $error=true;
  }
  gzclose($fp_out);
 } else { 
  $error=true;
 }
 if($error) { return false; }
 else { return $dest; }
} 

