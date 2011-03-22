<?php

class Controller {
	public $responseXml;
	
	public function fetch($ingredient, $query) {
		$this->responseXml = simplexml_load_file("http://www.recipepuppy.com/api/?i=$ingredient&q=$query&p=1&format=xml");
	}
	
	
	public function getIngredientData($ingredient) {
		//cache/memoize it
		$data = array();
		
		$headers = array(
			'oauth_consumer_key' => '',
			'oauth_nonce' => '',
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => '',
			'oauth_version' => '1.0',
			
		);
		$normalized = "POST&http%3A%2F%2Fplatform.fatsecret.com%2Frest%2Fserver.api&"
		
		$ing_xml = simplexml_load_file("http://platform.fatsecret.com/rest/server.api?food_id=33691&method=food.get&oauth_consumer_key=9a1a6fd1-fff5-433f-9dd7-7daa4587bf5d&oauth_nonce=1234&oauth_signature=sAyYTJiIxOGkvFpBcH8L%2BlFQRCQ%3D&oauth_signature_method=HMAC-SHA1&oauth_timestamp=1245126631&oauth_version=1.0");
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