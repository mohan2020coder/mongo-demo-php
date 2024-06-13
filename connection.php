<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Include Composer autoload file

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");

    // Check the connection
    $client->listDatabases();

    // Select a database
    $db = $client->my_database;

    // Select a collection
    $collection = $db->my_collection;

    echo "Connected to MongoDB!";
} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    echo "Failed to connect to MongoDB: ", $e->getMessage();
} catch (Exception $e) {
    echo "An error occurred: ", $e->getMessage();
}
?>
