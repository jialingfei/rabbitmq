<?php
include_once "../vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

#生产者

#创建连接
$connection = new AMQPStreamConnection("127.0.0.1",5672,"root","root","tiramisu_routing");
$channel = $connection->channel();

#创建交换机 需要定向 direct
$exchange_name = "tiramisu_exchange_routing";
$channel->exchange_declare($exchange_name,"direct",false,false,false,false);

#创建两个队列
$queue_name_1 = "tiramisu_queue_routing_1";
$queue_name_2 = "tiramisu_queue_routing_2";
$channel->queue_declare($queue_name_1,false,false,false,false,false);
$channel->queue_declare($queue_name_2,false,false,false,false,false);

#创建三个路由键
$routing_key_1 = "tiramisu_routing_key_1";
$routing_key_2 = "tiramisu_routing_key_2";
$routing_key_3 = "tiramisu_routing_key_3";
#将路由键1绑定队列1
$channel->queue_bind($queue_name_1,$exchange_name,$routing_key_1);
#将路由键2/3绑定队列2
$channel->queue_bind($queue_name_2,$exchange_name,$routing_key_2);
$channel->queue_bind($queue_name_2,$exchange_name,$routing_key_3);


#生产消息
for ($i = 0;$i < 10;$i++){
    if ($i%3==0){
        $data = "路由键1的消息:当前为第".$i."条消息";
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg,$exchange_name,$routing_key_1);
    }
    if ($i%3==1){
        $data = "路由键2的消息:当前为第".$i."条消息";
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg,$exchange_name,$routing_key_2);
    }
    if ($i%3==2){
        $data = "路由键3的消息:当前为第".$i."条消息";
        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg,$exchange_name,$routing_key_3);
    }
}


$channel->close();
$connection->close();