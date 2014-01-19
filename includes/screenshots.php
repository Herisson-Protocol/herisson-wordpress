<?php

function herisson_screenshots_wkhtmltoimage_amd64($url, $image)
{
    return herisson_screenshots_wkhtmltoimage('amd64', $url, $image);
}

function herisson_screenshots_wkhtmltoimage_i386($url, $image)
{
    return herisson_screenshots_wkhtmltoimage('i386', $url, $image);
}

function herisson_screenshots_wkhtmltoimage($type, $url, $image)
{
    // ./wkhtmltoimage-amd64 --disable-javascript --quality 50 http://www.wilkins.fr/ /home/web/www.wilkins.fr/google.png
    $wkhtmltoimage = HERISSON_BASE_DIR."wkhtmltoimage-$type";
    $options = " --load-error-handling ignore ";
    $options_nojs = " --disable-javascript ";
    $options_quality50 = " --quality 50 ";
    if (!file_exists($image) || filesize($image) == 0) {
        // echo "$wkhtmltoimage $options_quality50 \"$url\" $image<br>";
        $output = Herisson\Shell::shellExec($wkhtmltoimage, "$options $options_quality50 \"$url\" $image");
        // echo implode("\n", $output);
    }

    if (!file_exists($image) || filesize($image) == 0) {
        // echo "$wkhtmltoimage $options_nojs $options_quality50 \"$url\" $image";
        $output = Herisson\Shell::shellExec($wkhtmltoimage, "$options $options_nojs $options_quality50 \"$url\" $image");
        // echo implode("\n", $output);
    }
}

function herisson_screenshots_thumb($image, $thumb)
{
    if (!file_exists($thumb) || filesize($thumb) == 0) {
        $output = Herisson\Shell::shellExec("convert", "-resize 200x -crop 200x150 \"$image\" \"$thumb\"");
        // echo implode("\n", $output);
    }
}




