<?php	
	$php_key = 'xxxx';
	$_POST['t'] = !isset($_POST['t']) ? 0 : $_POST['t'];	
	if ( !isset($_POST['r']) || !isset( $_POST['k'] ) || !isset( $_POST['t'] ) ) {
		die('Illegal params');
	}
	$rel = $_POST['r'];
	//特殊符号特殊处理
	$rel = str_replace(' ', '+', $rel);
	$token = $_POST['k'];
	$time = $_POST['t'];	
	if ( md5($rel.$php_key.$time) != $token ) {
		die('Illegal params');
	}
	
	$status = 0;//1.错误，可以继续提交 2.错误不可继续提交 3.成功
	$info = '';
	
	if ( isset($_COOKIE[$token]) ) {
		$status = 2;
		$info = 'This password reset link has been used. Please request a new one.';
	} elseif ( time() > $time ) {
		$status = 2;
		$info = 'This password reset link is no longer valid. Please request a new one.';
	} elseif ( !isset($_POST['pwd']) || !isset($_POST['repwd']) ) {
		$status = 1;
		$info = 'Passwords must contain 6-20 characters, including letters, numbers, and symbols.';
	} else {
		require dirname(__FILE__).'/../config.php';
		require dirname(__FILE__).'/../functions.php';
		
		$newpwd = trim($_POST['pwd']);
		$repwd = trim($_POST['repwd']);
		if ( empty($newpwd) || empty($repwd) ) {
			$status = 1;
			$info = 'Passwords must contain 6-20 characters, including letters, numbers, and symbols.';
		} elseif ( $newpwd != $repwd ) {
			$status = 1;
			$info = 'Not the same password twice.';
		} elseif ( strpos($newpwd, " ") !== false ) {
			$status = 1;
			$info = 'Passwords must contain 6-20 characters, including letters, numbers, and symbols.';
		} elseif ( !is_ascii($newpwd) ) {
			$status = 1;
			$info = 'Passwords must contain 6-20 characters, including letters, numbers, and symbols.';
		} elseif ( strlen($newpwd) < 6 || strlen($newpwd) > 20 ) {
			$status = 1;
			$info = 'Passwords must contain 6-20 characters, including letters, numbers, and symbols.';
		} else {
			//合法后开始处理
			$memcache_obj = memcache_connect($mcd_host, $mcd_port);
			//如果mcd挂了，不影响业务
			if ( false === $memcache_obj ) {
				//记录日志
				$used_key = md5($rel);
				$ip = get_client_ip();
				$content = 'Action from '.$ip." \r\n";
				//解密
				$cgi_key = 'xxx';
				$cgi_url = decrypt($rel, $cgi_key);
				$cgi_url .= '&pwd='.md5($newpwd);
				
				$content .= 'call server cgi: '. $cgi_url . "\r\n";
				$ret = curl_request($cgi_url);
				if ( $ret->response == '<pre>200 OK</pre>' ) {
					$status = 3;
					$info = 'Password reset!';
					$content .= 'cache record:'.$used_key . ' ';
					//记录缓存，防止重复提交对服务端造成压力
					memcache_set($memcache_obj, $token, 1);
					//cookie记录
					setcookie($token,1);
				} else {
					$status = 2;
					$info = 'This password reset link is no longer valid. Please request a new one .';
				}
				$content .= $info . "\r\n";
				appLog('email_resetpwd',$content);
			} else {
				$val = memcache_get($memcache_obj, $token);
				if ( $val !== false ) {
					$status = 2;
					$info = 'This password reset link is no longer valid, Please request a new one .';
				} else {
					//记录日志
					$used_key = md5($rel);
					$ip = get_client_ip();
					$content = 'Action from '.$ip." \r\n";
					//解密
					$cgi_key = 'xxx';
					$cgi_url = decrypt($rel, $cgi_key);
					$cgi_url .= '&pwd='.md5($newpwd);
					
					$content .= 'call server cgi: '. $cgi_url . "\r\n";
					$ret = curl_request($cgi_url);
					if ( $ret->response == '<pre>200 OK</pre>' ) {
						$status = 3;
						$info = 'Password reset!';
						$content .= 'cache record:'.$used_key . ' ';
						//记录缓存，防止重复提交对服务端造成压力
						memcache_set($memcache_obj, $token, 1);
						//cookie记录
						setcookie($token,1);
					} else {
						$status = 2;
						$info = 'This password reset link is no longer valid. Please request a new one .';
					}
					$content .= $info . "\r\n";
					appLog('email_resetpwd',$content);
				}
			}
		}
	}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta content="width=device-width,user-scalable=no,initial-scale=1" name="viewport">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
	</head>
	<body>
		<div>
			<?php echo $info;?>
		</div>
		<br />
<?php
	if ($status == 1) {
?>
		<form action="user_resetpwd.php" method="post">
			<input type="hidden" name="r" value="<?php echo $rel;?>" />
			<input type="hidden" name="k" value="<?php echo $token;?>"/>
			<input type="hidden" name="t" value="<?php echo $time;?>"/>
			New password:<br />
			<input type="password" name="pwd"  style="height:24px;width:200px;" /><br /><br />
			Confirm password:<br /><input type="password" name="repwd"  style="height:24px;width:200px;" /><br /><br />
			<input type="submit" style="width: 100px;height:30px;" value="Submit" />
		</form>
<?php
	} else { 
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$iphone = (strpos($agent, 'iphone')) ? true : false;
		$ipad = (strpos($agent, 'ipad')) ? true : false;
		$android = (strpos($agent, 'android')) ? true : false;
		if ($iphone || $ipad) {
?>
			<a href="msg://">Open </a>
<?php
		} elseif($android) {
?>
			<a href="msg://www.baidu.com/openImApp">Open </a>
<?php
		} else { 
?>
			<a href="http://www.baidu.com.com">Open </a>
<?php 
		}
	}
?>
	</body>
</html>
