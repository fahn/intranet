<?php

require __DIR__ . '/vendor/autoload.php';

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

$mail = new Message;
$mail->setFrom('John <john@example.com>')
	->addTo('stefan@weinekind.de')
	->setSubject('Order Confirmation')
	->setBody("Hello, Your order has been accepted.");


$mailer = new SendmailMailer;
$mailer->send($mail);

echo 123;

echo __DIR__;

?>
