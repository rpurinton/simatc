<?php
$sql = mysqli_connect("127.0.0.1", "simatc", "simatc", "simatc_db");
$result = $sql->query("SELECT airport FROM (
    SELECT DISTINCT Orig AS airport FROM prefroutes
    UNION
    SELECT DISTINCT Dest FROM prefroutes
) AS combined ORDER BY airport;");

$airports = [];
while ($row = $result->fetch_assoc()) {
    $firstLetter = strtoupper(substr($row['airport'], 0, 1));
    $airports[$firstLetter][] = $row['airport'];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Airports</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php foreach (range('A', 'Z') as $letter) : ?>
                    <a href="#<?php echo $letter; ?>" class="text-white"><?php echo $letter; ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php foreach ($airports as $letter => $airportNames) : ?>
            <div class="row" id="<?php echo $letter; ?>">
                <div class="col-12">
                    <h2><?php echo $letter; ?></h2>
                    <p><?php echo implode(', ', $airportNames); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>