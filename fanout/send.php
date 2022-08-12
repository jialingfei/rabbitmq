<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

#发布/订阅模式

#创建链接
$connection = new AMQPStreamConnection("127.0.0.1",5672,"root","root","tiramisu_pubsub");

#channel
$channel = $connection->channel();

#设置交换机 模式 广播模式 fanout
$exchange_name = "tiramisu_exchange_fanout";
$channel->exchange_declare($exchange_name,'fanout',false,false,false);


#创建消息
$data = "广播消息";
$msg = new AMQPMessage($data);

#发布消息  路由为空
$channel->basic_publish($msg,$exchange_name,"");

#关闭连接
$channel->close();
$connection->close();
