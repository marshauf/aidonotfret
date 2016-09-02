<?php


interface iDice  {
    public function roll();
}

class LocalDice implements iDice {

    public function roll() {
        return rand(1, 6);
    }
}

/**
 * Dice simulates a six sided dice.
 */
class Dice implements iDice
{
	private static $url = "https://api.random.org/json-rpc/1/invoke";
    private static $precalculate = 500; // request random numbers preemptively to reduce API requests
    private static $min = 1;
    private static $max = 6;
    private $numbers = []; // 
    private $apiKey;
    private $id;
	
	public function __construct($apiKey) {
		$this->apiKey = $apiKey;
	}
	
	private function call($method, $params) {
        $request = new stdClass();
        $request->params = $params;
        $request->jsonrpc = '2.0';
        $request->method = $method;
        $request->id = (string)($this->id++); 
        $data = json_encode($request);
		$ch = curl_init(self::$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data))                                                                       
        );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$result=curl_exec($ch);
		curl_close($ch);
		$data = json_decode($result, true);
        if (array_key_exists('error', $data)) {
            throw new Exception($data['error']['message']);
        }
        return $data['result'];
	}
	
	private function generateIntegers($n, $min, $max) {
        $params = new stdClass();
        $params->apiKey = $this->apiKey;
        $params->n = $n;
        $params->min = $min;
        $params->max = $max;
		$result = $this->call("generateIntegers", $params);
		return $result["random"]["data"];
	}

	public function roll() {
        if (count($this->numbers) == 0) {
            $this->numbers = $this->generateIntegers(self::$precalculate, self::$min, self::$max);
        }
        return array_shift($this->numbers);
	}
}

?>