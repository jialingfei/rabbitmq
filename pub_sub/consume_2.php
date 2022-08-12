<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

#消费者2

#创建连接
$connection = new AMQPStreamConnection("127.0.0.1",5672,"root","root","tiramisu_pubsub");

$channel = $connection->channel();

#设置交换机
$exchange_name = "tiramisu_exchange_fanout";
$channel->exchange_declare($exchange_name,"fanout",false,false,false);

#设置队列2
$queue_name = "tiramisu_queue_fanout_2";
$channel->queue_declare($queue_name,false,false,false,false);

#绑定队列&交换机 不设置路由
$channel->queue_bind($queue_name,$exchange_name,"");

#获取队列消息
$channel->basic_consume($queue_name,"消费者2",false,false,false,false,function($message){
    var_dump("消费者2接受到的消息:".$message->body);
});

while ($channel->is_consuming()){
    $channel->wait();
}