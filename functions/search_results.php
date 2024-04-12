<?php
require 'connection.php';

$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$defaultImagePath = "/path/to/default_hostel_image.jpg";

if (strlen($searchQuery) >= 4) {
    $stmt = $conn->prepare("SELECT Hostels.*, Images.image_path AS image_path FROM Hostels
                             LEFT JOIN Images ON Hostels.hostel_id = Images.hostel_id
                             WHERE Hostels.name LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("s", $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = !empty($row['image_path']) ? "/hostel_images/" . htmlspecialchars($row['image_path']) : $defaultImagePath;
            echo "<div class='card'>";
            echo "<img src='{$imagePath}' alt='Image of " . htmlspecialchars($row['name']) . "' style='width:100%;'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['address']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "No hostels found with the search criteria.";
    }
    $stmt->close();
} else {
    echo "Search term is too short.";
}

$conn->close();
