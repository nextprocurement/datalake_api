<?php
function sendRequest($sourceLanguage, $targetLanguages, $textToTranslate){
    $host = $GLOBALS['eTRANSLATE_API'];
   
    $caller_information = array(
            'application' => 'nextProcurement',
            'username' => $GLOBALS['eTRANSLATE_USER']
    );
    
    $data = [
            "callerInformation" => $caller_information,
            'requesterCallback' => $GLOBALS['eTRANSLATE_CALLBACK'],
            "textToTranslate" =>  $textToTranslate,
            "sourceLanguage" => $sourceLanguage,
            "targetLanguages" => $targetLanguages
    ];
    
    $post = json_encode($data);
    if ($post === false) {
        error_log('JSON encoding error: ' . json_last_error_msg());
        return false;
    }

    $client=curl_init($host);  
   
    curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($client, CURLOPT_POST, 1);
    curl_setopt($client, CURLOPT_POSTFIELDS, $post);
    curl_setopt($client, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($client, CURLOPT_USERPWD, $GLOBALS['eTRANSLATE_USER']. ":" . $GLOBALS['eTRANSLATE_PASS']);
    curl_setopt($client, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($client, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($client, CURLOPT_TIMEOUT, 100);

    curl_setopt($client, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($post)
    )); 

    $response = curl_exec($client); 

    if (curl_errno($client)) {
        error_log("Curl error: ".curl_error($client));
        return false;
    }

    $idRequest = json_decode($response); 
    if ($idRequest === null && json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decoding error: ' . json_last_error_msg());
        return false;
    }

    return $idRequest;
}

function setListenSocket($idRequest, $numRequests) {
    set_time_limit($GLOBALS['eTRANSLATE_SOCK_TIMEOUT']);
    ob_implicit_flush(true);

    $sock = create_socket();

    $socketPath = "/tmp/eTrans".$idRequest.".sock";

    error_log("Socket path server: ".$socketPath);

    $bind = socket_bind($sock, $socketPath);
    if ($bind === false) {
        error_log("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)));
        return false;
    }

    $data = [];
    $requests = 0;
    do {
        socket_recvfrom($sock, $json, 1024, 0, $socketPath);
        if ($json === false) {
            error_log("socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)));
            socket_close($msgsock);
            break;
        }
        $data[] = json_decode($json, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decoding error: ' . json_last_error_msg());
        }
        $requests++;
        if ($requests == $numRequests) {
            socket_close($sock);
            break;
        }
    }
    while (true);

    return $data;
}

function create_socket() {
    $sock = socket_create(AF_UNIX, SOCK_DGRAM, 0);
    if ($sock === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    }
    return $sock;
}
function processCallBack($idRequest, $json){
    $sock = create_socket();
    $socketPath = "/tmp/eTrans".$idRequest.".sock";
    error_log("Socket path client: ".$socketPath);
    socket_connect($sock, $socketPath);
    socket_write($sock, $json, strlen($json));
}