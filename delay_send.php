<?php
include_once "./vendor/autoload.php";

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;


#延迟推送


#链接
$connection = new AMQPStreamConnection("127.0.0.1","5672","root","root","/");
$channel = $connection->channel();
#发布确认模式
$channel->confirm_select();

#推送成功
$channel->set_ack_handler(
    function (AMQPMessage $message){
        var_dump($message);
    }
);

#推送失败
$channel->set_nack_handler(
    function (AMQPMessage $message){
        var_dump($message);
    }
);


#声明两个路由
$delayExchangeRoute = "tiramisu_delay_exchange_route";#延迟交换机绑定路由
$workExchangeRoute = "tiramisu_work_exchange_route";#消费交换机绑定路由

#给delay_exchange 交换机推送延迟消息，时间到后转发到work_exchange的交换机处理
#声明两个交换机
$delayExchange = "tiramisu_delay_exchange";
$workExchange = "tiramisu_work_exchange";
$channel->exchange_declare($delayExchange,AMQP_EX_TYPE_DIRECT,false,false,false);
$channel->exchange_declare($workExchange,AMQP_EX_TYPE_DIRECT,false,false,false);

#设置交换机关系
$table = new AMQPTable();
$table->set("x-dead-letter-exchange",$workExchange);# 代表过期后由哪个exchange处理
$table->set('x-dead-letter-routing-key',$delayExchangeRoute);# 代表过期后根据什么路由策略转发到上main的exchange中

#绑定需要延迟的队列和交换机
$delayQueue = "tiramisu_delay_queue";
$channel->queue_declare($delayQueue,false,true,false,false,false,$table);
$channel->queue_bind($delayQueue,$delayExchange,$delayExchangeRoute);

#绑定真正消费的队列和交换机
$workQueue = "tiramisu_work_queue";
$channel->queue_declare($workQueue,false,true,false,false,false,$table);
$channel->queue_bind($workQueue,$workExchange,$workExchangeRoute);

$data = "tiramisu测试延迟推送";
$msg = new AMQPMessage($data,[
    "expiration" => 50000,#毫秒
    "delivery_mode" => AMQPMessage::DELIVERY_MODE_PERSISTENT
]);

$channel->basic_publish($msg,$delayExchange,$delayExchangeRoute);
$channel->confirm_select_ok();
$channel->close();
$connection->close();