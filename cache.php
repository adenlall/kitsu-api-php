<?php

class Cache
{
	
	public $cache_dir = WP_CONTENT_DIR . '/cache/adentheme/';
	public $cache_duration = 0;
	
	public function get_http($url, $cache_key) {
		
		$cache_file = $this->cache_dir . md5($cache_key) . '.json';
		$this->check_dir_exs();
		
		if (file_exists($cache_file) && $this->get_exp($cache_file)) {
			return file_get_contents($cache_file);
		}else{
			$ch = curl_init();
			curl_setopt( $ch,
						 CURLOPT_URL,
						 $url
			);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			$response = curl_exec($ch);

			if (!curl_errno($ch)) {
				if (file_put_contents($cache_file, $response) === false) {
					error_log("Failed to write to cache file: $cache_file");
				}
			} else {
				error_log("cURL Error: " . curl_error($ch));
			}

			curl_close($ch);
			return $response;
		}
	}
	private function check_dir_exs(){
		if (!is_dir($this->cache_dir)) {
			mkdir($this->cache_dir, 0755, true); // Create the directory recursively
		}
	}
	private function get_exp(){
		if($this->cache_duration <= 0){
			return true;
		}
		return (bool) (time() - filemtime($cache_file)) < $this->cache_duration;
	}


}

?>
