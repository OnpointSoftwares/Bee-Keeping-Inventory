<!-- Hive Health Section -->
<section id="health" class="tab-pane">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Hive Health Monitoring</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Health Check Records</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHealthCheckModal">
                        <i class="fas fa-plus"></i> Add Health Check
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($healthCheckData)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Hive</th>
                                    <th>Check Date</th>
                                    <th>Colony Strength</th>
                                    <th>Queen Present</th>
                                    <th>Disease Symptoms</th>
                                    <th>Pest Problems</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($healthCheckData as $healthCheck): ?>
                                    <tr data-health-id="<?php echo $healthCheck['checkID']; ?>">
                                        <td><?php echo htmlspecialchars($healthCheck['hiveNumber']); ?></td>
                                        <td><?php echo htmlspecialchars($healthCheck['checkDate']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <?php 
                                                $strength = (int)$healthCheck['colonyStrength'];
                                                $colorClass = 'bg-danger';
                                                if ($strength >= 7) {
                                                    $colorClass = 'bg-success';
                                                } elseif ($strength >= 4) {
                                                    $colorClass = 'bg-warning';
                                                }
                                                ?>
                                                <div class="progress-bar <?php echo $colorClass; ?>" role="progressbar" 
                                                    style="width: <?php echo $strength * 10; ?>%" 
                                                    aria-valuenow="<?php echo $strength; ?>" aria-valuemin="0" aria-valuemax="10">
                                                    <?php echo $strength; ?>/10
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($healthCheck['queenPresent'] == 1): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($healthCheck['diseaseSymptoms'])): ?>
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($healthCheck['diseaseSymptoms']); ?>">
                                                    <i class="fas fa-virus"></i> Yes
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($healthCheck['pestProblems'])): ?>
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($healthCheck['pestProblems']); ?>">
                                                    <i class="fas fa-bug"></i> Yes
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">None</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">No health checks found. Add your first health check to get started.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Health Summary</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($healthSummary)): ?>
                    <div class="health-stats">
                        <div class="health-stat mb-3">
                            <h3>Total Health Checks</h3>
                            <p class="fs-2 fw-bold"><?php echo $healthSummary['totalChecks']; ?></p>
                        </div>
                        
                        <div class="health-stat mb-3">
                            <h3>Average Colony Strength</h3>
                            <div class="progress" style="height: 25px;">
                                <?php 
                                $avgStrength = round($healthSummary['avgStrength'], 1);
                                $colorClass = 'bg-danger';
                                if ($avgStrength >= 7) {
                                    $colorClass = 'bg-success';
                                } elseif ($avgStrength >= 4) {
                                    $colorClass = 'bg-warning';
                                }
                                ?>
                                <div class="progress-bar <?php echo $colorClass; ?>" role="progressbar" 
                                    style="width: <?php echo $avgStrength * 10; ?>%" 
                                    aria-valuenow="<?php echo $avgStrength; ?>" aria-valuemin="0" aria-valuemax="10">
                                    <?php echo $avgStrength; ?>/10
                                </div>
                            </div>
                        </div>
                        
                        <div class="health-stat mb-3">
                            <h3>Queen Present Rate</h3>
                            <?php 
                            $queenRate = $healthSummary['totalChecks'] > 0 
                                ? round(($healthSummary['queenPresentCount'] / $healthSummary['totalChecks']) * 100, 1) 
                                : 0;
                            ?>
                            <p class="fs-4"><?php echo $queenRate; ?>%</p>
                        </div>
                        
                        <div class="health-stat mb-3">
                            <h3>Health Issues</h3>
                            <div class="d-flex justify-content-between">
                                <span>Disease: <?php echo $healthSummary['diseaseCount']; ?></span>
                                <span>Pests: <?php echo $healthSummary['pestCount']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">No health data available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Latest Health Status by Hive</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($latestHealthChecks)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Hive</th>
                                    <th>Last Check Date</th>
                                    <th>Colony Strength</th>
                                    <th>Queen Present</th>
                                    <th>Health Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($latestHealthChecks as $check): 
                                    // Determine health status
                                    $healthStatus = 'Healthy';
                                    $statusClass = 'bg-success';
                                    
                                    if (!empty($check['diseaseSymptoms']) || !empty($check['pestProblems']) || $check['queenPresent'] == 0 || $check['colonyStrength'] < 5) {
                                        $healthStatus = 'Issues Detected';
                                        $statusClass = 'bg-warning';
                                        
                                        if ($check['colonyStrength'] < 3 || ($check['queenPresent'] == 0 && !empty($check['diseaseSymptoms']))) {
                                            $healthStatus = 'Critical';
                                            $statusClass = 'bg-danger';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($check['hiveNumber']); ?></td>
                                    <td><?php echo htmlspecialchars($check['checkDate']); ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <?php 
                                            $strength = (int)$check['colonyStrength'];
                                            $colorClass = 'bg-danger';
                                            if ($strength >= 7) {
                                                $colorClass = 'bg-success';
                                            } elseif ($strength >= 4) {
                                                $colorClass = 'bg-warning';
                                            }
                                            ?>
                                            <div class="progress-bar <?php echo $colorClass; ?>" role="progressbar" 
                                                style="width: <?php echo $strength * 10; ?>%" 
                                                aria-valuenow="<?php echo $strength; ?>" aria-valuemin="0" aria-valuemax="10">
                                                <?php echo $strength; ?>/10
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($check['queenPresent'] == 1): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?php echo $statusClass; ?>"><?php echo $healthStatus; ?></span></td>
                                    <td>
                                        <?php if (!empty($check['notes'])): ?>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($check['notes']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <div class="alert alert-info">No health check data available for hives.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add New Health Check</h5>
                </div>
                <div class="card-body">
                    <form id="addHealthCheckForm">
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
                        <button type="submit" class="btn btn-primary">Add Health Check</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Colony Strength Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="colonyStrengthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
