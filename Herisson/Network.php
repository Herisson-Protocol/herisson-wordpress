<?php
/**
 * Herisson\Network
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
 * HTTP Code
 *
 * @source http://www.checkupdown.com/status/E417.html
 */
$http_codes = array(
    "100" => "Continue",
    "101" => "Switching Protocols",
    "102" => "Processing (WebDAV; RFC 2518)",

    "200" => "OK",
    "201" => "Created",
    "202" => "Accepted",
    "203" => "Non-Authoritative Information (since HTTP/1.1)",
    "204" => "No Content",
    "205" => "Reset Content",
    "206" => "Partial Content",
    "207" => "Multi-Status (WebDAV; RFC 4918)",
    "208" => "Already Reported (WebDAV; RFC 5842)",
    "226" => "IM Used (RFC 3229)",

    "300" => "Multiple Choices",
    "301" => "Moved Permanently",
    "302" => "Found",
    "303" => "See Other (since HTTP/1.1)",
    "304" => "Not Modified",
    "305" => "Use Proxy (since HTTP/1.1)",
    "306" => "Switch Proxy",
    "307" => "Temporary Redirect (since HTTP/1.1)",
    "308" => "Permanent Redirect (experimental Internet-Draft)[10]",

    "400" => "Bad Request",
    "401" => "Unauthorized",
    "402" => "Payment Required",
    "403" => "Forbidden",
    "404" => "Not Found",
    "405" => "Method Not Allowed",
    "406" => "Not Acceptable",
    "407" => "Proxy Authentication Required",
    "408" => "Request Timeout",
    "409" => "Conflict",
    "410" => "Gone",
    "411" => "Length Required",
    "412" => "Precondition Failed",
    "413" => "Request Entity Too Large",
    "414" => "Request-URI Too Long",
    "415" => "Unsupported Media Type",
    "416" => "Requested Range Not Satisfiable",
    "417" => "Expectation Failed",
    "418" => "I'm a teapot (RFC 2324)",
    "420" => "Enhance Your Calm (Twitter)",
    "422" => "Unprocessable Entity (WebDAV; RFC 4918)",
    "423" => "Locked (WebDAV; RFC 4918)",
    "424" => "Failed Dependency (WebDAV; RFC 4918)",
    "424" => "Method Failure (WebDAV)[13]",
    "425" => "Unordered Collection (Internet draft)",
    "426" => "Upgrade Required (RFC 2817)",
    "428" => "Precondition Required (RFC 6585)",
    "429" => "Too Many Requests (RFC 6585)",
    "431" => "Request Header Fields Too Large (RFC 6585)",
    "444" => "No Response (Nginx)",
    "449" => "Retry With (Microsoft)",
    "450" => "Blocked by Windows Parental Controls (Microsoft)",
    "451" => "Unavailable For Legal Reasons (Internet draft)",
    "499" => "Client Closed Request (Nginx)",

    "500" => "Internal Server Error",
    "501" => "Not Implemented",
    "502" => "Bad Gateway",
    "503" => "Service Unavailable",
    "504" => "Gateway Timeout",
    "505" => "HTTP Version Not Supported",
    "506" => "Variant Also Negotiates (RFC 2295)",
    "507" => "Insufficient Storage (WebDAV; RFC 4918)",
    "508" => "Loop Detected (WebDAV; RFC 5842)",
    "509" => "Bandwidth Limit Exceeded (Apache bw/limited extension)",
    "510" => "Not Extended (RFC 2774)",
    "511" => "Network Authentication Required (RFC 6585)",
    "598" => "Network read timeout error (Unknown)",
    "599" => "Network connect timeout error (Unknown)",
);

$mime_types = array(
    "text/html",
    "image/png",
    "image/jpg",
    "image/jpeg",
    "image/gif",
);

/**
 * Herisson\Network
 *
 * @category Tools
 * @package  Herisson
 * @author   Thibault Taillandier <thibault@taillandier.name>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 * @see      None
 */
class Network
{
    /**
     * Code
     */
    public $code = 0;

    /**
     * Message
     */
    public $message = "";

    /**
     * Content
     */
    public $content = "";

    /**
     * Error code
     */
    public $error = 0;

    /**
     * HTTP code
     */
    public $http_code = 0;

    /**
     * HTTP Message
     */
    public $http_message = "";

    /**
     * Constructor
     *
     * TODO : Fix this doc
     *
     * @param integer $code    the http code
     * @param string  $message the message
     * @param string  $content the content
     */
    public function __construct($code='', $message='', $content='')
    {
        $this->code = $code;
        $this->message = $message;
        $this->content = $content;
    }

    /**
     * Get a curl object
     *
     * @param string $url  the URL to download
     * @param array  $post the data to send via POST method
     *
     * @throws an Exception in case php-curl is missing
     *
     * @return the curl object
     */
    public function getCurl($url, $post=null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            if (sizeof($post)) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            }
            return $curl;
        } else {
            HerissonMessage::i()->addError(__('php-curl library is missing.', HERISSON_TD));
            throw new Exception(__('php-curl library is missing.', HERISSON_TD));
        }
    }

    /**
     * Download an URL
     *
     * @param string $url  the URL to download
     * @param array  $post the data to send via POST method
     *
     * @return the text content
     */
    public function download($url, $post=array())
    {
        $curl = $this->getCurl($url, $post);
        
        $content =  curl_exec($curl);
        $this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($this->http_code >= 400) {
            global $http_codes;
            $this->error = 1;
            $this->http_message = $http_codes[$this->http_code];
            throw new Network\Exception(sprintf(__("The site %s returned a %s error (%s).", HERISSON_TD),
                $url, $this->http_code, $this->http_message),
                $this->http_code);
        }

        $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $result = array(
            "data"  => $content,
            "type"  => $content_type,
            "code"  => $this->http_code
        );

        curl_close($curl);
        return $result;
    }

    /**
     * Check an URL
     *
     * @param string $url the URL to download
     *
     * @return the HTTP status
     */
    public function check($url)
    {
        $curl = $this->getCurl($url);
        $result =  curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return $this->httpData($httpCode);
    }

    /**
     * Create a HTTP status from the HTTP Code
     *
     * @param integer $code the HTTP Code
     *
     * @return an array with the HTTP status information
     */
    public function httpData($code)
    {
        global $http_codes;
        if (array_key_exists($code, $http_codes)) {
            $error = 0;
            if (intval($code)>=400) {
                $error = 1;
            }
            return array("code" => $code, "message" =>  $http_codes[$code], "error" => $error);
        } else {
            return array("code" => $code, "message" => __("HTTP code not found", HERISSON_TD), "error" => 1);
        }
    }

    /**
     * Send a header reply with a specific HTTP Code
     *
     * @param integer $code the HTTP code to send to the client
     * @param boolean $exit whether it should exit after sending HTTP response
     *
     * @return void
     */
    public static function reply($code, $exit=0)
    {
        global $http_codes;
        if (array_key_exists($code, $http_codes)) {
            if (!headers_sent()) {
                header("HTTP/1.1 $code ".$http_codes[$code]);
                if ($exit) {
                    exit;
                }
            } else {
                echo __("Headers already sent.\n", HERISSON_TD);
                echo $http_codes[$code];
            }
        } else {
            echo __("Error, HTTP code $code does not exist.", HERISSON_TD);
        }
    }


}

