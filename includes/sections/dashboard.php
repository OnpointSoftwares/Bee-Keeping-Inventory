<!-- Dashboard Section -->
<section id="dashboard" class="tab-pane active">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Total Production</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductionModal">
                        <i class="fas fa-plus"></i> Add Production
                    </button>
                </div>
                <div class="production-container">
                    <div id="productionContainer">
                        <?php
                            foreach ($productionData as $production) {
                                echo '<div class="production-item">';
                                echo '<div class="production-date">' . $production['harvestDate'] . '</div>';
                                echo '<div class="production-amount">' . $production['quantity'] . ' kg</div>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="productionChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Honey Production Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="production-stat">
                                <h3>Total Honey</h3>
                                <p class="fs-2 fw-bold"><?php echo number_format($totalHoney, 1); ?> kg</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="production-stat">
                                <h3>Total Harvests</h3>
                                <p class="fs-2 fw-bold"><?php echo count($productionData); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($productionByTypeData)): ?>
                    <div class="mt-4">
                        <h4>Production by Type</h4>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Honey Type</th>
                                        <th>Total Quantity</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productionByTypeData as $typeData): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($typeData['type']); ?></td>
                                        <td><?php echo number_format($typeData['totalQuantity'], 1); ?> kg</td>
                                        <td>
                                            <?php 
                                            $percentage = ($totalHoney > 0) ? ($typeData['totalQuantity'] / $totalHoney) * 100 : 0;
                                            echo number_format($percentage, 1) . '%'; 
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
                    <h5 class="mb-0">Equipment Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="equipmentChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Hive Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="production-stat">
                                <h3>Total Hives</h3>
                                <p class="fs-2 fw-bold"><?php echo count($hivesData); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="production-stat">
                                <h3>Active Hives</h3>
                                <?php
                                $activeHives = array_filter($hivesData, function($hive) {
                                    return $hive['status'] === 'Active';
                                });
                                ?>
                                <p class="fs-2 fw-bold"><?php echo count($activeHives); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
