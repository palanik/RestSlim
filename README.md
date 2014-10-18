RestSlim
========

Minimalist REST framework for [Slim](http://www.slimframework.com/).

[Slim](http://www.slimframework.com/) is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.

Shift your focus from Routes to Resources, while building RESTful API with php Slim.

## Greetings Tutorial
```php
$app = new \Slim\Slim();

// This should obviously go in a datastore
$data = array();
$data["1"] = array("id" => 1,
                "message" => "Hello, World!");
$data["2"] = array("id" => 2,
                "message" => "Good Bye!");


$greetings = new \RestSlim\RestSlim("greetings");

// List
$greetings->list(function() use ($app, $data) {
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->write(json_encode($data, JSON_NUMERIC_CHECK));
});

// Read
$greetings->get(function($id) use ($app, $data) {
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->write(json_encode($data[$id], JSON_NUMERIC_CHECK));
});

// Create
$greetings->create(function() use ($app, $data) {
	$request = $app->request();
	$message = json_decode($request->getBody(), true);
	$id = count($data) + 1;
	$message["id"] = $id;
	$data[$id] = $message;
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->write(json_encode($data, JSON_NUMERIC_CHECK));
})
// Update
$greetings->update(function($id) use ($app, $data) {
	$request = $app->request();
	$message = json_decode($request->getBody(), true);
	$message["id"] = $id;
	$data[$id] = $message;
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->write(json_encode($data, JSON_NUMERIC_CHECK));
})
// Delete
->delete(function($id) use ($app, $data) {
    $app->response->headers->set('Content-Type', 'application/json');
    $app->response->write(json_encode($data[$id], JSON_NUMERIC_CHECK));
    unset($data[$id]);
});

// Inject into Slim
$greetings->app($app)
            ->run();
```

Slim applications are built by mapping routes to callback functions for specific HTTP request methods.
But, RESTFul APIs are more about resources and actions on the resources, than about the routes. RestSlim brings the two together. It enhances Slim in creating RESTful applications with ease.

Create action oriented, restful resources independently and then inject them to your Slim application.
Add multiple resources to the same Slim app.

Although, you create your resources independently, RestSlim integrates with a Slim application to serve the resources.

RestSlim adheres to Slim framework's guiding principle: Cleanliness over terseness and common cases over edge cases. 

## License

  [MIT](LICENSE)
