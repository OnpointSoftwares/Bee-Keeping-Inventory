<!-- Production Modals -->

<!-- Add Production Modal -->
<div class="modal fade" id="addProductionModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Production Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="process_production.php" method="POST">
                    <div class="form-group mb-3">
                        <label>Select Hive</label>
                        <select name="hiveID" class="form-control hive-select" required>
                            <?php foreach ($hivesData as $hive): ?>
                                <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harvest Date</label>
                        <input type="date" name="harvestDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quantity (kg)</label>
                        <input type="number" name="quantity" class="form-control" step="0.1" min="0" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Honey Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Wildflower">Wildflower</option>
                            <option value="Acacia">Acacia</option>
                            <option value="Citrus">Citrus</option>
                            <option value="Eucalyptus">Eucalyptus</option>
                            <option value="Mixed">Mixed</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quality</label>
                        <select name="quality" class="form-control" required>
                            <option value="Premium">Premium</option>
                            <option value="Standard">Standard</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Production</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Production Modal -->
<div class="modal fade" id="editProductionModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Production Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductionForm">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="productionID">
                    <div class="form-group mb-3">
                        <label>Select Hive</label>
                        <select name="hiveID" class="form-control hive-select" required>
                            <?php foreach ($hivesData as $hive): ?>
                                <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Harvest Date</label>
                        <input type="date" name="harvestDate" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quantity (kg)</label>
                        <input type="number" name="quantity" class="form-control" step="0.1" min="0" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Honey Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Wildflower">Wildflower</option>
                            <option value="Acacia">Acacia</option>
                            <option value="Citrus">Citrus</option>
                            <option value="Eucalyptus">Eucalyptus</option>
                            <option value="Mixed">Mixed</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quality</label>
                        <select name="quality" class="form-control">
                            <option value="Premium">Premium</option>
                            <option value="Standard">Standard</option>
                            <option value="Economy">Economy</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Production Record</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Production History Modal -->
<div class="modal fade" id="productionHistoryModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Production History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Hive</label>
                            <select id="productionHistoryHiveSelect" class="form-control">
                                <option value="all">All Hives</option>
                                <?php foreach ($hivesData as $hive): ?>
                                    <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date Range</label>
                            <select id="productionHistoryDateRange" class="form-control">
                                <option value="30">Last 30 Days</option>
                                <option value="90">Last 90 Days</option>
                                <option value="180">Last 6 Months</option>
                                <option value="365">Last Year</option>
                                <option value="all" selected>All Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Honey Type</label>
                            <select id="productionHistoryTypeSelect" class="form-control">
                                <option value="all">All Types</option>
                                <?php 
                                $types = [];
                                foreach ($productionData as $production) {
                                    if (!empty($production['type']) && !in_array($production['type'], $types)) {
                                        $types[] = $production['type'];
                                        echo '<option value="' . htmlspecialchars($production['type']) . '">' . htmlspecialchars($production['type']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Production Records</h5>
                            </div>
                            <div class="card-body">
                                <div id="productionHistoryTable">
                                    <!-- Production history table will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Production Trends</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="productionHistoryChart"></canvas>
                                </div>
                                
                                <div class="mt-4">
                                    <h6>Summary</h6>
                                    <div id="productionHistorySummary">
                                        <!-- Summary will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
