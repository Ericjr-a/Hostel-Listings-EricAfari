<?php
$host = 'localhost';
$dbUser = 'root';
$dbPassword = '@Afari728';
$dbName = 'hostel_listings';

$conn = new mysqli($host, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully"; 