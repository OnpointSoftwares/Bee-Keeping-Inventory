<!-- Health History Section -->
<section id="health_history" class="tab-pane">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Health History Analytics</h2>
        </div>
    </div>
    
    <!-- Filters Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="form-group">
                <label>Select Hive</label>
                <select id="healthHistoryHiveFilter" class="form-control">
                    <option value="all">All Hives</option>
                    <?php foreach ($hivesData as $hive): ?>
                        <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Date Range</label>
                <select id="healthHistoryDateFilter" class="form-control">
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="180">Last 6 Months</option>
                    <option value="365">Last Year</option>
                    <option value="all" selected>All Time</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Health Status</label>
                <select id="healthHistoryStatusFilter" class="form-control">
                    <option value="all">All Status</option>
                    <option value="healthy">Healthy</option>
                    <option value="issues">Issues Detected</option>
                    <option value="critical">Critical</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Issue Type</label>
                <select id="healthHistoryIssueFilter" class="form-control">
                    <option value="all">All Issues</option>
                    <option value="disease">Disease Only</option>
                    <option value="pest">Pest Only</option>
                    <option value="queen">Queen Issues Only</option>
                    <option value="strength">Low Strength Only</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Health History Dashboard -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Health Check History</h5>
                    <button class="btn btn-primary" id="exportHealthHistoryBtn">
                        <i class="fas fa-download"></i> Export Data
                    </button>
                </div>
                <div class="card-body">
                    <div id="healthHistoryTableContainer">
                        <?php if (!empty($healthCheckData)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="healthHistoryTable">
                                <thead>
                                    <tr>
                                        <th>Hive</th>
                                        <th>Check Date</th>
                                        <th>Colony Strength</th>
                                        <th>Queen Present</th>
                                        <th>Health Status</th>
                                        <th>Issues</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($healthCheckData as $healthCheck): 
                                        // Determine health status
                                        $healthStatus = 'Healthy';
                                        $statusClass = 'bg-success';
                                        $issues = [];
                                        
                                        if (!empty($healthCheck['diseaseSymptoms'])) {
                                            $issues[] = 'Disease';
                                        }
                                        
                                        if (!empty($healthCheck['pestProblems'])) {
                                            $issues[] = 'Pests';
                                        }
                                        
                                        if ($healthCheck['queenPresent'] == 0) {
                                            $issues[] = 'No Queen';
                                        }
                                        
                                        if ($healthCheck['colonyStrength'] < 5) {
                                            $issues[] = 'Low Strength';
                                        }
                                        
                                        if (!empty($issues)) {
                                            $healthStatus = 'Issues Detected';
                                            $statusClass = 'bg-warning';
                                            
                                            if ($healthCheck['colonyStrength'] < 3 || ($healthCheck['queenPresent'] == 0 && !empty($healthCheck['diseaseSymptoms']))) {
                                                $healthStatus = 'Critical';
                                                $statusClass = 'bg-danger';
                                            }
                                        }
                                    ?>
                                    <tr data-health-id="<?php echo $healthCheck['checkID']; ?>" 
                                        data-hive-id="<?php echo $healthCheck['hiveID']; ?>"
                                        data-status="<?php echo strtolower($healthStatus); ?>"
                                        data-issues="<?php echo strtolower(implode(',', $issues)); ?>">
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
                                        <td><span class="badge <?php echo $statusClass; ?>"><?php echo $healthStatus; ?></span></td>
                                        <td>
                                            <?php if (!empty($issues)): ?>
                                                <?php foreach ($issues as $issue): ?>
                                                    <span class="badge bg-secondary me-1"><?php echo $issue; ?></span>
                                                <?php endforeach; ?>
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
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Health Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Health Status Distribution</h6>
                        <div class="chart-container">
                            <canvas id="healthStatusChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Colony Strength Trend</h6>
                        <div class="chart-container">
                            <canvas id="strengthTrendChart"></canvas>
                        </div>
                    </div>
                    
                    <div>
                        <h6>Health Issues Breakdown</h6>
                        <div class="chart-container">
                            <canvas id="issuesBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Health Comparison and Timeline -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hive Health Comparison</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="hiveComparisonChart"></canvas>
                    </div>
                    <div class="mt-3" id="hiveComparisonLegend"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Health Issues Timeline</h5>
                </div>
                <div class="card-body">
                    <div id="healthTimelineContainer">
                        <!-- Timeline will be populated dynamically -->
                        <?php if (!empty($healthTimelineData)): ?>
                            <ul class="timeline">
                                <?php foreach ($healthTimelineData as $timelineItem): ?>
                                <li class="timeline-item">
                                    <div class="timeline-marker <?php echo $timelineItem['statusClass']; ?>"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">
                                            Hive #<?php echo htmlspecialchars($timelineItem['hiveNumber']); ?> - 
                                            <?php echo htmlspecialchars($timelineItem['checkDate']); ?>
                                        </h6>
                                        <p><?php echo htmlspecialchars($timelineItem['issueDescription']); ?></p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-info">No health issues found in the timeline.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Health Analysis -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Health Insights</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h6>Average Colony Strength</h6>
                                    <?php 
                                    $avgStrength = 0;
                                    if (!empty($healthSummary) && isset($healthSummary['avgStrength'])) {
                                        $avgStrength = is_numeric($healthSummary['avgStrength']) ? 
                                            round((float)$healthSummary['avgStrength'], 1) : 0;
                                    }
                                    $strengthClass = 'text-danger';
                                    if ($avgStrength >= 7) {
                                        $strengthClass = 'text-success';
                                    } elseif ($avgStrength >= 4) {
                                        $strengthClass = 'text-warning';
                                    }
                                    ?>
                                    <p class="display-4 <?php echo $strengthClass; ?>"><?php echo $avgStrength; ?></p>
                                    <p class="text-muted">out of 10</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h6>Queen Present Rate</h6>
                                    <?php 
                                    $queenRate = !empty($healthSummary) && $healthSummary['totalChecks'] > 0 
                                        ? round(($healthSummary['queenPresentCount'] / $healthSummary['totalChecks']) * 100, 1) 
                                        : 0;
                                    $queenClass = 'text-danger';
                                    if ($queenRate >= 90) {
                                        $queenClass = 'text-success';
                                    } elseif ($queenRate >= 70) {
                                        $queenClass = 'text-warning';
                                    }
                                    ?>
                                    <p class="display-4 <?php echo $queenClass; ?>"><?php echo $queenRate; ?>%</p>
                                    <p class="text-muted">of health checks</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h6>Health Issue Rate</h6>
                                    <?php 
                                    $issueCount = !empty($healthSummary) ? ($healthSummary['diseaseCount'] + $healthSummary['pestCount']) : 0;
                                    $issueRate = !empty($healthSummary) && $healthSummary['totalChecks'] > 0 
                                        ? round(($issueCount / $healthSummary['totalChecks']) * 100, 1) 
                                        : 0;
                                    $issueClass = 'text-success';
                                    if ($issueRate >= 30) {
                                        $issueClass = 'text-danger';
                                    } elseif ($issueRate >= 10) {
                                        $issueClass = 'text-warning';
                                    }
                                    ?>
                                    <p class="display-4 <?php echo $issueClass; ?>"><?php echo $issueRate; ?>%</p>
                                    <p class="text-muted">of health checks</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Health Recommendations</h5>
                            <div id="healthRecommendations">
                                <?php if (!empty($healthRecommendations)): ?>
                                    <div class="list-group">
                                        <?php foreach ($healthRecommendations as $recommendation): ?>
                                        <div class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($recommendation['title']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($recommendation['priority']); ?></small>
                                            </div>
                                            <p class="mb-1"><?php echo htmlspecialchars($recommendation['description']); ?></p>
                                            <?php if (!empty($recommendation['hiveNumber'])): ?>
                                            <small class="text-muted">For Hive #<?php echo htmlspecialchars($recommendation['hiveNumber']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No specific recommendations at this time. Continue regular health checks.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
