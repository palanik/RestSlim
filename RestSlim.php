<?php

namespace RestSlim;

$_restActions = array();
// http://en.wikipedia.org/wiki/Representational_state_transfer#RESTful_web_APIs
$_restActions['index'] = $_restActions['list'] = array(
    "route" => '/',
    "method" => 'get'
);
$_restActions['putAll'] = array(
    "route" => '/',
    "method" => 'put'
);
$_restActions['post'] = $_restActions['create'] = array(
    "route" => '/',
    "method" => 'post'
);
$_restActions['deleteAll'] = $_restActions['delAll'] = array(
    "route" => '/',
    "method" => 'delete'
);

$_restActions['get'] = $_restActions['read'] = array(
    "route" => '/:id/?',
    "method" => 'get'
);
$_restActions['put'] = $_restActions['update'] = array(
    "route" => '/:id/?',
    "method" => 'put'
);
$_restActions['delete'] = $_restActions['del'] = array(
    "route" => '/:id/?',
    "method" => 'delete'
);

class RestSlim {

    const _VERSION = '0.0.1';

    protected $resourceName;
    protected $actions;
    protected $app;
    protected $_stack = array();

    public function __construct($resource, array $actions = array()) {
        global $_restActions;
        
        $this->resourceName = $resource;

        $this->actions = count($actions) > 0 ? $actions : $_restActions;
        $this->app = null;

    }

    protected function _viaApp($actionItem) {
        $restAction = $this->actions[$actionItem["actionName"]];

        // prepend route
        array_unshift($actionItem["callable"], $restAction["route"]);

        // Call app->METHOD
        $route = call_user_func_array(array($this->app, 
                                        $restAction["method"]
                                        ), 
                                    $actionItem["callable"]
                                    );
        // Set the route name.
        $route = $route->name($actionItem["actionName"]);

        if (array_key_exists("conditions", $restAction)) {
            $route = $route->conditions($restAction["conditions"]);
        }

        return $route;
    }
    /*
     * Handle REST actions
     */
    public function __call($name, $callable) {
        if (array_key_exists($name, $this->actions)) {
            $actionItem = array("actionName" => $name, "callable" => $callable);
            if (isset($this->app)) {
                $this->_viaApp($actionItem);
            }
            else {  // Defer action until app is available
                array_push($this->_stack, $actionItem);
            }
            return $this;
        }
        else {
            throw new \RuntimeException('No such RestSlim method defined. : '. $name);
        }
    }

    /*
     * Apply deferred actions to app
     */
    public function app($app) {
        $this->app = $app;

        foreach ($this->_stack as $actionItem) {
            $this->_viaApp($actionItem);
        }

        // flush stack
        $this->_stack = array();

        return $app;
    }

}

?>