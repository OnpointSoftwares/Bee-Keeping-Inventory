<!-- Hives Section -->
<section id="hives" class="tab-pane">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2>Hive Data</h2>
                <div id="hivesContainer">
                    <?php if (!empty($hivesData)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Hive Number</th>
                                        <th>Location</th>
                                        <th>Date Established</th>
                                        <th>Queen Age</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hivesData as $hive): ?>
                                        <tr data-hive-id="<?php echo $hive['hiveID']; ?>">
                                            <td><?php echo htmlspecialchars($hive['hiveNumber']); ?></td>
                                            <td><?php echo htmlspecialchars($hive['location']); ?></td>
                                            <td><?php echo htmlspecialchars($hive['dateEstablished']); ?></td>
                                            <td><?php echo htmlspecialchars($hive['queenAge']); ?></td>
                                            <td><?php echo htmlspecialchars($hive['status']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-hive-btn" data-hive-id="<?php echo $hive['hiveID']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-hive-btn" data-hive-id="<?php echo $hive['hiveID']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No hives found. Add your first hive to get started.</div>
                    <?php endif; ?>
                </div> 
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Hive Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHiveModal">
                        Add New Hive
                    </button>
                </div>
                <div class="card-body">
                    <!-- Content will be loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</section>
