<?php

// Extract options
$csvFilePath = $options['file'];
$createTable = isset($options['create_table']);
$dryRun = isset($options['dry_run']);
$dbUser = $options['u'];
$dbPass = $options['p'];
$dbHost = $options['h'];

// Database configuration
$dbName = 'app';

// Create or rebuild the users table if necessary
if ($createTable) {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "DROP TABLE IF EXISTS users;
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                surname VARCHAR(255),
                email VARCHAR(255)
            )";

    if ($conn->multi_query($sql)) {
        echo "Table 'users' created/rebuilt successfully.\n";
    } else {
        echo "Error creating/rebuilding table: " . $conn->error . "\n";
        exit(1);
    }

    $conn->close();
}

// Validate and insert data from CSV file into the database (if not in dry run mode)
if (!$dryRun && ($handle = fopen($csvFilePath, "r")) !== false) {
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
} elseif ($dryRun) {
    echo "Dry run mode. No data will be inserted into the database.\n";
}
