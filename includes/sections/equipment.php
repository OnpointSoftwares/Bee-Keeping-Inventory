<!-- Equipment Section -->
<section id="equipment" class="tab-pane">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Equipment Inventory</h5>
                    <div>
                        <select id="equipmentTypeFilter" class="form-control d-inline-block w-auto me-2">
                            <option value="">All Types</option>
                            <?php 
                            // Get unique equipment types
                            $types = [];
                            foreach ($equipmentData as $equipment) {
                                if (!in_array($equipment['type'], $types)) {
                                    $types[] = $equipment['type'];
                                    echo '<option value="' . htmlspecialchars($equipment['type']) . '">' . htmlspecialchars($equipment['type']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                            <i class="fas fa-plus"></i> Add Equipment
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="equipmentContainer">
                        <?php if (!empty($equipmentData)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Condition</th>
                                            <th>Purchase Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipmentData as $equipment): ?>
                                            <tr data-equipment-id="<?php echo $equipment['equipmentID']; ?>" data-type="<?php echo htmlspecialchars($equipment['type']); ?>">
                                                <td><?php echo htmlspecialchars($equipment['name']); ?></td>
                                                <td><?php echo htmlspecialchars($equipment['type']); ?></td>
                                                <td><?php echo htmlspecialchars($equipment['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars($equipment['condition_status']); ?></td>
                                                <td><?php echo htmlspecialchars($equipment['purchaseDate']); ?></td>
                                                <td>
                                                    <a href="process_equipment_edit.php?id=<?php echo $equipment['equipmentID']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="process_equipment_delete.php?id=<?php echo $equipment['equipmentID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this equipment?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No equipment found. Add your first equipment item to get started.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Inventory Summary</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Calculate total items
                    $totalItems = 0;
                    foreach ($equipmentData as $equipment) {
                        $totalItems += (int)$equipment['quantity'];
                    }
                    ?>
                    <div class="production-stat mb-4">
                        <h3>Total Items</h3>
                        <p id="totalItems"><?php echo $totalItems; ?></p>
                    </div>
                    <div class="chart-container">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                    <div id="inventoryReportContainer">
                        <?php if (!empty($equipmentSummaryData)): ?>
                            <h5 class="mt-4">Equipment by Type</h5>
                            <ul class="list-group">
                                <?php foreach ($equipmentSummaryData as $summary): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($summary['type']); ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo $summary['totalQuantity']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
