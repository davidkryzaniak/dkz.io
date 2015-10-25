# monday.dkz.io

Just a smiple app to track Monster Monday

settings.php contains the list and some other vars... Not sure I like this implementation, but it works.

```php
define("twilio_sid",""); // Your Account SID from www.twilio.com/user/account
define("twilio_token",""); // Your Auth Token from www.twilio.com/user/account
define("twilio_phone","");

//db creds
define('CONST_DB_HOST', "");
define('CONST_DB_NAME', "");
define('CONST_DB_USER', "");
define('CONST_DB_PASS', "");


//user list
define('CONST_USER_LIST',json_encode(array(
	0 => array("name"=>"David","number"=>"+1XXXXXXXXXX","type"=>"Ripper"),
	1 => array("name"=>"TBone","number"=>"+1XXXXXXXXXX","type"=>"Ripper"),
	2 => array("name"=>"Dylan","number"=>"+1XXXXXXXXXX","type"=>"Random"),
	3 => array("name"=>"Brian","number"=>"+1XXXXXXXXXX","type"=>"Khaos"),
	4 => array("name"=>"Logan","number"=>"+1XXXXXXXXXX","type"=>"Khaos"),
	5 => array("name"=>"Jared","number"=>"+1XXXXXXXXXX","type"=>"Zero-Sugar")
)));
```

