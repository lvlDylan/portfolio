<?php
namespace App;

use App\Middlewares\AuthMiddleware;

/**
 * Gère le routage de l'application.
 * Analyse la méthode et l'url et appelle la méthode correspondante dans le contrôleur associé.
 */
class Router
{
    /**
     * @var array<int, array{method: string, path: string, controller: string, action: string, protected: bool}>
     *     Liste des routes enregistrées.
     */
    private array $routes = [];


    public function __construct()
    {
        $this->addRoute("GET", "/", "MainController", "render");

        $this->addRoute("GET", "/api/projects", "Api\ProjectController", "getProjects", false);
        $this->addRoute("GET", "/api/stacks", "Api\StackController", "getStacks", false);

        $this->addRoute("POST", "/api/login", "Api\AuthController", "login", false);
        $this->addRoute("POST", "/api/logout", "Api\AuthController", "logout");
        $this->addRoute("POST", "/api/refresh", "Api\AuthController", "refresh");

        $this->addRoute("POST", "/api/projects", "Api\ProjectController", "createProject");
        $this->addRoute("POST", "/api/projects/edit", "Api\ProjectController", "updateProject");
        $this->addRoute("DELETE", "/api/projects/delete", "Api\ProjectController", "deleteProject");

        $this->addRoute("POST", "/api/stacks", "Api\StackController", "createStack");
        $this->addRoute("POST", "/api/stacks/edit", "Api\StackController", "updateStack");
        $this->addRoute("DELETE", "/api/stacks/delete", "Api\StackController", "deleteStack");

        $this->addRoute("POST", "/api/contact", "Api\ContactController", "handleContact", false);
    }

    /**
     * @param string $method Méthode HTTP (GET, POST, DELETE, etc...)
     * @param string $path Le chemin depuis la racine (/api/projects)
     * @param string $controller Le nom de la classe du contrôleur (sans le namespace)
     * @param string $action Le nom de la méthode à appeler
     * @return void
     */
    private function addRoute(string $method, string $path, string $controller, string $action, bool $protected = true): void
    {
        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "controller" => $controller,
            "action" => $action,
            "protected" => $protected
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

            if ($route["path"] === $uri) {
                if ($method === $route["method"]) {
                    $controllerName = "App\\Controllers\\" . $route["controller"];
                    $action = $route["action"];

                    if (!empty($route["protected"])) {
                        AuthMiddleware::accept();
                    }

                    $controller = new $controllerName();
                    $controller->$action();
                } else {

                    if (str_starts_with($uri, "/api/")) {
                        header("Content-Type: application/json; charset=utf-8");
                        http_response_code(405);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Méthode HTTP {$method} non autorisée pour cette action."
                        ]);
                    } else {
                        header("HTTP/1.1 405 Method Not Allowed");
                        http_response_code(405);
                        echo "405 - Méthode non autorisée";
                    }
                }

                exit;
            }
        }

        if (str_starts_with($uri, "/api/")) {
            header("Content-Type: application/json; charset=utf-8");
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Cette API n'existe pas."
            ]);
        } else {
            $this->render404();
        }

        exit;
    }

}