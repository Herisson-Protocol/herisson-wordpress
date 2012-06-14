<?

function herisson_screenshots_wkhtmltoimage_amd64($url,$image) {
 return herisson_screenshots_wkhtmltoimage('amd64',$url,$image);
}

function herisson_screenshots_wkhtmltoimage_i386($url,$image) {
 return herisson_screenshots_wkhtmltoimage('i386',$url,$image);
}

function herisson_screenshots_wkhtmltoimage($type,$url,$image) {
		$wkhtmltoimage = HERISSON_BASE_DIR."wkhtmltoimage-$type --load-error-handling ignore ";
		$options_nojs = " --disable-javascript ";
		$options_quality50 = " --quality 50 ";
		if (!file_exists($image) || filesize($image) == 0) {
# 		echo "$wkhtmltoimage $options_quality50 \"$url\" $image<br>";
 		exec("$wkhtmltoimage $options_quality50 \"$url\" $image",$output);
#		 echo implode("\n",$output);
		}

		if (!file_exists($image) || filesize($image) == 0) {
# 		echo "$wkhtmltoimage $options_nojs $options_quality50 \"$url\" $image";
 		exec("$wkhtmltoimage $options_nojs $options_quality50 \"$url\" $image",$output);
#		 echo implode("\n",$output);
		}
}
