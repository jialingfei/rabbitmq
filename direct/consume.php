<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

#消费者

#创建连接
$connection = new AMQPStreamConnection("127.0.0.1","5672","root","root","/");

#创建通道
$channel = $connection->channel();

#声明交换机
$exchange_name = "tiramisu_exchange";
$channel->exchange_declare($exchange_name,AMQP_EX_TYPE_DIRECT,false,true,false);

#声明队列
$queue_name = "tiramisu_queue";
$channel->queue_declare($queue_name,false,true,false,false);

#绑定队列和交换机 路由键
$route_key = "tiramisu_exchange_queue";
$channel->queue_bind($queue_name,$exchange_name,$route_key);

#获取队列消息
$channel->basic_consume($queue_name,"test",false,true,false,false,function($message){
    var_dump($message->body);
});

while($channel->is_consuming()){
    $channel->wait();
}