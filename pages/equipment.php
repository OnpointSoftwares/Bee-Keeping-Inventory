<?php
// Check if user is logged in
if (!isset($_SESSION['loggedIn'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'config/database.php';
require_once 'utils/database_utils.php';

// Get equipment data
try {
    $db = getDbConnection();
    
    // Get filter type if provided
    $filterType = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Build query based on filter
    $query = "SELECT * FROM equipment";
    $params = [];
    
    if (!empty($filterType)) {
        $query .= " WHERE type = :type";
        $params['type'] = $filterType;
    }
    
    $query .= " ORDER BY name ASC";
    
    // Get equipment data
    $equipmentData = selectQuery($query, $params);
    
    // Get equipment types for filter
    $equipmentTypes = selectQuery("SELECT DISTINCT type FROM equipment ORDER BY type ASC");
} catch (Exception $e) {
    // Log error
    error_log("Error getting equipment data: " . $e->getMessage());
    
    // Set error message
    $_SESSION['message'] = "An error occurred while retrieving equipment data.";
    $_SESSION['message_type'] = "danger";
    
    // Set empty arrays
    $equipmentData = [];
    $equipmentTypes = [];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Equipment Inventory</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="fas fa-plus"></i> Add New Equipment
        </button>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Equipment Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Equipment</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="page" value="equipment">
                        <div class="input-group">
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <?php foreach ($equipmentTypes as $type): ?>
                                    <option value="<?php echo htmlspecialchars($type['type']); ?>" <?php echo ($filterType == $type['type']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['type']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Equipment List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Equipment List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="equipmentTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Condition</th>
                            <th>Purchase Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipmentData)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No equipment found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipmentData as $equipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($equipment['name']); ?></td>
                                    <td><?php echo htmlspecialchars($equipment['type']); ?></td>
                                    <td><?php echo htmlspecialchars($equipment['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($equipment['condition_status']); ?></td>
                                    <td><?php echo htmlspecialchars($equipment['purchaseDate']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($equipment['status'] == 'Active') ? 'success' : (($equipment['status'] == 'Maintenance') ? 'warning' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($equipment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                
                                <!-- Edit Equipment Modal for each item -->
                                <div class="modal fade" id="editEquipmentModal<?php echo $equipment['equipmentID']; ?>">
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
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<?php include 'includes/modals/equipment_modals.php'; ?>
