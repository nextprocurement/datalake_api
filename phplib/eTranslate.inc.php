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

function setListenSocket(){
    set_time_limit($GLOBALS['eTRANSLATE_SOCK_TIMEOUT']);
    ob_implicit_flush(true);

    $sock = create_socket();

    if (socket_bind($sock, '127.0.0.1', $GLOBALS['eTRANSLATE_SOCK_PORT']) === false) {
        error_log("socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)));
        return false;
    }

    if (socket_listen($sock, 5) === false) {
        error_log("socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)));
        return false;
    }
    $data = [];
    do {
        if (($msgsock = socket_accept($sock)) === false) {
            error_log ("socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)));
            break;
        }
        $json = socket_read($msgsock, 1024);
        if ($json === false) {
            error_log("socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)));
            socket_close($msgsock);
            break;
        }
        $data = json_decode($json, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decoding error: ' . json_last_error_msg());
        }
        socket_close($msgsock);
        socket_close($sock);
        break;
    }
    while (true);

    return $data;
}

function create_socket() {
    if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
        echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
    }
    return $sock;
}
function processCallBack($json){
    $sock = create_socket();
    socket_connect($sock, '127.0.0.1', $GLOBALS['eTRANSLATE_SOCK_PORT']);
    socket_write($sock, $json, strlen($json));
}