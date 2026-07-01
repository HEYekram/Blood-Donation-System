<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests List</title>
    <link rel="stylesheet" href="requests_list.css">
    <script src="https://kit.fontawesome.com/ac5b348608.js" crossorigin="anonymous"></script>

</head>

<body>
    <section>
        <h1><span>Requests </span>list</h1>
        <div class="tbl-header">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Blood Group</th>
                        <th>Quantity</th>
                        <th>Needed By</th>
                        <th>Contact</th>
                        <th>Hospital Name</th>
                        <th>Hospital Unit</th>
                        <th>Location</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tbl-content">
            <table>
                <tbody>
                    <?php
                        $conn = mysqli_connect("localhost", "root", "", "370_blood_bank");
                        
                        $currentDate = date("Y-m-d");
                        $query = "SELECT * FROM blood_requests WHERE date_needed >= '$currentDate'";
                        $result = mysqli_query($conn, $query);
                        
                        while($row = mysqli_fetch_assoc($result)){
                            echo "<tr>";
                            echo "<td>". $row['name'] ."</td>";
                            echo "<td>". $row['blood_group'] ."</td>";
                            echo "<td>". $row['quantity'] ."</td>";
                            echo "<td>". $row['date_needed'] ."</td>";
                            echo "<td>". $row['contact'] ."</td>";
                            echo "<td>". $row['hospital_name'] ."</td>";
                            echo "<td>". $row['hospital_unit'] ."</td>";

                            // Check if hospital is in trusted_hospitals table
                            $hospitalName = $row['hospital_name'];
                            $checkQuery = "SELECT location FROM trusted_hospitals WHERE hospital_name = '$hospitalName'";
                            $checkResult = mysqli_query($conn, $checkQuery);
                            $locationRow = mysqli_fetch_assoc($checkResult);

                            if ($locationRow) {
                                $location = $locationRow['location'];
                                echo '<td><a href="' . $location . '" target="_blank">View On Map</a></td>';
                            } 
                            else {
                                echo "<td>Contact for Location</td>";
                            }
                            
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
    <a onclick="history.back();"><i class="fa-solid fa-circle-chevron-left"></i></a>
</body>

</html>