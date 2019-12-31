<?php
namespace APP;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require_once '../vendor/autoload.php';

// class Chat implements MessageComponentInterface {
//     protected $clients;

//     public function __construct() {
//         $this->clients = new \SplObjectStorage;
//     }

//     public function onOpen(ConnectionInterface $conn) {
//         // Store the new connection to send messages to later
//         $this->clients->attach($conn);

//     }

//     public function onMessage(ConnectionInterface $from, $msg) {
//         
//         

//         foreach ($this->clients as $client) {
//             //if ($from !== $client) {
//                 // The sender is not the receiver, send to each client connected
//                 $client->send($msg);
//             //}
//         }
//     }

//     public function onClose(ConnectionInterface $conn) {
//         // The connection is closed, remove it, as we can no longer send it messages
//         $this->clients->detach($conn);

//         
//     }

//     public function onError(ConnectionInterface $conn, \Exception $e) {
//         

//         $conn->close();
//     }
// }


class Chat implements MessageComponentInterface {
	protected $clients;
	protected $users;

	public function __construct() {
		$this->clients = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn) {
		$this->clients->attach($conn);
        // $this->users[$conn->resourceId] = $conn;
        
        echo "New connection! ({$conn->resourceId})\n";
	}

	public function onClose(ConnectionInterface $conn) {
		$this->clients->detach($conn);
        // unset($this->users[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
	}

	public function onMessage(ConnectionInterface $from,  $data) {
        $numRecv = count($this->clients) - 1;
        $from_id = $from->resourceId;
        echo sprintf('Connection %d sending message "%s" to %d other connection' . "\n"
             , $from_id, $data, $numRecv);

		$data = json_decode($data);
		$type = $data->type;
		switch ($type) {
			case 'chat':
				$user_id = $data->user_id;
				$chat_msg = $data->chat_msg;
				$response_from = "<span style='color:#999'><b>".$user_id.":</b> ".$chat_msg."</span><br><br>";
				$response_to = "<b>".$user_id."</b>: ".$chat_msg."<br><br>";
				// Output
				$from->send(json_encode(array("type"=>$type,"msg"=>$response_from)));
				foreach($this->clients as $client)
				{
					if($from!=$client)
					{
						$client->send(json_encode(array("type"=>$type,"msg"=>$response_to)));
					}
				}
				break;
		}
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();
	}
}

echo "Server Start ... \n";

$ws = new WsServer(new Chat());
$server = IoServer::factory(
	new HttpServer($ws),
	8080
);
$server->run();
?>