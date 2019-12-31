<?php
namespace APP;

use Exception;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once '../vendor/autoload.php';

class MessageHandler implements MessageComponentInterface {
	protected $users;
	private $sep = '_';
	
	
	//key 생성
	private function generateKey($user_id, $resource_id) {
		return $user_id . $this->sep . $resource_id;
	}

	private function getUserId($key) {
		return explode($this->sep, $key)[0];
	}
	

	//서버 Open시 호출 user를 저장할 객체 생성
	public function __construct() {
		$this->users = array();
	}
	
	//client 접속시 마다 호출 - 여기서 conn관리 로직 처리해도됨
	public function onOpen(ConnectionInterface $conn) {
		echo "New Open Connection! \n";
	}
	

	//client 접속 해제시 호출 users 관리 객체에서 삭제 처리 
	public function onClose(ConnectionInterface $conn) {		
		$findKeys = array_keys($this->users, $conn);
		if(count($findKeys) <= 0) {
			throw new Exception("connection not found.");
		}
		// del connection info
		$key = $findKeys[0];
		unset($this->users[$key]);
		
		$user_id = $this->getUserId($key);
		echo "Connection {$user_id} has disconnected\n";
	}
	

	// 이벤트 처리 
	public function onMessage(ConnectionInterface $from,  $data) {
		echo sprintf('received message "%s"' . "\n", $data);
		
		$data = json_decode($data);
		$user_id = $data->user_id;

		$msg = '';

		switch ($data->type) {
			case 'init':
				// add connection info
				$resource_id = $from->resourceId;
				$this->users[$this->generateKey($user_id, $resource_id)] = $from;
				break;
			case 'chat':
				// send response
				$response_msg = "<span style='color:#999'><b>Send Completed</span><br><br>";
				$from->send(json_encode(array("type"=>"chat","msg"=>$response_msg)));
				
				$msg = "<b>" . $data->user_id . "</b>: ". $data->chat_msg . "<br><br>";
				break;
			case 'GOOD':
				
				// send response
				$response_msg = "<span style='color:#999'><b>Send Completed</span><br><br>";
				$from->send(json_encode(array("type"=>"chat","msg"=>$response_msg)));
				
				
				//DB 접속 
				$conn=mysqli_connect('52.231.25.115', 'toz', 'toz123','testdb'); 
				if(mysqli_connect_errno($conn)){
					echo "DB FAIL" . "\n";
				}else{
					
					
					//Update 					
					if ( !mysqli_query ($conn,"UPDATE GOOD SET GOOD_COUNT=GOOD_COUNT+1  WHERE SEQ=1") )
					{
						echo("쿼리: " . mysqli_error($conn));
					}
					$msg = "<b>" . $data->user_id . "</b>님 께서 좋아요 보냈습니다.<br><br>";
					
					mysqli_close($conn);
					
					echo "DB SUCCESS" . "\n";
					
				}
				break;
			case 'DEPOSIT':
				// send response
				$response_msg = "<span style='color:#999'><b>Send Completed</span><br><br>";
				$from->send(json_encode(array("type"=>"chat","msg"=>$response_msg)));
				
				
				
				//DB 접속 
				$conn=mysqli_connect('52.231.25.115', 'toz', 'toz123','testdb'); 
				if(mysqli_connect_errno($conn)){
					echo "DB FAIL" . "\n";
				}else{
					
					$deposit_sum = $data->chat_msg;	
					
					//insert 					
					if ( !mysqli_query ($conn,"INSERT INTO DEPOSIT(DEPOSIT_SUM , DEPOSIT_USER , DEPOSIT_TYPE) VALUES('$deposit_sum','$user_id','1')") )
					{
						echo("쿼리: " . mysqli_error($conn));
					}
					$msg = "<b>" . $data->user_id . "</b> 님께서  ". $data->chat_msg . "원을 입금 하였습니다.<br><br>";
					
					mysqli_close($conn);
					
					echo "DB SUCCESS" . "\n";
					
				}
				break;
			default:
				break;
		}
	
	
		// client에 메세지 전송
		if(array_key_exists('target_id', $data)) {
			$target_id = $data->target_id;
			foreach ($this->getUsers($target_id) as $conn) {
				$conn->send(json_encode(array("type"=>"chat","msg"=>$msg)));
			}
		}
	}
	

	//접속 되어 있는 USER 중에서 보낼 USER 선택해서 전송
	private function getUsers($target_id) {
		$tmp = array();
		foreach ($this->users as $key => $conn) {
			$user_id = $this->getUserId($key);
			if($user_id == $target_id) {
				array_push($tmp, $conn);
			}
		}
		return $tmp;
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
		$conn->close();
	}
}
?>