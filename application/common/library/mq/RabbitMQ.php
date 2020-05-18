<?php


namespace app\common\library\mq;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use think\Config;

class RabbitMQ
{

    private $host      = '127.0.0.1';
    private $post      = 5672;
    private $user      = 'admin';
    private $queueName = 'queue_name';
    private $password  = 'admin';

    private $channel;

    /**
     * @var RabbitMQ
     */
    private static $instance;

    private function __construct()
    {

        if (Config::get('mq.host')) $this->host = Config::get('mq.host');
        if (Config::get('mq.port')) $this->port = Config::get('mq.port');
        if (Config::get('mq.user')) $this->user = Config::get('mq.user');
        if (Config::get('mq.password')) $this->password = Config::get('mq.password');
        if (Config::get('mq.queue_name')) $this->queueName = Config::get('mq.queue_name');

        $this->channel = (new AMQPStreamConnection($this->host, $this->post, $this->user, $this->password))
            ->channel();

    }


    public static function instance()
    {
        if (empty(self::$instance)) self::$instance = new RabbitMQ;

        return self::$instance;
    }

    private function getChannel()
    {
        return $this->channel;
    }

    public function push($array)
    {
        if (empty($array)) return false;

        self::$instance->getChannel()->basic_publish(new AMQPMessage(json_encode($array)), '', $this->queueName);

        return true;
    }
}