<?php
namespace Publi;

define('STYLES', "/Assets/styles/main.css"); 

use App\Core\Router;
use App\Core\ConstantMaker;
use App\Middlewares\Middleware;

session_start();

require "../Autoload.php";

\App\Autoload::register();
new ConstantMaker();

$uriExploded = explode("?", $_SERVER["REQUEST_URI"]);
$uri = $uriExploded[0];

if( preg_match('/\/site\/+/', $uri) ){
	if( file_exists('../Cms/index.php') ){
		include '../Cms/index.php';
		\CMS\handleCMS($uri);
	}else{
		die('Missing required cms file');
	}
	return;
}


$router = new Router($uri, "../routes.yml");
$c = $router->getController();
$a = $router->getAction();
$m = $router->getMiddleware();

if($m){
	Middleware::$m();
}

if( file_exists("../Controllers/".$c.".php")){

	include "../Controllers/".$c.".php";
	// SecurityController =>  App\Controller\SecurityController

	$c = "App\\Controller\\".$c;
	if(class_exists($c)){
		// $controller ====> SecurityController
		$cObjet = new $c();
		if(method_exists($cObjet, $a)){
			$cObjet->$a();
		}else{
			die("L'action' : ".$a." n'existe pas");
		}

	}else{
		die("La classe controller : ".$c." n'existe pas");
	}

}else{
	die("Le fichier controller : ".$c." n'existe pas");
}








