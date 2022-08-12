<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

#消费者1

#建立连接
$connection = new AMQPStreamConnection("127.0.0.1",5672,"root","root","tiramisu_pubsub");

$channel = $connection->channel();

#设置交换机
$exchange_name = "tiramisu_exchange_fanout";
$channel->exchange_declare($exchange_name,'fanout',false,false,false);

#创建队列
$queue_name = "tiramisu_queue_fanout_1";
$channel->queue_declare($queue_name,false,false,false,false);

#将队列和交换机绑定 不设置路由
$channel->queue_bind($queue_name,$exchange_name,"");

#获取队列消息
$channel->basic_consume($queue_name,'消费者1',false,false,false,false,function($message){
    var_dump("消费者2接受到的消息:".$message->body);
});

while ($channel->is_consuming()){
    $channel->wait();
}