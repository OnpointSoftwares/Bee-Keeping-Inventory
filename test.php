<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production and Health Records</title>
    <script src="js/production.js"></script>
    <script src="js/health.js"></script>
</head>
<body>
    <h1>Add Production Record</h1>
    <form id="addProductionForm" action="http://localhost/inventory-management-system/api/production?action=add" method="POST">
        <label for="hiveID">Hive ID:</label>
        <input type="number" id="hiveID" name="hiveID" required><br>

        <label for="harvestDate">Harvest Date:</label>
        <input type="date" id="harvestDate" name="harvestDate" required><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>

        <label for="type">Type:</label>
        <input type="text" id="type" name="type" required><br>

        <label for="quality">Quality:</label>
        <input type="text" id="quality" name="quality"><br>

        <label for="notes">Notes:</label>
        <textarea id="notes" name="notes"></textarea><br>

        <button type="submit">Add Production</button>
    </form>

    <h1>Add Health Record</h1>
    <form id="addHealthForm">
        <label for="hiveID">Hive ID:</label>
        <input type="number" id="hiveID" name="hiveID" required><br>

        <label for="healthStatus">Health Status:</label>
        <input type="text" id="healthStatus" name="healthStatus" required><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br>

        <label for="notes">Notes:</label>
        <textarea id="notes" name="notes"></textarea><br>

        <button type="submit">Add Health Record</button>
    </form>
</body>
</html>