<?php

class Router {
    private $routes = [];

    public function addRoute($method, $route, $controllerAction) {
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'controllerAction' => $controllerAction
        ];
    }

    public function match($method, $route) {
        foreach ($this->routes as $routeInfo) {
            // Convertir la route en regex pour gérer les paramètres dynamiques
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $routeInfo['route']);
            
            // Comparer l'URL avec la route, en utilisant la regex
            if ($routeInfo['method'] === $method && preg_match('#^' . $pattern . '$#', $route, $matches)) {
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


