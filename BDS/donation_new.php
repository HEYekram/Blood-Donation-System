<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "370_blood_bank");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sufferedProblem = $_POST['hasSuffered'] ?? '';
    $hasDisease = $_POST['hasDisease'] ?? '';
    $isSmoker = $_POST['isSmoker'] ?? '';
    $donationDate = $_POST['donationDate'] ?? '';
    $hospital = $_POST['approverHospital'] ?? '';

    // Deny if medical history is bad
    if ($sufferedProblem === 'YES' || $hasDisease === 'YES' || $isSmoker === 'YES') {
        echo "<script>
            alert('Denied due to medical history');
            window.location.href = 'user_web_view.php';
        </script>";
        exit();
    }

    // Check if hospital is trusted
    $query = "SELECT hospital_name FROM trusted_hospitals WHERE hospital_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $hospital);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>
            alert('Approval is not from our trusted hospital.');
            window.history.back();
        </script>";
        exit();
    }

    // Proceed to insert if user is logged in
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $formattedDonationDate = date('Y-m-d', strtotime($donationDate));

        // Check if user is already a donor
        $checkQuery = "SELECT username FROM donor_list WHERE username = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>
                alert('You are already a donor.');
                window.location.href = 'user_web_view.php';
            </script>";
            exit();
        }

        // Insert new donor
        $insertQuery = "INSERT INTO donor_list (username, previous_donation, approver_hospital) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, $formattedDonationDate, $hospital);

        if ($stmt->execute()) {
            echo "<script>
                alert('You have been added as a donor. Thank you!');
                window.location.href = 'user_web_view.php';
            </script>";
            exit();
        } else {
            echo "Error inserting data: " . $stmt->error;
        }
    } else {
        echo "<script>alert('Session username not set');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Donation Form</title>
    <link rel="stylesheet" href="donation_form.css">
</head>
<body>
    <div class="container">
        <div class="donation_head">
            <img src="formbanner.jpg" alt="">
            <div class="donation_head_info">
                <h1 class="blood">Blood Donation Form</h1>
                <p>Confidential - Please answer the following questions correctly. This helps protect both you and the recipient.</p>
            </div>
        </div>
        <form action="" method="post">
            <div>
                <label>Did you suffer any problem during previous donation?</label>
                <div style="display: flex; gap:10px">
                    <label for="d1">Yes</label>
                    <input type="radio" name="hasSuffered" id="d1" value="YES" required>
                    <label for="d2">No</label>
                    <input type="radio" name="hasSuffered" id="d2" value="NO">
                </div>
            </div>

            <div>
                <label><br>Do you have any major disease? (cancer, TB, hepatitis, HIV, etc.)</label>
                <div style="display: flex; gap:10px">
                    <label for="c1">Yes</label>
                    <input type="radio" name="hasDisease" id="c1" value="YES" required>
                    <label for="c2">No</label>
                    <input type="radio" name="hasDisease" id="c2" value="NO">
                </div>
            </div>

            <label for=""><br>Are you a chain smoker?</label>
            <div style="display: flex; gap:10px">
                <label for="b1">Yes</label>
                <input type="radio" name="isSmoker" id="b1" value="YES" required>
                <label for="b2">No</label>
                <input type="radio" name="isSmoker" id="b2" value="NO">
            </div>

            <div>
                <label for="donationDate"><br>Previous donation date:</label>
                <input type="date" id="donationDate" name="donationDate" required>
            </div>

            <div>
                <label for="approverHospital">Approver hospital (your clinical clearance issuer):</label>
                <input type="text" id="approverHospital" name="approverHospital" required>
            </div>

            <input type="submit" value="Submit form">
            <p class="approval"><a href="trusted_hospitals.php">Don’t have approval or not from a trusted hospital? (Click here)</a></p>
        </form>
    </div>
</body>
</html>