<?php

require_once('lib/FatSecretAPI.php');
require_once('lib/config.php');

class Controller {
	public $responseXml;
	public $ingredientData = array();
	public $ingredientMax = 0;
	
	public function fetch($ingredient, $query) {
		$this->responseXml = simplexml_load_file("http://www.recipepuppy.com/api/?i=$ingredient&q=$query&p=1&format=xml");
	}
	
	
	public function getIngredientData($ingredient) {
		$this->ingredientMax++;
		if($this->ingredientMax > 2 && !isset($this->ingredientData[$ingredient])) return array();
		if(isset($this->ingredientData[$ingredient])) {
			echo "found in cache<br />";
			return $this->ingredientData[$ingredient];
		}
		
		$API = new FatSecretAPI(API_KEY, API_SECRET);
		
		$arr = preg_split("/[|-\s]+/", $API->Search($ingredient)->food->food_description);
		$arr2 = array();
		for($i = 2; $i < sizeof($arr); $i += 2) {
			$arr2[strtolower(str_replace(":","",$arr[$i]))] = $arr[$i+1];
		}
		
		$this->ingredientData[$ingredient] = $arr2;
		
		return $arr2;
	}
	
	public function processData() {
		$j = 0;
		foreach($this->responseXml->recipe as $recipe) {
			$j++;
			//if($j > 1) return;
			$ingredients = explode(",", $recipe->ingredients);
			
			$ingredientData = $recipe->addChild("ingredientData");
			foreach($ingredients as $ingredient) {
				
				$data = $this->getIngredientData($ingredient);
				
				$ingchild = $ingredientData->addChild("ingredient");
				$ingchild->addAttribute("name", str_replace(" ", "", $ingredient));
				foreach ($data as $key => $value) {
				    $ingchild->addChild($key, $value);
				}
				
			}
		}
		
	}
	
	public function toXML() {
		return $this->responseXml->asXML();
	}
	
}

?>