<?php
//namespace App;
class Router {
    private $routes = [];

    public function addRoute($method, $route, $controllerAction) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controllerAction' => $controllerAction,
            'middlewares' => []
        ];
        return $this; // Permet le chaînage
    }

    public function addMiddleware($middleware) {
        $this->routes[count($this->routes) - 1]['middlewares'][] = $middleware;
        return $this; // Retourne $this pour permettre le chaînage
    }

    public function match($method, $route) {
        foreach ($this->routes as $routeInfo) {
            // Convertir la route en regex pour gérer les paramètres dynamiques
            $pattern = preg_replace('/{([a-zA-Z0-9_]+)}/', '(?P<$1>[a-f0-9\-]{36}|[0-9]+|[a-zA-Z0-9_-]+)', $routeInfo['route']);
            
            // Comparer l'URL avec la route, en utilisant la regex
            if ($routeInfo['method'] === $method && preg_match('#^' . $pattern . '$#', $route, $matches)) {

                // Check MiddleWares
                foreach ($routeInfo['middlewares'] as $middleware) {
                    if (!$middleware->handle()) {
                        return null; // Si le middleware échoue, ne pas continuer
                    }
                }

                // Si la route correspond, on retourne l'action du contrôleur avec les paramètres extraits
                return [
                    'controllerAction' => $routeInfo['controllerAction'],
                    'params' => array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY) // Extraire uniquement les paramètres nommés
                ];
            }
        }
        return null;
    }
}


