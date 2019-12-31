<?php
	/* UserId 
	    - 추후 로그인 후 변수 할당 필요
	*/
	$userId = "C";
?>

<!DOCTYPE html>
<html>
<head>
	<title>Chat C</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

	<style type="text/css">
		* {margin:0;padding:0;box-sizing:border-box;font-family:arial,sans-serif;resize:none;}
		html,body {width:100%;height:100%;}
		#wrapper {position:relative;margin:auto;max-width:1000px;height:100%;}
		#chat_output {position:absolute;top:0;left:0;padding:20px;width:100%;height:calc(100% - 100px);}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="chat_output"></div>
	</div>

	<script type="text/javascript">
		jQuery(function($){
			
			// WebSocket 접속
			var websocket_server = new WebSocket("ws://52.231.25.115:8080/");
			
			//WebSocket이 열렸을 때
			websocket_server.onopen = function(e) {
				console.log("Connection established!");

				websocket_server.send(
					JSON.stringify({
						'type':'init',
						'user_id': '<?php echo $userId; ?>'
					})
				);
			};
			
			//WebSocket Message처리 부분
			websocket_server.onmessage = function(e) {
				var json = JSON.parse(e.data);
				switch(json.type) {
					case 'chat':
						if($('#chat_output').find('span').length >= 3) {
							$($('#chat_output').find('span')[0]).remove()
						}
						$('#chat_output').append('<span>' + json.msg + '</span>');

						break;
				}
			}

			//Error처리
			websocket_server.onerror = function(e) {
			
			}			
		});
	</script>
</body>
</html>