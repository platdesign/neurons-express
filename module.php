<?PHP

	
	
	
	
	
	
	$module = nrns::module('express', ['router']);
	
	$module->config(function(){
	
		require 'provider/expressProvider.php';
	
	});
	
	
	$module->provider('expressProvider', 'express\\expressProvider');
	
	
	
	

?>