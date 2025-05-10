<!-- Reports Section -->
<section id="reports" class="tab-pane">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Reports Generation</h5>
                </div>
                <div class="card-body">
                    <form id="reportsForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reportType">Select Report Type:</label>
                                    <select id="reportType" class="form-control" required>
                                        <option value="">--Select Report--</option>
                                        <option value="honeyProduction">Honey Production Report</option>
                                        <option value="hiveHealth">Hive Health Report</option>
                                        <option value="equipmentUsage">Equipment Usage Report</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="startDate">Start Date:</label>
                                    <input type="date" id="startDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="endDate">End Date:</label>
                                    <input type="date" id="endDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">Generate Report</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="reportResults" class="mt-4">
                        <!-- Report results will be displayed here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Sections -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div id="productionReportSection"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div id="equipmentReportSection"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div id="healthReportSection"></div>
        </div>
    </div>
</section>

<!-- Include jsPDF library for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
