<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

#生产者
#Connection: publisher/consumer和broker之间的TCP链接
#Channnel: 如果每一次访问RabbitMQ都建立一个Connection,在消息量大的时候建立TCP Connection的开销很大，效率也很低；Channel是在connection内部建立的逻辑连接Channel作为轻量级的Connection极大减少了操作系统建立TCP connection的开销



#建立connection
$connection = new AMQPStreamConnection("127.0.0.1","5672","root","root","/");

#channel
$channel = $connection->channel();

#交换机类型
$args = new AMQPTable(['x-delayed-type' => 'direct']);
#交换机名称
$exchange_name = "tiramisu_exchange_delayed";
$channel->exchange_declare($exchange_name,"x-delayed-message",false,true,false,false,false,$args);

#队列名称
$queue_name = "tiramisu_queue_delayed";
$channel->queue_declare($queue_name,false,true,false,false);

#绑定队列和交换机 路由
$route_key = "tiramisu_exchange_queue_delayed";
$channel->queue_bind($queue_name,$exchange_name,$route_key);


#传入数据
$date = date("H:i:s",time());
$data = 'tiramisu测试延迟数据,发送时间:'.$date;

#创建消息 消息持久化
$msg = new AMQPMessage($data,['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
#延时设置
$headers = new AMQPTable(['x-delay' => 5000]);
$msg->set('application_headers', $headers);

#发布消息
$channel->basic_publish($msg,$exchange_name,$route_key);

#关闭链接
$channel->close();
$connection->close();