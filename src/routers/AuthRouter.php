<?php
namespace App\Routers;

class AuthRouter extends AuthentificationController {
    private $url;

    private $routes;

    private $requestMethod;


    function __construct($url, $requestMethod)
    {
        ///Add some data there next time
        $this->auth = new AuthController();

        $this->route = parse_url($url, PHP_URL_PATH);

        $this->requestMethod = $requestMethod;

        $this->routes = [
            'POST' => [
                '/create.user' => function() {

                },
            ],
            'PUT' => [
                '/update.user' =>function() {

                }
            ]
        ];
    }

    function router()
    {
        if(array_key_exists($this->route, $this->routes)) {
            return $this->routes["$this->requestMethod"]["$this->route"]();
        } else {
            return json_encode([
                'error' => true,
                'message' => 'Маршрут не найден',
                'code' => 404,
                'method' => $this->requestMethod,
                'uri' => $this->url
            ]);
        }
    }


}