# Air controllers
to create Air controller just extend `Air\Controller` and implement `action()` method. Typical controller looks like that:

```php
class MyHelloWorldController extends Air\Controller
{
    /**
     * This is main controller method
     */
    function action()
    {
        /**
         * Get twig template from /app/views/hello/world.twig 
         * render it with string 'Hello, World!' and name from request
         * and display
         */
        echo $this->getTwig()->render("hello/world.twig", [
            'text' => 'Hello, World!',
            'name' => $this->r('name')
        ]);
    }
}

# Do not not forget this line, actual run of your controller
new MyHelloWorldController();
```
Air has file based routing as default. For example, if you want to create controller for `http://domain.com/posts/save` then create file `/app/controllers/posts/save.php` and put code there. 

There is no special naming convention for controller class name, but it is recommended to give it name after actual path. So, `/app/controllers/posts/save.php` would contain class `PostsSaveController`. 

There several useful methods in `Air\Controller` class:
* `Air\Controller::r($name, $default = '')` - to get and trim value from `$_REQUEST['name']` if value does not exist `$default` is returned. If `$_REQUEST['name']` is not simple scalar value, for example Array, it is returned as is.
* `Air\Controller::jsonResponse()` - to create and get instance of `Air\JsonResponse` if you need to output JSON. 
* `Air\Controller::getTwig` - to get instance of `Twig_Environment` to render templates from `/app/views/`

