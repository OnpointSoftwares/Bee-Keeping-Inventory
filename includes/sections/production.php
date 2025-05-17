<!-- Production Section -->
<section id="production" class="tab-pane">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Honey Production</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Production Records</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductionModal">
                        <i class="fas fa-plus"></i> Add Production
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($productionData)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Hive</th>
                                    <th>Harvest Date</th>
                                    <th>Quantity (kg)</th>
                                    <th>Type</th>
                                    <th>Quality</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productionData as $production): 
                                    // Get hive number
                                    $hiveNumber = '';
                                    foreach ($hivesData as $hive) {
                                        if ($hive['hiveID'] == $production['hiveID']) {
                                            $hiveNumber = $hive['hiveNumber'];
                                            break;
                                        }
                                    }
                                ?>
                                <tr data-production-id="<?php echo $production['productionID']; ?>">
                                    <td><?php echo htmlspecialchars($hiveNumber); ?></td>
                                    <td><?php echo htmlspecialchars($production['harvestDate']); ?></td>
                                    <td><?php echo htmlspecialchars($production['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($production['type']); ?></td>
                                    <td><?php echo htmlspecialchars($production['quality'] ?? 'N/A'); ?></td>
                                    
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">No production records found. Add your first production record to get started.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Production Summary</h5>
                </div>
                <div class="card-body">
                    <div class="production-stat mb-3">
                        <h3>Total Production</h3>
                        <p class="fs-2 fw-bold"><?php echo number_format($totalHoney, 1); ?> kg</p>
                    </div>
                    
                    <div class="production-stat mb-3">
                        <h3>Average per Harvest</h3>
                        <?php 
                        $harvestCount = count($productionData);
                        $avgProduction = $harvestCount > 0 ? $totalHoney / $harvestCount : 0;
                        ?>
                        <p class="fs-4"><?php echo number_format($avgProduction, 1); ?> kg</p>
                    </div>
                    
                    <div class="production-stat mb-3">
                        <h3>Total Harvests</h3>
                        <p class="fs-4"><?php echo $harvestCount; ?></p>
                    </div>
                    
                    <?php if (!empty($productionByTypeData)): ?>
                    <h4 class="mt-4">Production by Type</h4>
                    <div class="list-group">
                        <?php foreach ($productionByTypeData as $typeData): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($typeData['type']); ?>
                            <span class="badge bg-primary rounded-pill"><?php echo number_format($typeData['totalQuantity'], 1); ?> kg</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Production Trends</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="productionTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Honey Type Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="honeyTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Production Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Filter by Date Range</label>
                                <select id="productionDateRange" class="form-control">
                                    <option value="month">Last Month</option>
                                    <option value="quarter">Last Quarter</option>
                                    <option value="year" selected>Last Year</option>
                                    <option value="all">All Time</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Filter by Hive</label>
                                <select id="hiveFilter" class="form-control">
                                    <option value="">All Hives</option>
                                    <?php foreach ($hivesData as $hive): ?>
                                    <option value="<?php echo $hive['hiveID']; ?>"><?php echo htmlspecialchars($hive['hiveNumber']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Filter by Honey Type</label>
                                <select id="honeyTypeFilter" class="form-control">
                                    <option value="">All Types</option>
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
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div id="productionAnalyticsResults">
                                <!-- Results will be loaded dynamically -->
                                <div class="alert alert-info">Select filters above to analyze production data.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
