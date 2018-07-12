
<?php

/**
 * Description of api
 *
 * @author DESMOND
 */

include_once 'Configs/config.php';

if(PHOENIX_MODE == 'dev'){
 error_reporting(E_ALL);
 ini_set('display_errors','On');
}

//echo password_hash('123', PASSWORD_DEFAULT)."\n";

spl_autoload_register(function($class_name) {
    $file = PHOENIX . '/' . $class_name . '.php';
    if(file_exists($file)) {
        require_once $file;
    }
});
// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}



try {
    if(isset($_REQUEST['url'])){
		include 'security.php';
	}else{
		$APP = new APP($_REQUEST, $_SERVER);
		$APP->callAPP();
	}
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

?>

<html>
	<script>
		window.onerror = function (message, url, lineNo){
    	alert('Error: ' + message + '\n' + 'Line Number: ' + lineNo);
    	return true;
	}
	</script>
	
</html>