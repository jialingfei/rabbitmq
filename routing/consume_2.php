<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;

#消费者2

#创建连接
$connection = new AMQPStreamConnection("127.0.0.1",5672,"root","root","tiramisu_routing");
$channel = $connection->channel();

#直接获取队列消息
$queue_name = "tiramisu_queue_routing_2";
$channel->basic_consume($queue_name,"测试路由键",false,false,false,false,function($message){
    var_dump("队列2消息:".$message->body);
});

while ($channel->is_consuming()){
    $channel->wait();
}