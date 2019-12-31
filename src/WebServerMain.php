<?php
namespace APP;

use Exception;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use APP\MessageHandler;

require_once '../vendor/autoload.php';

echo "Server Start ... \n";

//웹서버 구동 8080 포트로 시작
$ws = new WsServer(new MessageHandler());
$server = IoServer::factory(
	new HttpServer($ws),
	8080
);
$server->run();
?>