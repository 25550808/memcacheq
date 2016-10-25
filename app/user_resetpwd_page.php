<!DOCTYPE HTML>
<html>
    <head>
        <meta content="width=device-width,user-scalable=no,initial-scale=1" name="viewport">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
	</head>
	<body>
<?php
	$php_key = 'xxxx';
	$_GET['t'] = !isset($_GET['t']) ? 0 : $_GET['t'];	
	if ( !isset($_GET['r']) || !isset( $_GET['k'] ) || !isset($_GET['t']) ) {
		echo 'Illegal params';
	} else {
		$rel = $_GET['r'];
		//特殊符号特殊处理
		$rel = str_replace(' ', '+', $rel);
		$token = $_GET['k'];
		$time = $_GET['t'];
		if ( isset($_COOKIE[$token]) ) {
			echo 'This password reset  has been used. Please request a new one.<br /><br />';
			
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
									<a href="msg://www.baidu.com.app/openImApp">Open </a>
						<?php
							} else { 
						?>
									<a href="http://www.baidu.com">Open </a>
						<?php
							}
						
		} elseif ( time() >= $time ) {
			echo 'This password reset link is no longer valid. Please request a new one.<br /><br />';
						
			$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
			$iphone = (strpos($agent, 'iphone')) ? true : false;
			$ipad = (strpos($agent, 'ipad')) ? true : false;
			$android = (strpos($agent, 'android')) ? true : false;
				
				if ($iphone || $ipad) {
			?>
						<a href="linkmsg://">Open </a>
			<?php
				} elseif($android) {
			?>
						<a href="linkmsg://http://www.baidu.com/openImApp">Open </a>
			<?php
				} else { 
			?>
						<a href="http://www.baidu.com">Open </a>
			<?php
				}
			
		} else {			
			if ( md5($rel.$php_key.$time) != $token ) {
				echo 'Illegal params';
			} else {
?>
		<div>
			Passwords must contain 6-20 characters, including letters, numbers, and symbols.
		</div>
		<br />
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
		
			}
		}
	}
?>
	</body>
</html>