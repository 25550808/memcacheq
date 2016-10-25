# memcacheq
memcacheq异步队列发邮件功能
首先使用nohub php task/sendEmailTaskProcess.php 运行该文件，使该文件在后台运行，异步调用memcacheq缓存队列，发送邮件
客户端请求地址且传参，传参格式也在此文件中    serv/apiMail.php
