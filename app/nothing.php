<?php
$chk_key = 'xxxx';
$uin = $_POST['uin'];
$session_id =$_POST['session_id'];
$token1 = $_POST['token'];
$token2 = md5(md5($uin.$session_id.$chk_key));
if ( $token1 != $token2 ) {
	var_dump($_POST);
	exit('Err code 999');
}
//
//print_r( $_POST );
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<table border="1">
		<?php foreach($_POST as $key => $value){?>
		<tr>
			<td><?php echo $key;?></td><td><?php echo htmlspecialchars($value);?></td>
		</tr>
		<?php }?>
	</table>
  	<script type="text/javascript">
		
	</script>  
</body>
</html>
