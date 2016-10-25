<?php
//获取数据流
$data = file_get_contents('php://input');
//$data = "http://192.168.1.109:9081/cgi-bin/emailopt?opt=modpwd&uin=2001005880&code=validate_A0FDE3931ED638C3\r\nToUserName=测试名字&ToEmail=xxxx@qq.com\r\nxxx";

//提前结束关闭连接
header('HTTP/1.1 200 OK');
fastcgi_finish_request();

require dirname(__FILE__).'/../config.php';
require dirname(__FILE__).'/../functions.php';

//切割解析
$arr = explode("\r\n", $data);
if ( count($arr) != 3 ) {
	exit('Illegal params');
}
$serv_url = $arr[0];
$arr_tmp = explode_str( $arr[1], '&', '=' );
$username = $arr_tmp['ToUserName'];
if (empty($username)) {
	$username = substr($arr_tmp['ToEmail'],0,strpos($arr_tmp['ToEmail'],'@'));
}
$email = $arr_tmp['ToEmail'];
$check_code = $arr[2];	
//检查接入是否合法
$key = 'xxxxx';
if ( md5($arr[1].$key) !== $check_code ) {
	exit('Illegal params');
}
//记录日志
$ip = get_client_ip();
$content = 'receive from '.$ip." >>>EOF\r\n".$data."\r\n".'EOF'." \r\n";
appLog( 'email_rec', $content );
$now = time();
$data = array(
	'cgi_data' => $serv_url,
	'username' => $username,
	'email' => $email,
	'create_time' => $now,
	'exp_time' => $now + 2400,
	'fail_time' => 0,//失败次数，用于后台任务，如果失败次数超过3次，则废弃并记录到失败日志里
);
$seri_data = serialize($data);
$memcache_obj = memcache_connect($mcq_host, $mcq_port);
memcache_set($memcache_obj, $mcq_email_que_name, $seri_data, 0, 0);	//入队
memcache_close($memcache_obj);