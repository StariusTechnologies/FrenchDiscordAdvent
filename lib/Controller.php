<?php

namespace Befew;

class Controller {
    protected static ?Controller $instance = null;

    protected Request $request;
    protected Template $template;

    public function __construct(string $action) {
        $action = $action . 'Action';
        $reflector = new \ReflectionClass(get_class($this));

        $path = 'src/' . $reflector->getNamespaceName() . '/View/';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        $templatePath = BEFEW_BASE_URL . DIRECTORY_SEPARATOR . $path;
        $assetsPath = '..' . DIRECTORY_SEPARATOR . $path;

        $this->template = new Template($templatePath, $assetsPath);
        $this->request = Request::getInstance();

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            $this->errorAction();
        }
    }

    public function errorAction(int $code = 404): void {
        Response::throwStatus($code);
    }
}