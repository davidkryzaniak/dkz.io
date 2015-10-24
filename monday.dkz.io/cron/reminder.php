<?php
/**
 * Created by PhpStorm.
 * User: davidkryzaniak
 * Date: 24/10/15
 * Time: 16:10
 */

require_once(__DIR__.'/../classes/MonsterMonday.php');
date_default_timezone_set('America/Chicago');

$mm = new MonsterMonday();

$timeIs = time() - strtotime("today"); //seconds since midnight
$dayOfWeekIs = date('N'); // 7 == Sunday

$TIME_NOON = 43200; //NOON
$TIME_LAST_CALL = 64875; //6:15pm


// are we in the correct time slot (Sunday)
if ( 7 == $dayOfWeekIs && $timeIs > 43000 && $timeIs < 64875 ) {

	// is this the buyer?
	$theBuyer = $mm->getNextWeekBuyer();

	$mm->setSendMessage($theBuyer['number'],'MONSTER MONDAY REMINDER: Ohh Snap! It\'s your turn to buy! Please reply YES or NO to confirm.');

	exit;

}


if ( 7 == $dayOfWeekIs && $timeIs >  64975 && $mm->getIsWeekStatusSet() === NULL) {

	// is this the buyer?
	$theBuyer = $mm->getNextWeekBuyer();

	$mm->setSendMessageToAll("MONSTER MONDAY has been cancelled. {$theBuyer['name']} never replied to the reminder");

	exit;

}