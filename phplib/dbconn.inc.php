<?php
/*
 * Mongo DB connection 
 */
if (isset($MDB_userName)) {
    $dbconnStr = "mongodb://${MDB_userName}:${MDB_password}@${MDB_hostname}";
} else {
    $dbconnStr = "mongodb://${MDB_hostname}";
}
$dbConn = new MongoDB\Client($dbconnStr,
        [],
        [
            'typeMap' => [
                'array' => 'array',
                'document' =>'array',
                'root' => 'array'
            ]
        ]);
// Database selection
$db = $dbConn->$MDB_database;
// Collections handlers
$collections = $db->listCollections();
$cols=[];
foreach ($collections as $col) {
    $cols[$col->getName()] = $dbConn->selectCollection($MDB_database, $col->getName());
}
