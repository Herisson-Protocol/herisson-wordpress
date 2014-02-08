<?php
/**
 * Export tool
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */

namespace Herisson;

/**
 * Class: Herisson\Export
 *
 * This is a tools class with static methods
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Export
{

    /**
     * Give content to be send to the browser
     *
     * @param string $content  the content data to download
     * @param string $filename the filename for the downloaded file
     *
     * @return void
     */
    public static function forceDownloadContent($content, $filename)
    {
        $temp = tempnam("/tmp", "herisson");
        file_put_contents($temp, $content);
        self::forceDownload($temp, $filename);
        unlink($temp);
    }


    /**
     * Send a file to gzip and send to the browser
     *
     * @param string $filepath the path of the file to send to the browser
     * @param string $filename the filename for the downloaded file
     *
     * @return void
     */
    public static function forceDownloadGzip($filepath, $filename)
    {
        $gzip = self::gzCompressFile($filepath);
        if ($gzip) { 
            self::forceDownload($gzip, $filename.".gz");
            unlink($gzip);
        }
    }


    /**
     * Force a file to be downloaded by the browser (not displayed inline)
     *
     * @param string $filepath the path of the file to send to the browser
     * @param string $filename the filename for the downloaded file
     *
     * @return void
     */
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
     * Gzip a file
     *
     * @param string  $source filename of the source
     * @param integer $level  the compression level (0 to 9)
     *
     * @return the destination filename for the gzipped file if everything went well, false otherwise
     */
    public static function gzCompressFile($source, $level=false)
    {
        $dest  = $source.'.gz';
        $mode  = 'wb'.$level;
        $error = false;
        if ($fpOut = gzopen($dest, $mode)) {
            if ($fpIn = fopen($source, 'rb')) {
                while (!feof($fpIn)) {
                    gzwrite($fpOut, fread($fpIn, 1024*512));
                }
                fclose($fpIn);
            } else { 
                $error = true;
            }
            gzclose($fpOut);
        } else { 
            $error = true;
        }
        if ($error) {
            return false;
        } else {
            return $dest;
        }
    } 


}


