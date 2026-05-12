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
        $this->addRoute("GET", "/", "MainController", "render");

        $this->addRoute("GET", "/api/projects", "Api\ProjectController", "getProjects");
        $this->addRoute("GET", "/api/stacks", "Api\StackController", "getStacks");

        $this->addRoute("POST", "/api/login", "Api\AuthController", "login");
        $this->addRoute("POST", "/api/logout", "Api\AuthController", "logout");
        $this->addRoute("POST", "/api/refresh", "Api\AuthController", "refresh");

        $this->addRoute("POST", "/api/contact", "Api\ContactController", "handleContact");
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
     * Méthode qui affiche la page 404.
     * @return void
     */
    private function render404(): void
    {
        http_response_code(404);
        $title = "404 - Page Introuvable";
        ob_start();
        require_once ROOT . "/views/404.html";
        $content = ob_get_clean();
        require_once ROOT . "/views/layout.php";
        exit;
    }

    /**
     * Méthode qui instancie le contrôleur associé et appelle la méthode associée dans le tableau des routes.
     * @return void
     */
    public function run(): void
    {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = substr($uri, 0, -1);
        }

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

        $this->render404();
    }

}