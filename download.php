<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM file_upload";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
    <html>
    <head>
        <title> Download PDF</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>
    <div class="container">
<h1>Download PDF </h1>
<div class="table-responsive">
    <table class= "table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Sr .No.</th>
                <th>File Name</th>
                <th>Action</th>
</tr>
</thead>
<tbody>
    $count = 1;
    if ($result->num_rows > 0){
        while($row = $$result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$count."</td>";
            echo "<td>".$row['filename']."</td>";
            echo "<td><a href="uploads/' .$row['filename'].'" class="btn btn-info" download>Download</a></td>';
            echo "</tr>";
            $count++;
        }
    }else{
        echo"<tr><td colspan='3'> No records found.</td></tr>";
    }
    ?>
</tbody>
</table>
</div>
</body>
</html>

