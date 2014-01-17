<?PHP 
	
	namespace express;
	use nrns;
	
	
	
	class expressException extends \Exception {
		public function __construct($message, $code=null, $description=null) {
			parent::__construct($message, $code);
			$this->desc = $description;
		}
		
		public function getDesc() {
			return $this->desc;
		}
	}

		
	class expressProvider extends nrns\Provider {
		
		// Load routeSetter-methods (when, get, put, post, delete)
		use \router\routeSetter;
			
		public function __construct($routeProvider, $injectionProvider, $response) {
			$this->injectionProvider = $injectionProvider;
			$this->routeProvider = $routeProvider;
			$this->res = $response;
			
			$this->exceptionHandler('express\\expressException', function($error){
				return $this->createErrorObject($error);
			});
			$this->exceptionHandler('Exception', function($error){
				return $this->createErrorObject($error);
			});
		}
		
		public function addRoute($method, $route, $closure) {
			$this->routeProvider->addRoute($method, $route, function()use($closure){
				
				$this->handleRoute($closure);
			
			});
		}
		
		public function otherwise($closure) {
			$this->routeProvider->otherwise($closure);
		}
	
	
		private function handleRoute($dataClosure) {
			
			$this->res->ContentType('JSON');
			
			try {
				$response = $this->injectionProvider->invoke($dataClosure->bindTo($this));
			} catch(\Exception $e) { 
				$response = $this->handleException($e);
				$this->res->setCode($response->error->code);
			}
			
			$this->res->setBody(json_encode($response, JSON_NUMERIC_CHECK));
		}
	
		public function error($message, $code=400, $description=null) {
			throw new expressException($message, $code, $description);
		}

		public function createErrorObject($error) {
			
			$code = ($error->getCode()!==0) ? $error->getCode() : $this->res->getCode();

			return (object) [
				'error'	=>	(object) [
					'code'			=>	$code,
					'message'		=>	$error->getMessage(),
					'description'	=>	method_exists($error, 'getDesc') ? $error->getDesc() : null
				]
			];
			
		}
		
		public function exceptionHandler($name, $handler) {
			
			$this->exceptionHandlers[$name] = $handler;
			
			
			return $this;
		}
	
		private function handleException($error) {
			$name = get_class($error);
			
			if( isset($this->exceptionHandlers[$name]) && $handler = $this->exceptionHandlers[get_class($error)] ) {
				return $this->injectionProvider->invoke($handler->bindTo($this), ['error'=>$error]);
			} else {
				throw $error;
			}
		}
	
	}
	
	

?>