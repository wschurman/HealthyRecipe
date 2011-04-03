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
	
	/* loads in initial recipe data from recipepuppy */
	public function fetch($query) {
		$this->query = $query;
		$this->responseXml = simplexml_load_file("http://www.recipepuppy.com/api/?&q=$query&p=1&format=xml");
	}
	
	/* apparently this is used */
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
	
	/* called right after fetch to process the data. Simultaneously merges the xml sources (fatsecret, recipepuppy) and creates an atom compliant feed */
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
			
			$ingredientData = $recipe->addChild("ingredientData");
			$atomingredients = $entry->addChild("ingredientData");
			$atomingredients->addAttribute("xmlns", "http://ingredientdata.com");
			
			foreach($ingredients as $ingredient) {
				
				$data = $this->getIngredientData($ingredient);
				
				$ingchild = $ingredientData->addChild("ingredient");
				$atomingchild = $atomingredients->addChild("ingredient");

				$ingchild->addAttribute("name", str_replace(" ", "", $ingredient));
				$atomingchild->addAttribute("name", str_replace(" ", "", $ingredient));
							
				foreach ($data as $key => $value) {
				    $ic = $ingchild->addChild("nutrient", $value);
					$ic->addAttribute("name", $key);

					$ic2 = $atomingchild->addChild("nutrient", $value);
					$ic2->addAttribute("name", $key);
				}
				
			}
		}
		$this->atom = $feed;
	}
	
	/* returns the recipe xml */
	public function toXML() {
		return $this->responseXml->asXML();
	}
	
	/* returns the atom */
	public function toAtom() {
		return $this->atom->asXML();
	}
	
	/* curl inserts into exist DB */
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
