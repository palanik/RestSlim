<?php

namespace RestSlim;

class RestSlim {

	const _VERSION = '0.0.1';

	protected $resourceName;
	protected $actions;
	protected $app;
	protected $_stack = array();

	public function __construct($resource, array $actions = array()) {
		$this->resourceName = $resource;

		$this->actions = $actions;
		$this->app = null;

	}

	protected function _viaApp($actionItem) {
		$restAction = $this->actions[$actionItem["actionName"]];

		// prepend route
		array_unshift($actionItem["callable"], $restAction["route"]);

		// Call app->METHOD
		return call_user_func_array(array($this->app, 
										$restAction["method"]
										), 
									$actionItem["callable"]
									);
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
			else {	// Defer action until app
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
