<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Onboard Non-Faculty Staff Member</h2>
        <span class="badge badge-info">Auto Provisions Staff Portal Account</span>
    </div>

    <form method="POST" action="/staff/create">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
            <div class="form-group">
                <label class="form-label" for="employee_id">Staff Code / Employee ID *</label>
                <input type="text" id="employee_id" name="employee_id" class="form-control" required placeholder="e.g. STF-ACC-001">
            </div>

            <div class="form-group">
                <label class="form-label" for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required placeholder="First Name">
            </div>

            <div class="form-group">
                <label class="form-label" for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required placeholder="Last Name">
            </div>

            <div class="form-group">
                <label class="form-label" for="department_type">Department Domain *</label>
                <select id="department_type" name="department_type" class="form-control" required>
                    <option value="accounts">Accounts & Finance</option>
                    <option value="library">Library Services</option>
                    <option value="hostel">Hostel Administration</option>
                    <option value="transport">Transport & Logistics</option>
                    <option value="canteen">Canteen Services</option>
                    <option value="admin">General Administration</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="designation_id">Designation *</label>
                <select id="designation_id" name="designation_id" class="form-control" required>
                    <option value="">-- Select Designation --</option>
                    <?php foreach ($designations as $des): ?>
                        <option value="<?= $des['id'] ?>"><?= e($des['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="staff@college.edu">
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="+91 9876543210">
            </div>

            <div class="form-group">
                <label class="form-label" for="joining_date">Date of Joining</label>
                <input type="date" id="joining_date" name="joining_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem;">Onboard Staff & Create Account</button>
        </div>
    </form>
</div>
