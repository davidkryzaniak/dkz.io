<?php

date_default_timezone_set('America/Chicago');

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

file_put_contents('logs/.'.time().'.txt',print_r($_REQUEST,TRUE));

require_once(__DIR__.'/../classes/MonsterMonday.php');

$mm = new MonsterMonday();

//ensure we got a message from a known sender
if(
	!isset($_REQUEST['Body']) || empty($_REQUEST['Body']) || !isset($_REQUEST['From']) || empty($_REQUEST['From'])
	//|| !in_array($_REQUEST['From'],$mm->getAllPhoneNumbers())
) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	echo "unknown number or invalid body";exit;
}

$messageBody = ''.trim($_REQUEST['Body']);
$receivedFrom = ''.trim($_REQUEST['From']);

$timeIs = time() - strtotime("today"); //seconds since midnight
$dayOfWeekIs = date('N'); // 7 == Sunday

$TIME_NOON = 43200; //NOON
$TIME_LAST_CALL = 64875; //6:15pm

$mm->logMessage($receivedFrom,$messageBody);


// ================================ Do stuff with the message  ================================ //

// are we in the correct time slot (Sunday)
if ( 7 == $dayOfWeekIs && $timeIs > 43200 && $timeIs < 64875 ) {

	// is this the buyer?
	$theBuyer = $mm->getNextWeekBuyer();
	if ($theBuyer['number'] == $receivedFrom) {

		// check to make sure they have not verified already
		if (NULL !== $mm->getIsWeekStatusSet()) {
			$mm->setSendMessage($receivedFrom,'Sorry, you\'ve already replied a status for this week!');
			header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
		}


		// is this a confirmation from the buyer?
		if ('yes' == strtolower(substr($messageBody,0,4))) {
			$mm->setSendMessageToAll("Kudos to {$theBuyer['name']}! Monster Monday is a go.");

			$mm->setWeekStatus(TRUE); // mark this week as a success

			$mm->logMessage('localhost',"Kudos to {$theBuyer['name']}! Monster Monday is a go.");
			header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
		}


		// is this a cancelation from the buyer?
		if ('no' == strtolower(substr($messageBody,0,2)))  {
			$mm->setSendMessage($receivedFrom,"Monster Monday is off. {$theBuyer['name']} has declined. Well, this sucks.");

			$mm->setWeekStatus(FALSE); // mark this week as a fail

			$mm->logMessage('localhost',"Monster Monday is off. {$theBuyer['name']} has declined. Well, this sucks.");
			header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
		}

	}

}// end confirmation checks


// Does this message start with "List"
if( 'list' == strtolower(substr($messageBody,0,4)) ){
	$list = $mm->getShoppingList();

	//interesting use of native PHP functions
	$list = http_build_query($list,NULL,",\n");

	$mm->setSendMessage($receivedFrom,$list);
	header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
}


// Does this message start with "Next"
if( 'next' == strtolower(substr($messageBody,0,4)) ){
	$found = $mm->getNextWeekBuyer();
	$mm->setSendMessage($receivedFrom,"{$found['name']} is on Monster Monday duty for ".date('l, F jS',$found['weekOf']));
	header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
}

// Brodcast, send to all expect the sender
if( 'broadcast' == strtolower(substr($messageBody,0,9)) ){
	$messageBody = substr($messageBody,9);
	$messageBody = trim(trim($messageBody,':'));
	$mm->setSendMessageToAll($mm->getNameByNumber($receivedFrom).': '.$messageBody,$receivedFrom);
	header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;
}

// Does this message start with "Rotation" -- list the order of
//@todo check for rotation message

$mm->setSendMessage($receivedFrom,"Sorry, that's not a valid option. See http://monday.dkz.io/ for help... you n00b.");
header($_SERVER['SERVER_PROTOCOL'] . ' OK', true, 200);exit;