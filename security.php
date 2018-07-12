<?php
	include_once 'Configs/config.php';

	if(PHOENIX_MODE == 'dev'){
	 error_reporting(E_ALL);
	 ini_set('display_errors','On');
	}
	
	spl_autoload_register(function($class_name) {
		$file = PHOENIX . '/' . $class_name . '.php';
		if(file_exists($file)) {
			require_once $file;
		}
	});
	$security = new SECURITY();
	
	$config = $security->returnConfig();
	if(isset($_REQUEST['token'])){
		$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt ($ch, CURLOPT_REFERER, $url);
		$header = array($config['tokenization']['client_name'].":".$_REQUEST['token'], 'security-action: redirect');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 

		$result = curl_exec($ch);
		curl_close($ch); 
		exit;
	}
?>
<html>
	<form name="security_form" id="security_form" method="post">
		<input type="hidden" id="token" name="token" value=""/>
	</form>
<script>
	if(typeof(localStorage.getItem("<?php echo $config['tokenization']['field']; ?>")) !== "undefined" && localStorage.getItem("<?php echo $config['tokenization']['field']; ?>") !== null ){
		document.getElementById("token").value = localStorage.getItem("<?php echo $config['tokenization']['field']; ?>");
		<?php if(!isset($_REQUEST['token']) || empty($_REQUEST['token'])){ ?>
			document.getElementById("security_form").submit();
		<?php } ?>
	}else{
		localStorage.setItem("<?php echo $config['tokenization']['field']; ?>", '0');
		document.getElementById("security_form").submit();
	}
</script>
</html>