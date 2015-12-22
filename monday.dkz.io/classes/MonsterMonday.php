<?php

/**
 * Created by PhpStorm.
 * User: davidkryzaniak
 * Date: 18/10/15
 * Time: 13:19
 */
require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../../settings.php');
date_default_timezone_set('America/Chicago');

class MonsterMonday {

	private $_db_host = '';
	private $_db_name = '';
	private $_db_user = '';
	private $_db_pass = '';

	public $order = array();

	private $weekBuyer = -1;
	private $weekCount = -1;
	private $offsetWeekCount = 0;
	private $beginningOfTime = -1;

	public function __construct()
	{
		$this->_db_host = CONST_DB_HOST;
		$this->_db_user = CONST_DB_USER;
		$this->_db_name = CONST_DB_NAME;
		$this->_db_pass = CONST_DB_PASS;

		$this->order = json_decode(CONST_USER_LIST,TRUE);

		$this->beginningOfTime = strtotime('2015-10-11 00:00:00'); //note, this was a Sunday
		$this->weekCount = floor((strtotime(date('Y-m-d 00:00:00')) - $this->beginningOfTime ) / 604800); //nth week of monster monday
		$this->weekCount += $this->offsetWeekCount; //offset incase we need to adjust the week count
		$this->weekBuyer = ($this->weekCount % count($this->order)); //weeks MOD buyers = current buyer
	}

	public function getWeekBuyer()
	{
		$data = $this->order[$this->weekBuyer];
		$data['weekOf'] = strtotime('Monday this week');
		return $data;
	}

	public function getNextWeekBuyer()
	{
		$data = $this->order[(($this->weekCount+1) % count($this->order))];
		$data['weekOf'] = strtotime("Monday next week");
		return $data;
	}

	public function getShoppingList()
	{
		// return array_count_values(array_map(function($foo){return $foo['type'];}, $this->order));
		$string="";
		foreach($this->order as $single){
			$string .= $single["name"] . ": " . $single["type"] . "\n";
		}
		return $string;
	}

	public function getAllPhoneNumbers()
	{
		return array_map(function($foo){return $foo['number'];}, $this->order);
	}

	public function getName($id)
	{
		return $this->order[$id]['name'];
	}

	public function getNameByNumber($number)
	{
		if('localhost' == strtolower($number)){return 'Server - Automated Message';}
		foreach($this->order as $person){
			if($number == $person['number'])
				return $person['name'];
		}
		return 'Unknown';
	}

	public function setSendMessageToAll($message,$exceptNumber = NULL)
	{
		foreach($this->getAllPhoneNumbers() as $key=>$contact){
			if ($exceptNumber != $contact) {
				$this->setSendMessage($contact,$message);
			}
		}
	}

	public function setWeekStatus($bool)
	{
		$dbh = $this->_connectToDatabase();
		$stmt = $dbh->prepare("INSERT INTO weekly_status (week, status) VALUES (:week, :status) ON DUPLICATE KEY UPDATE status = :status");
		$stmt->bindParam(':week', $this->weekCount);
		$stmt->bindParam(':status', $bool);
		$stmt->execute();
	}

	public function getIsWeekStatusSet()
	{
		$dbh = $this->_connectToDatabase();
		$stmt = $dbh->prepare("SELECT status FROM weekly_status WHERE week = :week");
		$stmt->bindParam(':week', $this->weekCount);
		$stmt->execute();
		$results = $stmt->fetchAll();
		return (!isset($results[0]['status']) ? NULL : (bool) $results[0]['status']);
	}

	public function setSendMessage($to,$message)
	{
		$client = new Services_Twilio(twilio_sid, twilio_token);
		$message = $client->account->messages->sendMessage( twilio_phone, $to, $message );
		return $message->sid;
	}

	private function _connectToDatabase()
	{
		return new PDO("mysql:host={$this->_db_host};dbname={$this->_db_name}", $this->_db_user, $this->_db_pass);
	}

	public function getNewestMessages()
	{
		$dbh = $this->_connectToDatabase();
		$stmt = $dbh->prepare("SELECT * FROM log LIMIT 20");
		$stmt->execute();
		return array_reverse($stmt->fetchAll());
	}

	public function logMessage($phone,$message)
	{
		$dbh = $this->_connectToDatabase();
		$stmt = $dbh->prepare("INSERT INTO log (phone, message) VALUES (:phone, :message)");
		$stmt->bindParam(':phone', $phone);
		$stmt->bindParam(':message', $message);
		$stmt->execute();
	}
}
