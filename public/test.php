<?php


$mail = new PHPMailer;

$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";
$mail->Host = "smtp.gmail.com";
$mail->Username = "oupicky2@gmail.com";
$mail->Password = "oepkllebuoendtsc";
$mail->Port = "465";

$mail->From = 'oupicky2@gmail.com';
$mail->FromName = 'Honzaik';
$mail->addAddress('honzaik@seznam.cz', 'honzaik');  // Add a recipient
$mail->addReplyTo('oupicky2@gmail.com', 'derp');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
   echo 'Message could not be sent.';
   echo 'Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'Message has been sent';