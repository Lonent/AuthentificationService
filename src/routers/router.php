<?php
class AuthentificationRouter extends AuthentificationController {
    private $url;

    private $routes;

    private $requestMethod;

    function __construct($url, $requestMethod)
    {
        $this->route = parse_url($url, PHP_URL_PATH);

        $this->requestMethod = $requestMethod;

        $this->routes = [
            'POST' => [
                '/create.user' => function() {

                },
            ],
            'PUT' => [
                '/update.user' =>
            ]
        ];
    }

    function router()
    {
        if(array_key_exists($this->route, $this->routes)) {
            return $this->routes["$this->route"]();
        } else {
            return json_encode([
                'error' => true,
                'message' => 'Маршрут не найден',
                'code' => 404,
                'method' => $this->routes["$this->route"][],
                'uri' => $requestUri
            ]);
        }
    }


}