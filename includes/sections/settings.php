<!-- Settings Section -->
<section id="settings" class="tab-pane">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <form id="settingsForm">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Change Password:</label>
                            <input type="password" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="language">Preferred Language:</label>
                            <select id="language" class="form-control">
                                <option value="en">English</option>
                                <option value="sw">Kiswahili</option>
                                <option value="ki">Kikuyu</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
