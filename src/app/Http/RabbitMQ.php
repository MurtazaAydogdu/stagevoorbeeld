<?php

namespace App\Http;

require_once __DIR__.'./../../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ {
    
    public function send($message) {
        $connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USERNAME'), env('RABBITMQ_PASSWORD'));

        $channel = $connection->channel();
        $channel->queue_declare(env('RABBITMQ_QUEUE'), false, true, false, false);

        $msg = new AMQPMessage($message);

        $channel->basic_publish($msg, '', env('RABBITMQ_QUEUE'));
        
        $channel->close();
        $connection->close();
    }
}
