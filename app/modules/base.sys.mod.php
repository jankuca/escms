<?php

TPL::addTpl('header');

TPL::modify('TPL::addTpl(\'side\');');
TPL::modify('TPL::addTpl(\'footer\');');

/*
// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/accounts/o8/id');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xrds+xml'));
#curl_setopt($ch, CURLOPT_HEADER, 0);

// grab URL and pass it to the browser
echo(curl_exec($ch));

// close cURL resource, and free up system resources
curl_close($ch);
*/
?>