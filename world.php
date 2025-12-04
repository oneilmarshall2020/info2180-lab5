<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

// PDO connection with error handling
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$country = isset($_GET['country']) ? $_GET['country'] : '';
$lookupType = isset($_GET['lookup']) ? $_GET['lookup'] : 'country';

$results = [];

try {
    if ($lookupType === 'cities') {
        // --- CITY LOOKUP ---
        // Join cities with countries to filter by country name
        $sql = "SELECT cities.name, cities.district, cities.population 
                FROM cities 
                JOIN countries ON cities.country_code = countries.code 
                WHERE countries.name LIKE :countryQuery 
                ORDER BY cities.population DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['countryQuery' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Output Table Header for Cities
        echo '<table class="look-up-table">';
        echo '<thead><tr><th>Name</th><th>District</th><th>Population</th></tr></thead>';
        echo '<tbody>';
        
        // Output Data Rows
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['district']) . '</td>';
            echo '<td>' . htmlspecialchars($row['population']) . '</td>';
            echo '</tr>';
        }

    } else {
        // --- COUNTRY LOOKUP (Default) ---
        $sql = "SELECT * FROM countries WHERE name LIKE :countryQuery";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['countryQuery' => "%$country%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output Table Header for Countries
        // Note: I'm using column names based on standard World DB.
        echo '<table class="look-up-table">';
        echo '<thead><tr><th>Name</th><th>Continent</th><th>Independence Year</th><th>Head of State</th></tr></thead>';
        echo '<tbody>';

        // Output Data Rows
        foreach ($results as $row) {
            echo '<tr>';
            // Ensure these array keys match your specific database column names
            $name = isset($row['name']) ? $row['name'] : (isset($row['Name']) ? $row['Name'] : 'N/A');
            $continent = isset($row['continent']) ? $row['continent'] : (isset($row['Continent']) ? $row['Continent'] : 'N/A');
            // Try standard naming conventions for independence year
            $indepYear = isset($row['independence_year']) ? $row['independence_year'] : (isset($row['IndepYear']) ? $row['IndepYear'] : 'N/A');
            // Try standard naming conventions for Head of State
            $headOfState = isset($row['head_of_state']) ? $row['head_of_state'] : (isset($row['HeadOfState']) ? $row['HeadOfState'] : 'N/A');

            echo '<td>' . htmlspecialchars($name) . '</td>';
            echo '<td>' . htmlspecialchars($continent) . '</td>';
            echo '<td>' . htmlspecialchars($indepYear) . '</td>';
            echo '<td>' . htmlspecialchars($headOfState) . '</td>';
            echo '</tr>';
        }
    }

    echo '</tbody></table>';

    if (empty($results)) {
        echo '<p class="no-results">No results found for "' . htmlspecialchars($country) . '"</p>';
    }

} catch(PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}
?>