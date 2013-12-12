#neurons-express#

A json-api-module for [Neurons](https://github.com/platdesign/Neurons).

##install##
`bower install neurons-express --save`

##provider#

###$expressProvider###

Equivalent to neurons-router ($routeProvider) the expressProvider allows to
define route-handlers for different request-methods: `get`, `post`, `put`, `delete` and `all`.

The following example takes effect for all request-methods.

####get($route, $closure)####

	$routeProvider->get('/account', function($account) {
		
		if($account->isOnline()) {
			return $account;
		} else {
			$this->error('Account is offline', 401, 'To see this data you have to be signed in');
		}
		
	});

