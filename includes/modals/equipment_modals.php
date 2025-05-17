<!-- Equipment Modals -->

<!-- Add Equipment Modal -->
<div class="modal fade" id="addEquipmentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="process_equipment.php" method="POST">
                    <div class="form-group mb-3">
                        <label>Equipment Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Equipment Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Hive Box">Hive Box</option>
                            <option value="Frame">Frame</option>
                            <option value="Extractor">Extractor</option>
                            <option value="Smoker">Smoker</option>
                            <option value="Protective Gear">Protective Gear</option>
                            <option value="Tools">Tools</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Condition</label>
                        <select name="condition_status" class="form-control" required>
                            <option value="New">New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Purchase Date</label>
                        <input type="date" name="purchaseDate" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Equipment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Equipment Modal -->
<div class="modal fade" id="editEquipmentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Equipment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEquipmentForm">
                    <input type="hidden" name="equipmentID">
                    <div class="form-group mb-3">
                        <label>Equipment Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Equipment Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Hive Box">Hive Box</option>
                            <option value="Frame">Frame</option>
                            <option value="Extractor">Extractor</option>
                            <option value="Smoker">Smoker</option>
                            <option value="Protective Gear">Protective Gear</option>
                            <option value="Tools">Tools</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Condition</label>
                        <select name="condition_status" class="form-control" required>
                            <option value="New">New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Purchase Date</label>
                        <input type="date" name="purchaseDate" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Equipment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Equipment Modal -->
<div class="modal fade" id="viewEquipmentModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Equipment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="equipmentDetails">
                    <!-- Content will be dynamically populated -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editEquipmentBtn">Edit</button>
            </div>
        </div>
    </div>
</div>
