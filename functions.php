<?php
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
	$type       =  $type ? 1 : 0;
	static $ip  =   NULL;
	if ($ip !== NULL) return $ip[$type];
	if($adv){
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos    =   array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip     =   $_SERVER['HTTP_CLIENT_IP'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip     =   $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u",ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

//日志记录
function appLog( $pre_log_name, $log_data )
{
	global $log_path;
	$date = date('Y_m_d');
	$file = $log_path . $pre_log_name . '_' . $date . '.log';
	$log = "[".date('Y-m-d H:i:s')."] " . $log_data . "\r\n";
	file_put_contents( $file, $log, FILE_APPEND );
}

/**
 * curl
 */
function curl_request($url, array $options = NULL)
{
	$ch = curl_init($url);

	$defaults = array(
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT => 24,
	);

	// Connection options override defaults if given
	curl_setopt_array($ch, (array) $options + $defaults);
	
	// Create a response object
	$object = new stdClass;

	// Get additional request info
	$object->response = curl_exec($ch);
	$object->error_code = curl_errno($ch);
	$object->error = curl_error($ch);
	$object->info = curl_getinfo($ch);
	
	curl_close($ch);

	return $object;
}
function encode($string, $to = 'UTF-8', $from = 'UTF-8')
{
	// ASCII is already valid UTF-8
	if($to == 'UTF-8' AND is_ascii($string))
	{
		return $string;
	}

	// Convert the string
	return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
}

// 获取http头信息
if (!function_exists('getallheaders'))   
{  
    function getallheaders()   
    {  
       foreach ($_SERVER as $name => $value)   
       {  
           if (substr($name, 0, 5) == 'HTTP_')   
           {  
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;  
           }  
       }  
       return $headers;  
    }  
}


