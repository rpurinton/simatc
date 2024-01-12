<?php
$id = $_GET['id'];

$sql = mysqli_connect("127.0.0.1", "simatc", "simatc", "simatc_db");
$resultOrig = $sql->query("SELECT * FROM prefroutes WHERE Orig = '$id'");
$resultDest = $sql->query("SELECT * FROM prefroutes WHERE Dest = '$id'");

$origRoutes = [];
$destRoutes = [];
while ($row = $resultOrig->fetch_assoc()) {
    $origRoutes[] = $row;
}
while ($row = $resultDest->fetch_assoc()) {
    $destRoutes[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Details for <?php echo $id; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6">
                <h1 class="text-center">Origin Routes for <?php echo $id; ?></h1>
                <?php foreach ($origRoutes as $route) : ?>
                    <div class="card bg-secondary text-white mb-3">
                        <div class="card-header">Route: <?php echo $route['Route_String']; ?></div>
                        <div class="card-body">
                            <p class="card-text">Destination: <?php echo $route['Dest']; ?></p>
                            <p class="card-text">Hours: <?php echo $route['Hours1'] . ', ' . $route['Hours2'] . ', ' . $route['Hours3']; ?></p>
                            <!-- Add more fields as needed -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-12 col-md-6">
                <h1 class="text-center">Destination Routes for <?php echo $id; ?></h1>
                <?php foreach ($destRoutes as $route) : ?>
                    <div class="card bg-secondary text-white mb-3">
                        <div class="card-header">Route: <?php echo $route['Route_String']; ?></div>
                        <div class="card-body">
                            <p class="card-text">Origin: <?php echo $route['Orig']; ?></p>
                            <p class="card-text">Hours: <?php echo $route['Hours1'] . ', ' . $route['Hours2'] . ', ' . $route['Hours3']; ?></p>
                            <!-- Add more fields as needed -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>