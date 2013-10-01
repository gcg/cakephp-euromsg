cakephp-euromsg
===============

MailTransport class to send emails over Euromsg webservices. 

Example Config 
==============
in your app/Config/email.php file
```
	public $euromsg = array(
		'from' => array('example@example.com' => 'Example Site'),
		'FromName' => 'Example Site',
		'FromAddress' => 'example@example.com',
		'ReplyAddress' => 'reply@example.com',
		'url' => 'http://ws.euromsg.com/ecomm/',
		'username' => 'euromsg_username',
		'password' => 'euromsg_password',
		'transport' => 'Euromsg'
	);
```

Example Usage
=============
in any of your controller or shell 
```
App::uses('CakeEmail', 'Network/Email');
$Email = new CakeEmail('euromsg');
$Email->to('guneycan@gmail.com');
$Email->template('default', 'default');
$Email->emailFormat('html');
$Email->subject('Test Subject');
$Email->send("Lets test drive this puppy");
```

This example uses default email layout and default email view template in CakePHP. 

Notes
===============
According to euromsg api, you must
* use html in your messages, which means your mail body must contain 
```
	<html><body> MESSAGE </body></html>
```
* In your config, FromName and FromAddress variables must be same as in your euromsg admin panel.  