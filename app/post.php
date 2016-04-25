<?php
require_once "../Slim/Slim.php";
Slim\Slim::registerAutoloader ();

$app = new \Slim\Slim (); // slim run-time object

require_once "config/config.inc.php";

$app->map ( "/posts(/:id)", function ($postID = null) use($app) {

	$httpMethod = $app->request->getMethod ();
	$action = null;
	$parameters ["id"] = $postID; // prepare parameters to be passed to the controller (example: ID)

	if (($postID == null) or is_numeric ( $postID )) {
		switch ($httpMethod) {
			case "GET" :
				if ($postID != null)
					$action = ACTION_GET_POST;
					else
						$action = ACTION_GET_POSTS;
						break;
			case "POST" :
				$action = ACTION_CREATE_POST;
				break;
			case "PUT" :
				$action = ACTION_UPDATE_POST;
				break;
			case "DELETE" :
				$action = ACTION_DELETE_POST;
				break;
			default :
		}
	}
	return new loadRunMVCComponents ( "PostModel", "PostController", "jsonView", $action, $app, $parameters );
} )->via ( "GET", "POST", "PUT", "DELETE" );

$app->run ();


class loadRunMVCComponents {
	public $model, $controller, $view;
	public function __construct($modelName, $controllerName, $viewName, $action, $app, $parameters = null) {
		include_once "models/" . $modelName . ".php";
		include_once "controllers/" . $controllerName . ".php";
		include_once "views/" . $viewName . ".php";
		
		$model = new $modelName (); // common model
		$controller = new $controllerName ( $model, $action, $app, $parameters );
		$view = new $viewName ( $controller, $model, $app, $app->headers ); // common view
		$view->output (); // this returns the response to the requesting client
	}
}

?>