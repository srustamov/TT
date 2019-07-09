<?php namespace Socket;

use System\Libraries\Database\Model;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;


require dirname(dirname(__DIR__)).'/vendor/autoload.php';


class Message implements MessageComponentInterface
{
    protected $clients;

    private $users;

    //private $subscriptions;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

       // $this->subscriptions = [];

        $this->users   = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        $this->users[$conn->resourceId] = $conn->resourceId;

        //echo "New connection! ({$conn->resourceId})\n";


    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        // $data = json_decode($msg);

        // switch ($data->command) {
        //     case "subscribe":
        //         $this->subscriptions[$conn->resourceId] = $data->channel;
        //         break;
        //     case "message":
        //         if (isset($this->subscriptions[$conn->resourceId])) {
        //             $target = $this->subscriptions[$conn->resourceId];

        //             foreach ($this->subscriptions as $id => $channel) {
        //                 if ($channel == $target && $id != $conn->resourceId) {
        //                     $this->users[$id]->send($data->message);
        //                 }
        //             }
        //         }
        // }

        $numRecv = count($this->clients) - 1;

        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        unset($this->users[$conn->resourceId]);

        //unset($this->subscriptions[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}



