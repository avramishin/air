<?php
namespace Air;

abstract class Controller
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var mixed
     */
    protected $response;

    function __construct()
    {
        $this->action();
    }

    abstract function action();

    /**
     * @return \Twig_Environment
     */
    function getTwig()
    {
        if (!$this->twig) {
            $loader = new \Twig_Loader_Filesystem(ROOT . "/app/views");
            $this->twig = new \Twig_Environment($loader, [
                'cache' => ROOT . "/data/tmp/twig",
            ]);
        }

        return $this->twig;
    }

    function jsonResponse()
    {
        if (!$this->response) {
            $this->response = new JsonResponse();
        }

        return $this->response;
    }

    /**
     * Get request value and trim it
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    function r($param, $default = '')
    {
        if (isset($_REQUEST[$param]))
            return is_array($_REQUEST[$param]) ? $_REQUEST[$param] : trim($_REQUEST[$param]);
        return $default;
    }
}