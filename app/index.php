<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Number Generator</title>
</head>
<body>
    <h1>Phone Number Generator</h1>
    <form action="src/Model/process.php" method="POST">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>
        <br>
        <label for="country_code">Country Code:</label>
        <input type="text" id="country_code" name="country_code" required>
        <br>
        <button type="submit">Generate</button>
    </form>
</body>
</html>