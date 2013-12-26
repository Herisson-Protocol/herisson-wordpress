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
    $wkhtmltoimage = HERISSON_BASE_DIR."wkhtmltoimage-$type --load-error-handling ignore ";
    $options_nojs = " --disable-javascript ";
    $options_quality50 = " --quality 50 ";
    if (!file_exists($image) || filesize($image) == 0) {
        // echo "$wkhtmltoimage $options_quality50 \"$url\" $image<br>";
        exec("$wkhtmltoimage $options_quality50 \"$url\" $image", $output);
        // echo implode("\n", $output);
    }

    if (!file_exists($image) || filesize($image) == 0) {
        // echo "$wkhtmltoimage $options_nojs $options_quality50 \"$url\" $image";
        exec("$wkhtmltoimage $options_nojs $options_quality50 \"$url\" $image", $output);
        // echo implode("\n", $output);
    }
}

function herisson_screenshots_thumb($image, $thumb)
{
    $options = get_option('HerissonOptions');
    $convert = $options['convertPath'];
    if (file_exists($convert) && is_executable($convert)) {

        if (!file_exists($thumb) || filesize($thumb) == 0) {
            exec("$convert -resize 200x -crop 200x150 \"$image\" \"$thumb\"", $output);
            // echo implode("\n", $output);
        }
    }
}

function herisson_spider_fullpage($url, $directory)
{
    $default = "index.html";
    if (file_exists("$directory/$default")) {
        return false;
    }
    HerissonMessage::i()->addSucces("/usr/bin/wget --no-parent --timestamping --convert-links --page-requisites --no-directories --no-host-directories -erobots=off -P $directory ".'"<a href="'.$url.'" target="_blank">'.$url.'</a>"');
    exec("/usr/bin/wget --no-parent --timestamping --convert-links --page-requisites --no-directories --no-host-directories -erobots=off -P $directory ".'"'.$url.'"');
    $file = basename($url);
    if ($file) {
        HerissonMessage::i()->addSucces("mv \"$directory/$file\" \"$directory/index.html\"");
        exec("mv \"$directory/$file\" \"$directory/index.html\"");
    }
}




