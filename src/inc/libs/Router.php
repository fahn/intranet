<?php

/**
 * Badminton Intranet System
 * Copyright 2017-2024
 * All Rights Reserved
 *
 * Copying, distribution, usage in any form is not
 * allowed without  written permit.
 *
 * Stefan Metzner <stefan@weinekind.de>
 * Philipp M. Fischer <phil.m.fischer@googlemail.com>
 *
 **/
namespace Badtra\Intranet\Libs;

use Symfony\Component\Yaml\Yaml;

class Router
{
    private $routes = [];

    public function __construct($routesFile)
    {
        $this->loadRoutes($routesFile);
    }

    private function loadRoutes($routesFile)
    {
        $routes = Yaml::parseFile($routesFile);
        foreach ($routes as $routeName => $routeConfig) {
            $this->routes[$routeConfig['path']] = $routeConfig;
        }
    }

    public function dispatch($uri)
    {
        foreach ($this->routes as $path => $routeConfig) {
            $regex = $this->convertPathToRegex($path, $routeConfig['requirements'] ?? []);
            if (preg_match($regex, $uri, $matches)) {
                $controllerName = $routeConfig['controller'];
                $methodName = $routeConfig['method'];

                // Vollständiger Controller-Name mit Namespace
                $controllerClass = "Badtra\\Intranet\\Controller\\{$controllerName}";
                if (!class_exists($controllerClass)) {
                    throw new \Exception("Controller $controllerClass not found.");
                }

                $controller = new $controllerClass();

                // Entferne den ersten Match (vollständige Übereinstimmung)
                
                array_shift($matches);

                return call_user_func_array([$controller, $methodName], $matches);
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "Seite nicht gefunden";
    }

    private function convertPathToRegex($path, $requirements)
    {
        $regex = preg_quote($path, '#');

        // Parameter in der Route erkennen und durch Regex-Patterns ersetzen
        $regex = preg_replace_callback('#\\\{(\w+)\\\}#', function ($matches) use ($requirements) {
            $param = $matches[1];
            if (isset($requirements[$param])) {
                return '(' . $requirements[$param] . ')';
            }
            return '(\w+)';
        }, $regex);

        return '#^' . $regex . '$#';
    }
}
