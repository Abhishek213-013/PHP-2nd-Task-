<?php
include('../includes/db.php');
include('../includes/auth.php');

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $weight = trim($_POST['weight']);
    $height = trim($_POST['height']);


    if (empty($name) || empty($weight) || empty($height)) {
        $error = "Please fill in all fields.";
    } elseif (!is_numeric($weight) || !is_numeric($height) || $height <= 0) {
        $error = "Please enter valid numbers for weight and height. Height must be greater than zero.";
    } else {

        $bmi = $weight / ($height * $height);

        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO BMIUsers (user_id, name, weight, height, bmi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdds", $user_id, $name, $weight, $height, $bmi);
        if ($stmt->execute()) {
            $success = "BMI information stored successfully.";
        } else {
            $error = "Error storing BMI information. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI Calculator</title>
    <link href="../css/styles.css" rel="stylesheet">
    <script>
        function validateInput() {
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            let weightMsg = '';
            let heightMsg = '';

            if (isNaN(weight) || weight <= 0) {
                weightMsg = 'Weight must be a positive number.';
            }

            if (isNaN(height) || height <= 0) {
                heightMsg = 'Height must be a positive number greater than zero.';
            }

            document.getElementById('weight-feedback').innerText = weightMsg;
            document.getElementById('height-feedback').innerText = heightMsg;

            return weightMsg === '' && heightMsg === '';
        }

        function calculateBMI() {
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);

            if (!isNaN(weight) && weight > 0 && !isNaN(height) && height > 0) {
                const bmi = weight / (height * height);
                document.getElementById('bmi-result').innerText = 'Your BMI is ' + bmi.toFixed(2);
            } else {
                document.getElementById('bmi-result').innerText = '';
            }
        }
    </script>
</head>
<body>
    <h1>BMI Calculator</h1>
    <form method="post" action="" onsubmit="return validateInput()">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
        <br>
        <label for="weight">Weight (kg):</label>
        <input type="text" id="weight" name="weight" oninput="calculateBMI()">
        <span id="weight-feedback" style="color: red;"></span>
        <br>
        <label for="height">Height (m):</label>
        <input type="text" id="height" name="height" oninput="calculateBMI()">
        <span id="height-feedback" style="color: red;"></span>
        <br>
        <input type="submit" value="Calculate">
    </form>
    <p id="bmi-result"></p>
    <?php
    if (!empty($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    if (!empty($success)) {
        echo "<p style='color: green;'>$success</p>";
    }
    ?>
</body>
</html>
