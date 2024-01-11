<?php
$sql = mysqli_connect("127.0.0.1","simatc","simatc","simatc_db");
$result = $sql->query("SELECT airport FROM (
    SELECT DISTINCT Orig AS airport FROM prefroutes
    UNION
    SELECT DISTINCT Dest FROM prefroutes
) AS combined ORDER BY airport;");
while($row = $result->fetch_assoc())
{
	print_r($row);
}

