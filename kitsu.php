<?php

require_once(realpath(dirname(__FILE__) . '/../classes/cache.php'));

class Kitsu {
	
	public $type;
	public $limitation;
	public $cache;
	
	public function __construct($type,$limitation)
	{
		$this->type=$type;
		$this->limitation=$limitation;
		$this->cache=new Cache();
	}
	public function get_details_with_name($query){
			$res = $this->get_by_name($query);
			if(isset($res["data"]) && count($res["data"])!==0){
				$id = $res["data"][0]["id"];
				$genres = $this->get_genres($id);
				$catego = $this->get_categories($id);
				$result = $this->merge([
					["data"      , $res["data"][0]],
					["genres"    , $genres["data"]],
					["categories", $catego["data"]],
				]);
				return $result;//$this->cache($query, $result);
			}else{
				$this->type = "text";
				return $this->get_details_with_name($this->replace_slug($query));//$this->cache($query, $this->get_details_with_name($this->replace_slug($query)));
			}
			
	}

	// public methods
	public function get_anime($id){
		return $this->response($id);
	}
	public function get_by_name($query){
		return $this->response("?filter[".$this->type."]=".$query."&page[".$this->limitation."]=1&page[offset]=0");
	}
	public function get_genres($id){
		return $this->response($id."/genres");
	}
	public function get_categories($id){
		return $this->response($id."/categories");
	}
	
	// private methods
	private function replace_slug($ubject){
		return str_replace(
			"-",
			" ",
			$ubject
		);
	}
	private function response($param){
		return $this->parse($this->cache->get_http(
			"https://kitsu.io/api/edge/anime/{$param}",
			str_replace("/","-",$param)
		));
	}
	private function parse($response){
		if(isset($response)){
			return json_decode($response, true);
		}else{
			return null;
		}
	}
	private function merge($arrs){
		$arr = [];
		for ($i = 0; $i < count($arrs); $i++) {
			$arr[$arrs[$i][0]] = $arrs[$i][1];
		}
		return $arr;
	}
}
