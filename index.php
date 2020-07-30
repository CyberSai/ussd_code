<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'Db.php';

header('Content-Type: application/json');

$phoneNumber = $_POST['msisdn'];
$network = $_POST['network'];
$input = $_POST['ussdString'];
$opt = $_POST['ussdServiceOp'];
$sessionId = $_POST['sessionID'];

$code = 2;

$db = Db::getInstance();

if ($opt == 1) {
    $db->insert([
        'phone_number' => $phoneNumber,
        'network' => $network,
        'session_id' => $sessionId,
        'stage' => 'second'
    ]);
    $message = "Welcome to my first USSD";
} elseif ($opt == 18) {
    $record = $db->get($sessionId);

    switch($record['stage']) {
        case 'second':
            $db->update($record['id'], ['stage' => 'third']);
            $message = 'Welcome to second menu';
        break;

        case 'third':
            $db->update($record['id'], ['stage' => 'final']);
            $message = 'Welcome to third menu';
        break;
        default:
            $message = 'Could not find stage';
            $code = 17;
    }
}

echo json_encode([
    'message' => $message,
    'ussdServiceOp' => $code,
]);