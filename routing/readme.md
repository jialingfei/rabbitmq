### 路由模式
 图示  

 ![图示](../resource/img/rabbitmq-routing.png)

上面的orange、black、green就是不同的routing_key  
上面的使用场景为：在消费者C1中，只接受orange的数据；C2中接受black、green的数据


--------

运行结果
![rabbitmq-test-routing](../resource/img/rabbitmq-test-routing.png)