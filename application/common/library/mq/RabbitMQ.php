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

    private $url;
    private $urlParam = [];

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url): void
    {
        if (empty($url)) $url = base_url() . 'notice.php';
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getUrlParam(): array
    {
        return $this->urlParam;
    }

    /**
     * @param array $urlParam
     */
    public function setUrlParam(array $urlParam): void
    {
        $this->urlParam = $urlParam;
    }

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

        $this->setUrlParam([
            's' => '/task/notify/mq',
        ]);

        $this->setUrl(Config::get('mq.url'));

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

        if (isset($array['urlParam'])) {
            $this->setUrlParam(array_merge($array['urlParam'], $this->getUrlParam()));
        }

        $array['url'] = $this->fullUrl();
        self::$instance->getChannel()
            ->basic_publish(new AMQPMessage(json_encode($array)), '', $this->queueName);

        return true;
    }

    public function fullUrl()
    {
        return str_replace('https', 'http', $this->getUrl()) . http_build_query($this->getUrlParam());
    }
}