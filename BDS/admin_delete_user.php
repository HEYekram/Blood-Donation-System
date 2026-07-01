<?php
    $conn = mysqli_connect("localhost", "root", "", "370_blood_bank");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = mysqli_real_escape_string($conn, $_POST["username"]);

        $deleteQuery = "DELETE FROM user_list WHERE username = '$username'";
        if (mysqli_query($conn, $deleteQuery)) {
            echo "Success";
        } else {
            echo "Error";
        }
    }

    mysqli_close($conn);
?>