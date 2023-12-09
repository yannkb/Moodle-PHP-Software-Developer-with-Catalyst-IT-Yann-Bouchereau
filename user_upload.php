<?php

$dbUser = $options['u'];
$dbPass = $options['p'];
$dbHost = $options['h'];

// Database configuration
$dbName = 'app';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$firstRow = true;
while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    // Ignore the first row of csv file
    if ($firstRow === true) {
        $firstRow = false;
        continue;
    }

    // Capitalize name and surname, lowercase email
    $name = ucfirst(strtolower($data[0]));
    $surname = ucfirst(strtolower($data[1]));
    $email = strtolower($data[2]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format: $email\n";
        continue;
    }

    $name = $conn->real_escape_string(ucfirst(strtolower($data[0])));
    $surname = $conn->real_escape_string(ucfirst(strtolower($data[1])));
    $email = $conn->real_escape_string(strtolower($data[2]));

    // Insert data into the database
    $sql = "INSERT INTO users (name, surname, email) VALUES ('$name', '$surname', '$email')";
    var_dump($sql);
    if ($conn->query($sql) !== true) {
        echo "Error inserting record: " . $conn->error . "\n";
    }
}

echo "Data inserted successfully.\n";

fclose($handle);
$conn->close();
