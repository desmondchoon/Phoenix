<?php
	function isHttps()
	{
		if (array_key_exists("HTTPS", $_SERVER) && 'on' === $_SERVER["HTTPS"]) {
			return true;
		}
		if (array_key_exists("SERVER_PORT", $_SERVER) && 443 === (int)$_SERVER["SERVER_PORT"]) {
			return true;
		}
		if (array_key_exists("HTTP_X_FORWARDED_SSL", $_SERVER) && 'on' === $_SERVER["HTTP_X_FORWARDED_SSL"]) {
			return true;
		}
		if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && 'https' === $_SERVER["HTTP_X_FORWARDED_PROTO"]) {
			return true;
		}
		return false;
	}
	
	include_once 'Configs/config.php';
	if(isHttps()){
		$https = 'https://';
	}else{
		$https = 'http://';
	}
	
?>

<html>
You may create or redirect to your preferred login path
</br>
Login <a href="<?php echo $https.$_SERVER['HTTP_HOST'].ROOT_PATH ?>your_login_path_here">Here</a>
</html>