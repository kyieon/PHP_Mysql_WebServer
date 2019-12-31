<?php
	$userId = "D";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat D</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

	<style type="text/css">
	* {margin:0;padding:0;box-sizing:border-box;font-family:arial,sans-serif;resize:none;}
	html,body {width:100%;height:100%;}
	#wrapper {position:relative;margin:auto;max-width:1000px;height:100%;}
	#chat_output {position:absolute;top:0;left:0;padding:20px;margin-top:70px;width:100%;height:calc(100% - 100px);}
	#chat_input {bottom:0;left:0;width:20%;height:30px;border:1px solid #ccc;}
	#button {bottom:0;left:0;width:20%;height:30px;border:1px solid #ccc;}
	#button1 {bottom:0;left:0;margin-left:10px;margin-top:5px;width:10%;height:30px;border:1px solid #ccc;}
	</style>
</head>
<body>
	<div id="wrapper">
	
		<button  id = "button" name="button" value='1' onclick="button_click();">좋아요</button><br>
		<input type="text" id="chat_input"/><button  id = "button1" name="button" value='1' onclick="button_click1();">입금</button>
		<div id="chat_output"></div>
		<script type="text/javascript">
			var websocket_server = new WebSocket("ws://52.231.25.115:8080/");
			jQuery(function($){
				// Websocket
			
				websocket_server.onopen = function(e) {
					console.log("Connection established!");

					websocket_server.send(
						JSON.stringify({
							'type':'init',
							'user_id': '<?php echo $userId; ?>'
						})
					);
				};
				websocket_server.onerror = function(e) {
					// Errorhandling
				}
				websocket_server.onmessage = function(e)
				{
					var json = JSON.parse(e.data);
					switch(json.type) {
						case 'chat':
							$('#chat_output').append(json.msg);
							break;
					}
				}
			
			});

			function button_click(){
				websocket_server.send(
							JSON.stringify({
								'type':'GOOD',
								'user_id': '<?php echo $userId; ?>',
								'target_id':'B',
								'chat_msg':'1'
							})
						);
			}

			function button_click1(){
					var chat_msg = $('#chat_input').val();
				websocket_server.send(
							JSON.stringify({
								'type':'DEPOSIT',
								'user_id': '<?php echo $userId; ?>',
								'target_id':'C',
								'chat_msg':chat_msg
							})
						);
			}

		</script>
	</div>
</body>
</html>