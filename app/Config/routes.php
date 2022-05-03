<?php
	Router::setExtensions(['xls']);
	Router::parseExtensions();
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

	Router::connect('/', ['controller' => 'dashboards', 'action' => 'home']);
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/state', ['controller' => 'pages', 'action' => 'state']);
	Router::connect('/pages/*', ['controller' => 'pages', 'action' => 'display']);

/**
 * Enable routing for the api to route to domain.com/api/controller/action.json
 */
	Router::connect('/api/:controller/:action/*', ['prefix' => 'api', 'api' => true]);
/**
 * Load all plugin routes.  See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
/*
 * Enable Router to handle the 'json' and 'csv' extensions
 */
Router::mapResources(['merchants']);
Router::parseExtensions('json', 'csv');
