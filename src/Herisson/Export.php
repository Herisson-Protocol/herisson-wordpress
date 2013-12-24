<?php


class HerissonExport
{

    public static function forceDownloadContent($content, $filename)
    {
        $temp = tempnam("/tmp", "herisson");
        file_put_contents($temp, $content);
        self::forceDownloadGzip($temp, "herisson-bookmarks.json");
        unlink($temp);
    }

    public static function forceDownloadGzip($filepath, $filename)
    {
        $gzip = self::gzCompressFile($filepath);
        if ($gzip) { 
            self::forceDownload($gzip, $filename.".gz");
            unlink($gzip);
        }
    }
    public static function forceDownload($filepath, $filename)
    {
        $file = basename($filepath);

        // Special for IE
        header("Pragma: public");
        header("Cache-control: private");
        header("Content-Type: text/plain; charset=UTF-8");
        header("Content-Length: ". filesize($filepath));
        header("Content-Disposition: attachment; filename=$filename\n");

        flush();

        if (file_exists($filepath)) {
            $fd = fopen($filepath, "r");
            while (!feof($fd)) {
                echo fread($fd, round(1024));
                flush();
            }
            fclose($fd);
        }
        return;
    }




    /**
     *
     */
    public static function exportFirefox($bookmarks)
    {
         $bookmarks = WpHerissonBookmarksTable::getAll();
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

        self::forceDownloadContent($content, "herisson-bookmarks-firefox.html");
    }

    public static function exportJson($bookmarks)
    {
        $list= array();
        foreach ($bookmarks as $bookmark) { 
            $list[] = $bookmark->toArray();
        }
        self::forceDownloadContent(json_encode($list), "herisson-bookmarks.json");
        exit;
    }

    public static function gzCompressFile($source, $level=false)
    {
        $dest=$source.'.gz';
        $mode='wb'.$level;
        $error=false;
        if ($fp_out=gzopen($dest, $mode)) {
            if ($fp_in=fopen($source, 'rb')) {
                while (!feof($fp_in))
                    gzwrite($fp_out, fread($fp_in, 1024*512));
                fclose($fp_in);
            } else { 
                $error=true;
            }
            gzclose($fp_out);
        } else { 
            $error=true;
        }
        if ($error) {
            return false;
        } else {
            return $dest;
        }
    } 


}


