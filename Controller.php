<?php

require_once('lib/FatSecretAPI.php');
require_once('lib/config.php');

class Controller {
	public $responseXml;
	public $atom;
	public $query;
	public $ingredientData = array();
	public $ingredientMax = 0;
	private $API;
	
	function __construct() {
		 $this->API = new FatSecretAPI(API_KEY, API_SECRET);
	}
	
	public function fetch($query) {
		$this->query = $query;
		//$this->responseXml = $this->API->runQuery("method=recipes.search&searh_expression=$query&format=xml&max_results=1");
		$this->responseXml = simplexml_load_file("http://www.recipepuppy.com/api/?i=$ingredient&q=$query&p=1&format=xml");
	}
	
	
	/* No Longer Used */
	public function getIngredientData($ingredient) {
		$this->ingredientMax++;
		if($this->ingredientMax > 40 && !isset($this->ingredientData[$ingredient])) return array();
		if(isset($this->ingredientData[$ingredient])) {
			return $this->ingredientData[$ingredient];
		}
		
		$arr = preg_split("/[|-\s]+/", $this->API->Search($ingredient)->food->food_description);
		$arr2 = array();
		for($i = 2; $i < sizeof($arr); $i += 2) {
			$arr2[strtolower(str_replace(":","",$arr[$i]))] = $arr[$i+1];
		}
		$arr2["amount"] = $arr[1];
		$this->ingredientData[$ingredient] = $arr2;
		
		return $arr2;
	}
	
	public function processData() {
		$start = "<feed xmlns='http://www.w3.org/2005/Atom'></feed>";
		$this->atom = new SimpleXMLElement($start);
		$feed = $this->atom;
		//$feed->addChild("title", $this->query);
		//add link to exist xml representation
		//$feed->addChild();
		
		foreach($this->responseXml->recipe as $recipe) {
			$entry = $feed->addChild("entry");
			$entry->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
			$entry->addChild("title", $recipe->title[0]);
			$link = $entry->addChild("link");
			$link->addAttribute("href", $recipe->href[0]);
			
			$ingredients = explode(",", $recipe->ingredients);
			$recipe->addChild("name", $recipe->title[0]);
			
			$desc = "";
			
			$ingredientData = $recipe->addChild("ingredientData");
			foreach($ingredients as $ingredient) {
				
				$data = $this->getIngredientData($ingredient);
				
				$desc .= str_replace(" ", "", $ingredient)."\n";
				$ingchild = $ingredientData->addChild("ingredient");
				$ingchild->addAttribute("name", str_replace(" ", "", $ingredient));
				foreach ($data as $key => $value) {
				    $ic = $ingchild->addChild("nutrient", $value);
					$ic->addAttribute("name", $key);
					$desc .= $key." : ".$value."\n";
				}
				
			}
			$entry->addChild("summary", $desc);
		}
		$this->atom = $feed;
	}
	
	public function processRecipes() {
		$recipe = $this->responseXml->recipe;
		$id = $recipe->recipe_id[0];
		$recipe_full = $this->API->runQuery("method=recipe.get&recipe_id=$id&format=xml");
		$ingredients = $recipe->addChild("ingredients");
		foreach($recipe_full->ingredients->ingredient as $ingredient) {
			$ingchild = $ingredients->addChild("ingredient");
			$ingchild->addChild("food_name", $ingredient->food_name[0]);
			$ingchild->addChild("number_of_units", $ingredient->number_of_units[0]);
			$ingchild->addChild("measurement_description", $ingredient->measurement_description[0]);
			$fid = $ingredient->food_id;
			$food = $this->API->runQuery("method=food.get&food_id=$fid&format=xml");
			foreach($food->servings->serving as $serving) {
				if(strcmp($serving->measurement_description[0], $ingredient->measurement_description[0]) == 0) {
					foreach($serving->children() as $child) {
						if($child->getName() != "serving_url") $ingchild->addChild($child->getName(), $child[0]);
						else $ingchild->addChild($child->getName(), urlencode($child[0]));
					}
				}
			}
		}
	}
	
	public function toXML() {
		return $this->responseXml->asXML();
	}
	
	public function toAtom() {
		return $this->atom->asXML();
	}
	
	public function insertIntoExist() {
		$initATOM = '<?xml version="1.0" ?> <feed xmlns="http://www.w3.org/2005/Atom"> <title>'.$this->query.'</title><updated>'.time().'</updated></feed>';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/exist/atom/edit/4302Collection/".$this->query);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/atom+xml'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $initATOM);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		$output2 = "";
		$entries = "";
		foreach($this->atom->entry as $entry) {
			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_URL, "http://localhost:8080/exist/atom/edit/4302Collection/".$this->query);
			curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-type: application/atom+xml'));
			curl_setopt($ch2, CURLOPT_POST, true);
			curl_setopt($ch2, CURLOPT_POSTFIELDS, $entry->asXML());
			$entries = $entry->asXML();
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
			$output2 = curl_exec($ch2);
			curl_close($ch2);
		}
		
		return $output;
		//var_dump($entries."\n\n".$output2);
	}
}

?>