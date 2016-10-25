<?php
//mcq配置
$mcq_host = '127.0.0.1';
$mcq_port = '22201';
$mcq_email_que_name = 'emailpwd';
$mcq_verify_que_name = 'verify';

//mcd配置
$mcd_host = '127.0.0.1';
$mcd_port = '11211';

//服务端代理配置,appweb服务器上用的是本地8080指向upstream路由
//$server_proxy = '127.0.0.1';//127.0.0.1
//$server_port  = '8080';//8080
//qa
//$server_proxy = '10.0.2.196';//127.0.0.1
//$server_port  = '9081';//8080

//邮件重置密码重置路径
//外服
$resetpwd_path = 'http://www.baidu.com/user_resetpwd_page.php?';
$verify_path = 'http://www.baidu.com/user_resetpwd_page.php?';
//QA
//$resetpwd_path = 'http://10.0.2.81:9902/user_resetpwd_page.php?';
//$verify_path = 'http://10.0.2.81:9902/verify_mail_page.php?';

//日志路径
$log_path = '/var/log/appWeb/';

//监听事件ID
$how_to_get_charm_event_id = '10012006';

//群信息url
$group_url = 'http://10.10.17.105:9090';