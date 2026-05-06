<?php
namespace App;

/**
 * Gère le routage de l'application.
 * Analyse la méthode et l'url et appelle la méthode correspondante dans le contrôleur associé.
 */
class Router
{
    /**
     * @var array<int, array{method: string, path: string, controller: string, action: string}>
     *     Liste des routes enregistrées.
     */
    private array $routes = [];


    public function __construct()
    {
        $this->addRoute("GET", "/api/projects", "Api\ProjectController", "getProjects");
        $this->addRoute("GET", "/api/stacks", "Api\StackController", "getStacks");
    }

    /**
     * @param string $method Méthode HTTP (GET, POST, DELETE, etc...)
     * @param string $path Le chemin depuis la racine (/api/projects)
     * @param string $controller Le nom de la classe du contrôleur (sans le namespace)
     * @param string $action Le nom de la méthode à appeler
     * @return void
     */
    private function addRoute(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "controller" => $controller,
            "action" => $action
        ];
    }

    /**
     * Méthode qui instancie le contrôleur associé et appelle la méthode associée dans le tableau des routes.
     * @return void
     */
    public function run(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route["path"] === $uri && $route["method"] === $method) {
                $controllerName = "App\\Controllers\\" . $route["controller"];
                $action = $route["action"];

                $controller = new $controllerName();
                $controller->$action();
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
        exit;
    }

}