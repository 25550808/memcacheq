<?php
error_reporting(E_ALL & ~E_NOTICE);
set_time_limit(0);
date_default_timezone_set('PRC');
require dirname(__FILE__).'/../config.php';
require dirname(__FILE__).'/../functions.php';
require dirname(__FILE__).'/phpMailer/class.phpmailer.php';
require dirname(__FILE__).'/phpMailer/class.smtp.php';

$support_url = $resetpwd_path;
$support_key = 'xxxx';

//加密密钥
$cgi_key = '*****';
$php_key = '******';

$memcache_obj = memcache_connect($mcq_host, $mcq_port);

while (true)
{
	$item = memcache_get($memcache_obj, $mcq_email_que_name);
	
	//如果队列中没有数据，等待3秒。
	if ( $item == FALSE ) {
		sleep(3);
		continue;
	} else {
		appLog('email_task_fetch', $item);
		$errs = array();
		$curls = array();
		$data = unserialize($item);
		//失败次数超过3次，放到失败日志里
		if ( $data['fail_time'] >= 3 ) {
			appLog( 'email_fail', $item );
			continue;
		}
		//如果过期时间点已经超过当前时间
		$now = time();
		if ( $data['exp_time'] < $now ) {
			continue;
		}
		$serv_url = $data['cgi_data'];
		$username = $data['username'];
		$email    = $data['email'];
		$c_time   = $data['create_time'];
		$e_time   = $data['exp_time'];

		//通用邮件接口
		$mail = new PHPMailer();
		$mail->IsSMTP(); // call class to process SMTP
		$mail->SMTPAuth = true;     // 开启 SMTP验证
		$mail->SMTPSecure = "tls";
		$mail->CharSet = 'UTF-8';
		$mail->Host =  "smtp.163.com";  // 指定邮件服务器
		$mail->Port = 25;    //指定邮件服务器端口
		$mail->Username = 'xxx@163.com';
		$mail->Password = 'xxxx';
		$mail->From = 'xxxx@163.com'; //指定发送邮件地址
		$mail->FromName = 'xxxx'; //为发送邮件地址命名
		$mail->AddAddress($email); //发送邮件地址
		$mail->IsHTML(true); // 设置Email格式为HTML
		$mail->Subject    = 'xxxx Password Reset';  //设置标题
		
		//定义邮件内容
		$encode_cgi = encrypt($serv_url, $cgi_key);
		//$encode_cgi = uc_authcode($serv_url, '', $cgi_key);
		$token = md5($encode_cgi.$php_key.$e_time);
		
		$url = $support_url . 'r='.urlencode($encode_cgi).'&k='.urlencode($token).'&t='.urlencode($e_time);
		//随机的地址链接
		$htmlurl = 'http://www.baidu.com/link/password/reset/'.generate_password();
		$email_body = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
<title>xxx Password Reset</title>
</head>
<body>
<p style="font-weight: bold;">Hi</p>
<p style="font-weight: bold;">Trouble with your password? Click the following link or copy and paste it in your brower.</p>
<p style="font-weight: bold;"><a href="'.$url.'" target="_blank">'.$url.'</a></p>
<p style="font-weight: bold;">If you have any questions or concerns, please contact us at <a href="mailto:support@linksocialapp.com" target="_blank">support@xxx.com</a>.</p>
<p style="font-weight: bold;">test</p>
<p style="font-weight: bold;"><a href="http://www.baidu.com" target="_blank">http://www.baidu.com</a></p>
</body>
</html>
';
		$mail->Body = $email_body;// 邮件正文
		//第三方接口
		$curls[] = "\r\n" . 'username: '.$username;
		$curls[] = 'email: '.$email;
		$curls[] = 'url: '.$url;
		if($mail->Send()) {
			$content = join("\r\n", $curls);
			appLog( 'email_task_curl', $content );
		} else {
			//如果失败，则把失败计数+1，重新尝试发送。
			$data['fail_time']++;
			$seri_data = serialize($data);
			memcache_set($memcache_obj, 'emailpwd', $seri_data, 0, 0);
			//收集错误
			$errs[] = print_r($ret,true);
			$content = join("\r\n", $errs);
			appLog( 'email_task_err', $content );
		}
	}
}
