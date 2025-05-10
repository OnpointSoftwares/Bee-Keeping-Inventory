<?php
// Direct script to view equipment details
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/equipment_errors.log');

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedIn'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'config/database.php';
require_once 'utils/database_utils.php';

// Check if ID was provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Get equipment ID
        $equipmentID = $_GET['id'];
        
        // Get equipment details
        $equipment = selectSingleRow(
            "SELECT * FROM equipment WHERE equipmentID = :equipmentID",
            ['equipmentID' => $equipmentID]
        );
        
        if (!$equipment) {
            $_SESSION['message'] = "Equipment not found.";
            $_SESSION['message_type'] = "danger";
            header('Location: index.php?page=equipment');
            exit();
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error viewing equipment: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
        header('Location: index.php?page=equipment');
        exit();
    }
} else {
    // Set error message
    $_SESSION['message'] = "Invalid equipment ID.";
    $_SESSION['message_type'] = "danger";
    header('Location: index.php?page=equipment');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Equipment - Beekeeping Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container my-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Equipment Details</h5>
                <div>
                    <a href="index.php?page=equipment" class="btn btn-secondary btn-sm">Back to List</a>
                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editEquipmentModal">Edit</a>
                    <a href="process_equipment_delete.php?id=<?php echo $equipment['equipmentID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this equipment?')">Delete</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($equipment['name']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($equipment['type']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($equipment['quantity']); ?></p>
                        <p><strong>Condition:</strong> <?php echo htmlspecialchars($equipment['condition_status']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Purchase Date:</strong> <?php echo htmlspecialchars($equipment['purchaseDate']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($equipment['status']); ?></p>
                        <p><strong>Notes:</strong> <?php echo htmlspecialchars($equipment['notes']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Equipment Modal -->
    <div class="modal fade" id="editEquipmentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="process_equipment_edit.php" method="POST">
                        <input type="hidden" name="equipmentID" value="<?php echo $equipment['equipmentID']; ?>">
                        <div class="form-group mb-3">
                            <label>Equipment Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($equipment['name']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Equipment Type</label>
                            <select name="type" class="form-control" required>
                                <option value="Hive Box" <?php echo ($equipment['type'] == 'Hive Box') ? 'selected' : ''; ?>>Hive Box</option>
                                <option value="Frame" <?php echo ($equipment['type'] == 'Frame') ? 'selected' : ''; ?>>Frame</option>
                                <option value="Extractor" <?php echo ($equipment['type'] == 'Extractor') ? 'selected' : ''; ?>>Extractor</option>
                                <option value="Smoker" <?php echo ($equipment['type'] == 'Smoker') ? 'selected' : ''; ?>>Smoker</option>
                                <option value="Protective Gear" <?php echo ($equipment['type'] == 'Protective Gear') ? 'selected' : ''; ?>>Protective Gear</option>
                                <option value="Tools" <?php echo ($equipment['type'] == 'Tools') ? 'selected' : ''; ?>>Tools</option>
                                <option value="Other" <?php echo ($equipment['type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="1" value="<?php echo htmlspecialchars($equipment['quantity']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Condition</label>
                            <select name="condition_status" class="form-control" required>
                                <option value="New" <?php echo ($equipment['condition_status'] == 'New') ? 'selected' : ''; ?>>New</option>
                                <option value="Good" <?php echo ($equipment['condition_status'] == 'Good') ? 'selected' : ''; ?>>Good</option>
                                <option value="Fair" <?php echo ($equipment['condition_status'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                                <option value="Poor" <?php echo ($equipment['condition_status'] == 'Poor') ? 'selected' : ''; ?>>Poor</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Purchase Date</label>
                            <input type="date" name="purchaseDate" class="form-control" value="<?php echo htmlspecialchars($equipment['purchaseDate']); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Active" <?php echo ($equipment['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($equipment['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                <option value="Maintenance" <?php echo ($equipment['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($equipment['notes']); ?></textarea>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Equipment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
