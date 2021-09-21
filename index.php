<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

require_once "meli_connect.php";


list($meli, $params, $expires_in) = meli_connect($app_id, $secret_api, $expires_in, $tokens);

// Devuelve información sobre los sitios donde Mercado Libre está disponible.
function get_sites($meli, $params){
	
	$response = $meli->get('/sites', $params);
	
	return $response;
}

// Devuelve las categorías disponibles en el sitio.
function get_categories($meli, $SITE_ID3, $params){
	
	$response = $meli->get('/sites/'.$SITE_ID3.'/categories', $params);
	
	return $response;
}

// Devuelve los productos más vendidos de un sitio por categoría
function get_bests_sellers($meli, $SITE_ID3, $categoria_referencia6, $params){
	
	$response = $meli->get('/highlights/'.$SITE_ID3.'/category/'.$categoria_referencia6, $params);
	
	return $response;
}

$bests_sellers = get_bests_sellers($meli, $SITE_ID3, $categoria_referencia6, $params);

// Devuelve una cadena con los ids de los productos más vendidos por categoría.
// Si no hay productos se devuelve un arreglo vacío.
function return_ids($bests_sellers){
	$bests_sellers_ids = array();
	if(isset($bests_sellers['body']->content)){
		foreach($bests_sellers['body']->content as $best_seller){
			$bests_sellers_ids[] = $best_seller->id;
		}
		$bests_sellers_ids = implode(",", $bests_sellers_ids);
	}
	return $bests_sellers_ids;
}

$get_ids = return_ids($bests_sellers);

// Devuelve los datos de los productos más vendidos por categoría.
// Recibe una cadena con los ids de los productos.
function multiget_data_items($meli, $get_ids, $params){
	
	if(is_string($get_ids)){
		$response = $meli->get('/items?ids='.$get_ids, $params);
		
		return $response;
	}
}

$data_items = multiget_data_items($meli, $get_ids, $params);

echo "<pre>";
echo "Hola mundo";

echo "<pre>";
print_r($params);

echo "<pre>";
print_r($expires_in);

echo "<pre>";
print_r($bests_sellers);

echo "<pre>";
print_r($get_ids);

//~ echo "<pre>";
//~ print_r($data_items);
echo "<br>";
echo "<br>";

// Imprimiendo datos e imágenes
if(is_string($get_ids)){
	foreach($data_items['body'] as $key => $item){
		if($item->code == 200){
			echo "Key: ".$key;
			echo "<br>";
			echo "Producto: ".$item->body->title;
			echo "<br>";
			echo "Precio: ".$item->body->price;
			echo "<br>";
			echo "Condición: ".$item->body->condition;
			echo "<br>";
			if(count($item->body->pictures) > 0){
				echo "<img src='".$item->body->pictures[0]->url."' width='250' height='200'";
				echo "<br>";
				echo "<br>";
			}
		}else{
			echo "Key: ".$key;
			echo "<br>";
			echo "Código de respuesta: ".$item->code;
			echo "<br>";
			echo "Mensaje: ".$item->body->message;
			echo "<br>";
			echo "<br>";
		}
	}
}

// Imprimiendo sitios de Mercado Libre
$sites = get_sites($meli, $params);

echo "<pre>";
print_r($sites);

// Imprimiendo categorías de sitio específico de Mercado Libre
$categories = get_categories($meli, $SITE_ID3, $params);

echo "<pre>";
print_r($categories);

?>
