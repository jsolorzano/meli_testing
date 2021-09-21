<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

require_once "Meli.php";

$app_id = '';
$secret_api = '';
$tokens = '';
$expires_in = 0;
$categoria_referencia = "MLV1227";
$categoria_referencia2 = "MLB432825";  // Categoría de juguetes para niñas en Brasil
$categoria_referencia3 = "MLB1648";  // Categoría de informática en Brasil
$categoria_referencia4 = "MLV1144";  // Categoría de videojuegos y consolas en Venezuela
$categoria_referencia5 = "MCO1144";  // Categoría de videojuegos y consolas en Colombia
$categoria_referencia6 = "MCO1648";  // Categoría de computación en Colombia
$SITE_ID = "MLB";  // Mercado Libre Brasil
$SITE_ID2 = "MLV";  // Mercado Libre Venezuela
$SITE_ID3 = "MCO";  // Mercado Libre Colombia

function meli_connect($app_id, $secret_api, $expires_in, $tokens){

	$meli = new Meli($app_id, $secret_api);
	if($expires_in + time() + 1 < time()){
		$params = array('access_token' => $tokens);
		echo "Bloque 1";
		
		// $body = array('title' => $nombre_producto, 'price' => round($result, 2), 'available_quantity' => $producto->cantidad, 'pictures' => $lista_fotos);
		
		// $response = $meli->put('/items/'.$producto->referencia, $body, $params);
		
		// print_r($response);
		
		// Ejecutamos el método de envío de ítems
		// $response_reg = $meli->post('/items', $item, $params);
		
		// $response_desc = $meli->put('/items/'.$producto->referencia.'/description', $body, $params);
	}else{
		if(isset($_GET['code'])) {
			echo "Bloque 2";
			// If the code was in get parameter we authorize
			$user = $meli->authorize($_GET['code'], 'https://octopus.meli/');
			 
			// Now we create the sessions with the authenticated user
			if(isset($user['body']->access_token)){
				echo "Bloque 3";
				$_SESSION['access_token'] = $user['body']->access_token;
				$_SESSION['expires_in'] = $user['body']->expires_in;
				
				$tokens = $user['body']->access_token;
				$expires_in = $user['body']->expires_in;
				
				// We can check if the access token in invalid checking the time
				if($_SESSION['expires_in'] + time() + 1 < time()) {
					try {
						print_r($meli->refreshAccessToken());
					} catch (Exception $e) {
						echo "Exception: ",  $e->getMessage(), "\n";
					}
				}
				
				$params = array('access_token' => $_SESSION['access_token']);
				
				// $body = array('title' => $nombre_producto, 'price' => round($result, 2), 'available_quantity' => $producto->cantidad, 'pictures' => $lista_fotos);
				
				// $response = $meli->put('/items/'.$producto->referencia, $body, $params);
				
				// print_r($response);
				
				// Ejecutamos el método de envío de items
				// $response_reg = $meli->post('/items', $item, $params);
				
				// $response_desc = $meli->put('/items/'.$producto->referencia.'/description', $body, $params);
			}else{
				echo "Bloque 4";
				$redirect = $meli->getAuthUrl('https://octopus.meli/', Meli::$AUTH_URL['MLV']);
				header("Location: ".$redirect);
			}
		}else{
			echo "Bloque 5";
			$redirect = $meli->getAuthUrl('https://octopus.meli/', Meli::$AUTH_URL['MLV']);
			header("Location: ".$redirect);
		}
	}
	
	return array($meli, $params, $expires_in);
}

?>
