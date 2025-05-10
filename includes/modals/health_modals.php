<!-- Health Modals -->

<!-- Add Health Check Modal -->
<div class="modal fade" id="addHealthCheckModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Health Check</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="process_health.php" method="POST">
                    <div class="form-group mb-3">
                        <label>Select Hive</label>
                        <select name="hiveID" class="form-control hive-select" required>
                            <?php foreach ($hivesData as $hive): ?>
                                <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Check Date</label>
                        <input type="date" name="checkDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Colony Strength (1-10)</label>
                        <input type="number" name="colonyStrength" class="form-control" min="1" max="10" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Queen Present</label>
                        <select name="queenPresent" class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Disease Symptoms</label>
                        <textarea name="diseaseSymptoms" class="form-control" rows="3">None</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Pest Problems</label>
                        <textarea name="pestProblems" class="form-control" rows="3">None</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Health Check</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Health Check Modal -->
<div class="modal fade" id="viewHealthCheckModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Health Check Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="healthCheckDetails">
                    <!-- Content will be dynamically populated -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editHealthCheckBtn">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Health Check Modal -->
<div class="modal fade" id="editHealthCheckModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Health Check</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHealthCheckForm">
                    <input type="hidden" name="healthID">
                    <div class="form-group mb-3">
                        <label>Select Hive</label>
                        <select name="hiveID" class="form-control hive-select" required>
                            <?php foreach ($hivesData as $hive): ?>
                                <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Check Date</label>
                        <input type="date" name="checkDate" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Colony Strength (1-10)</label>
                        <input type="number" name="colonyStrength" class="form-control" min="1" max="10" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Queen Present</label>
                        <select name="queenPresent" class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Disease Symptoms</label>
                        <textarea name="diseaseSymptoms" class="form-control"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Pest Problems</label>
                        <textarea name="pestProblems" class="form-control"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Health Check</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Health History Modal -->
<div class="modal fade" id="healthHistoryModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hive Health History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Select Hive</label>
                            <select id="historyHiveSelect" class="form-control">
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
                            <select id="historyDateRange" class="form-control">
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
                            <label>Health Status</label>
                            <select id="historyHealthStatus" class="form-control">
                                <option value="all">All Status</option>
                                <option value="healthy">Healthy</option>
                                <option value="issues">Issues Detected</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Health Check History</h5>
                            </div>
                            <div class="card-body">
                                <div id="healthHistoryTable">
                                    <!-- Health history table will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Health Trends</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="healthHistoryChart"></canvas>
                                </div>
                                
                                <div class="mt-4">
                                    <h6>Health Summary</h6>
                                    <div id="healthHistorySummary">
                                        <!-- Summary stats will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Health Issues Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div id="healthIssuesTimeline">
                                    <!-- Timeline will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
