<?
# http://www.checkupdown.com/status/E417.html
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

#function herisson_network_download($url,$post=array()) {
#
# if (function_exists('curl_init')) {
#  $curl = curl_init();
#  curl_setopt($curl, CURLOPT_URL, $url);
#  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
#  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
#  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
#		if (sizeof($post)) {
#	  curl_setopt($curl, CURLOPT_POST,TRUE);
#	  curl_setopt($curl, CURLOPT_POSTFIELDS,$post);
#		}
#	
#  $result =  curl_exec($curl);
# 	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
##	 echo "$url => $httpCode<br>";
#  if($httpCode >= 400) {
#		 global $http_codes;
#		 $httpText = $http_codes[$httpCode];
# 	 return new WP_Error('herisson',sprintf(__("The address %s returns a %s error (%s).",HERISSON_TD),$url,$httpCode,$httpText),$httpCode);
#		}
#
#		curl_close($curl);
#		return $result;
# } else {
#	 return new WP_Error('herisson',__('php-curl library is missing.',HERISSON_TD));
#	}
#
#}


#function herisson_network_check($url) {
#
# if (function_exists('curl_init')) {
#  $curl = curl_init();
#  curl_setopt($curl, CURLOPT_URL, $url);
#  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
#  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
#  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
#		curl_setopt($curl, CURLOPT_NOBODY, true);
##  curl_setopt($curl, CURLOPT_VERBOSE, 1);
#  $result =  curl_exec($curl);
# 	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
#  return herisson_network_httpcode($httpCode);
# } else {
#	 return WP_Error('herisson',__('php-curl library is missing.',HERISSON_TD));
#	}
#
#}


#function herisson_network_httpcode($code) {
# global $http_codes;
# if (array_key_exists($code,$http_codes)) {
#  $color = "green";
#  $error = 0;
#  if (intval($code)>=400) { $color = "red"; $error = 1; }
#  return array("code" => $code, "message" =>  $http_codes[$code], "color" => $color, "error" => $error);
# } else {
#  return array("code" => $code, "message" => __("HTTP code not found",HERISSON_TD), "color" => "red", "error" => 1);
# }
#}


#function herisson_network_reply_code($code) {
# global $http_codes;
# if (array_key_exists($code,$http_codes)) {
#	 header("HTTP/1.1 $code ".$http_codes[$code]);
#		exit;
#	}
#	echo __("Error, HTTP code $code does not exist.",HERISSON_TD);
#}


class HerissonNetwork {
 public $code = 0;
 public $message = "";
 public $content = "";
 public $error = 0;
 public $http_code = 0;
 public $http_message = "";
 public function __construct($code='',$message='',$content='') {
  $this->code = $code;
  $this->message = $message;
  $this->content = $content;
 }

	public function getCurl($url,$post=null) {
  if (function_exists('curl_init')) {
   $curl = curl_init();
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
   curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
 		if (sizeof($post)) {
 	  curl_setopt($curl, CURLOPT_POST,TRUE);
 	  curl_setopt($curl, CURLOPT_POSTFIELDS,$post);
 		}
			return $curl;
		} else {
 	 errors_add(__('php-curl library is missing.',HERISSON_TD));
			throw new Exception(__('php-curl library is missing.',HERISSON_TD));
		}
	}

	public function download($url,$post=array()) {
	 $curl = $this->getCurl($url,$post);
 	
  $content =  curl_exec($curl);
 	$this->http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  if($this->http_code >= 400) {
		 global $http_codes;
		 $this->error = 1;
			$this->http_message = $http_codes[$this->http_code];
 	 return new WP_Error('herisson',sprintf(__("The address %s returns a %s error (%s).",HERISSON_TD),$url,$this->http_code,$this->http_message),$this->http_code);
		}

 	$content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
	 $result = array("data"=>$content, "type" => $content_type);

		curl_close($curl);
		return $result;
	}

	public function check($url) {
	 $curl = $this->getCurl($url);
  $result =  curl_exec($curl);
 	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  return $this->http_data($httpCode);
	}

 public function http_data($code) {
  global $http_codes;
  if (array_key_exists($code,$http_codes)) {
   $color = "green";
   $error = 0;
   if (intval($code)>=400) { $color = "red"; $error = 1; }
   return array("code" => $code, "message" =>  $http_codes[$code], "color" => $color, "error" => $error);
  } else {
   return array("code" => $code, "message" => __("HTTP code not found",HERISSON_TD), "color" => "red", "error" => 1);
  }
	}

 public static function reply($code) {
  global $http_codes;
  if (array_key_exists($code,$http_codes)) {
 		if (!headers_sent()) {
  	 header("HTTP/1.1 $code ".$http_codes[$code]);
  		exit;
			} else { 
			 echo $http_codes[$code];
			}
 	} else {
  	echo __("Error, HTTP code $code does not exist.",HERISSON_TD);
		}
 }


}

