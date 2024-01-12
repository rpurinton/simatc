<?php
// Database connection
$sql = mysqli_connect("127.0.0.1", "simatc", "simatc", "simatc_db");
if ($sql->connect_error) {
    die("Connection failed: " . $sql->connect_error);
}
// Fetch distinct values for filters
$distinctAltitudes = $sql->query("SELECT DISTINCT Altitude FROM prefroutes ORDER BY Altitude ASC");
if (!$distinctAltitudes) {
    die("Query failed: " . $sql->error);
}
$distinctAircrafts = $sql->query("SELECT DISTINCT Aircraft FROM prefroutes ORDER BY Aircraft ASC");
if (!$distinctAircrafts) {
    die("Query failed: " . $sql->error);
}
$distinctTypes = $sql->query("SELECT DISTINCT Type FROM prefroutes ORDER BY Type ASC");
if (!$distinctTypes) {
    die("Query failed: " . $sql->error);
}

// Fetch all routes for a given ID
$id = isset($_GET['id']) ? mysqli_real_escape_string($sql, $_GET['id']) : 'JFK';
$routesResult = $sql->query("SELECT * FROM prefroutes WHERE Orig = '$id' OR Dest = '$id' ORDER BY Dest, Seq");

// Process routes into a structured array
$routes = [];
while ($row = $routesResult->fetch_assoc()) {
    if ($row['Orig'] === $id) {
        $routes['orig'][$row['Dest']][] = $row;
    } else {
        $routes['dest'][$row['Orig']][] = $row;
    }
}

// Sort the routes by destination and sequence
foreach ($routes as $type => $destinations) {
    ksort($destinations);
    foreach ($destinations as $destination => $routeList) {
        usort($routeList, function ($a, $b) {
            return $a['Seq'] <=> $b['Seq'];
        });
        $routes[$type][$destination] = $routeList;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Route Board</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the board */
        .route-board {
            margin-top: 20px;
        }

        .route-board h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .route-list {
            list-style: none;
            padding: 0;
        }

        .route-list li {
            cursor: pointer;
            padding: 5px 10px;
            border-bottom: 1px solid #ccc;
        }

        .route-list li:hover {
            background-color: #f5f5f5;
        }

        .route-details {
            display: none;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="bg-dark text-white">
    <div class="container">
        <!-- Filters -->
        <div class="row">
            <div class="col">
                <!-- Dropdown filters for Altitude, Aircraft, Type, etc. -->
                <select id="filterAltitude" class="form-control mb-3">
                    <option value="">Select Altitude</option>
                    <?php while ($altitude = $distinctAltitudes->fetch_assoc()) : ?>
                        <option value="<?php echo htmlspecialchars($altitude['Altitude']); ?>">
                            <?php echo htmlspecialchars($altitude['Altitude']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select id="filterAircraft" class="form-control mb-3">
                    <option value="">Select Aircraft</option>
                    <?php while ($aircraft = $distinctAircrafts->fetch_assoc()) : ?>
                        <option value="<?php echo htmlspecialchars($aircraft['Aircraft']); ?>">
                            <?php echo htmlspecialchars($aircraft['Aircraft']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <select id="filterType" class="form-control mb-3">
                    <option value="">Select Type</option>
                    <?php while ($type = $distinctTypes->fetch_assoc()) : ?>
                        <option value="<?php echo htmlspecialchars($type['Type']); ?>">
                            <?php echo htmlspecialchars($type['Type']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <!-- Origin and Destination Sections -->
        <div class="row route-board">
            <div class="col">
                <h1>Origin Routes from <?php echo htmlspecialchars($id); ?></h1>
                <ul class="route-list" id="originRoutes">
                    <?php foreach ($routes['orig'] as $destination => $routeList) : ?>
                        <li data-destination="<?php echo htmlspecialchars($destination); ?>">
                            <?php echo htmlspecialchars($destination); ?> (<?php echo count($routeList); ?> routes)
                        </li>
                        <div class="route-details" id="details-<?php echo htmlspecialchars($destination); ?>">
                            <?php foreach ($routeList as $route) : ?>
                                <div>
                                    <?php
                                    foreach ($route as $key => $value) {
                                        if (!empty(trim($value))) {
                                            echo "<strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "<br>";
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col">
                <h1>Destination Routes to <?php echo htmlspecialchars($id); ?></h1>
                <ul class="route-list" id="destinationRoutes">
                    <?php foreach ($routes['dest'] as $origin => $routeList) : ?>
                        <li data-origin="<?php echo htmlspecialchars($origin); ?>">
                            <?php echo htmlspecialchars($origin); ?> (<?php echo count($routeList); ?> routes)
                        </li>
                        <div class="route-details" id="details-<?php echo htmlspecialchars($origin); ?>">
                            <?php foreach ($routeList as $route) : ?>
                                <div>
                                    <?php
                                    foreach ($route as $key => $value) {
                                        if (!empty(trim($value))) {
                                            echo "<strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "<br>";
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for interactivity and filtering
        document.addEventListener('DOMContentLoaded', function() {
            var originListItems = document.querySelectorAll('#originRoutes li');
            var destinationListItems = document.querySelectorAll('#destinationRoutes li');

            function toggleDetails(event) {
                var targetId = 'details-' + event.target.getAttribute('data-destination') || event.target.getAttribute('data-origin');
                var detailsDiv = document.getElementById(targetId);
                if (detailsDiv) {
                    detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' : 'none';
                }
            }

            originListItems.forEach(function(li) {
                li.addEventListener('click', toggleDetails);
            });

            destinationListItems.forEach(function(li) {
                li.addEventListener('click', toggleDetails);
            });

            document.getElementById('filterAltitude').addEventListener('change', function() {
                // Filter functionality goes here
            });

            document.getElementById('filterAircraft').addEventListener('change', function() {
                // Filter functionality goes here
            });

            document.getElementById('filterType').addEventListener('change', function() {
                // Filter functionality goes here
            });


        });
    </script>
</body>

</html>