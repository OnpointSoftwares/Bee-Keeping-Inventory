<!-- Hive Modals -->

<!-- Add Hive Modal -->
<div class="modal fade" id="addHiveModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Hive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHiveForm">
                    <div class="form-group mb-3">
                        <label>Hive Number</label>
                        <input type="text" name="hiveNumber" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Date Established</label>
                        <input type="date" name="dateEstablished" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Queen Age (months)</label>
                        <input type="number" name="queenAge" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Hive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Hive Modal -->
<div class="modal fade" id="editHiveModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Hive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHiveForm">
                    <input type="hidden" name="hiveID">
                    <div class="form-group mb-3">
                        <label>Hive Number</label>
                        <input type="text" name="hiveNumber" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Queen Age (months)</label>
                        <input type="number" name="queenAge" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Hive</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Hive Modal -->
<div class="modal fade" id="viewHiveModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be dynamically populated -->
            </div>
        </div>
    </div>
</div>
