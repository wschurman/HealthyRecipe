<?php

require_once('lib/FatSecretAPI.php');
require_once('lib/config.php');

class Controller {
	public $responseXml;
	
	public function fetch($ingredient, $query) {
		$this->responseXml = simplexml_load_file("http://www.recipepuppy.com/api/?i=$ingredient&q=$query&p=1&format=xml");
	}
	
	
	public function getIngredientData($ingredient) {
		//cache/memoize it
		$data = array();
		
		$API = new FatSecretAPI(API_KEY, API_SECRET);
		
	}
	
	public function processData() {
		
		foreach($this->responseXml->recipe as $recipe) {
			$ingredients = explode(",", $recipe->ingredients);
			
			$ingredientData = $recipe->addChild("ingredientData");
			foreach($ingredients as $ingredient) {
				
				$data = $this->getIngredientData($ingredient);
				
				$ingchild = $ingredientData->addChild("ingredient");
				$ingchild->addChild("fat", $data["fat"]);
			}
		}
		
	}
	
	public function toXML() {
		return $responseXml->asXML();
	}
	
}

?>