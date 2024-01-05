<?php

$datetimeString = '2023-12-31T23:00:00.000Z';

// Create a DateTime object from the string
$dateTime = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $datetimeString);

// Set the timezone to UTC
$dateTime->setTimezone(new DateTimeZone('UTC'));

// Now, $dateTime contains the parsed date and time in the UTC timezone
