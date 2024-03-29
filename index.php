<?php
$sql = mysqli_connect("127.0.0.1", "simatc", "simatc", "simatc_db");
$result = $sql->query("SELECT airport FROM (
    SELECT DISTINCT Orig AS airport FROM prefroutes
    UNION
    SELECT DISTINCT Dest FROM prefroutes
) AS combined ORDER BY airport;");

$airports = [];
$fixes = [];
while ($row = $result->fetch_assoc()) {
    $firstLetter = strtoupper(substr($row['airport'], 0, 1));
    if (strlen($row['airport']) == 3) {
        $airports[$firstLetter][] = $row['airport'];
    } elseif (strlen($row['airport']) == 5) {
        $fixes[$firstLetter][] = $row['airport'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Airports and Fixes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6">
                <?php foreach (range('A', 'Z') as $letter) : ?>
                    <a href="#a<?php echo $letter; ?>" class="text-white"><?php echo $letter; ?></a>
                <?php endforeach; ?>
                <h1 id="airports">Airports and VORs</h1>
                <?php foreach ($airports as $letter => $airportNames) : ?>
                    <div class="row" id="a<?php echo $letter; ?>">
                        <div class="col-12">
                            <h2><?php echo $letter; ?></h2>
                            <p>
                                <?php foreach ($airportNames as $name) : ?>
                                    <a href="/view.php?id=<?php echo $name; ?>" class="text-white"><?php echo $name; ?></a>
                                <?php endforeach; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-12 col-md-6">
                <?php foreach (range('A', 'Z') as $letter) : ?>
                    <a href="#f<?php echo $letter; ?>" class="text-white"><?php echo $letter; ?></a>
                <?php endforeach; ?>
                <h1 id="fixes">Fixes</h1>
                <?php foreach ($fixes as $letter => $fixNames) : ?>
                    <div class="row" id="f<?php echo $letter; ?>">
                        <div class="col-12">
                            <h2><?php echo $letter; ?></h2>
                            <p>
                                <?php foreach ($fixNames as $name) : ?>
                                    <a href="/view.php?id=<?php echo $name; ?>" class="text-white"><?php echo $name; ?></a>
                                <?php endforeach; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>