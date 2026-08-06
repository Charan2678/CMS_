<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Onboard Faculty Member</h2>
        <span class="badge badge-info">Auto Provisions Login Account</span>
    </div>

    <form method="POST" action="/faculty/create">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
            <div class="form-group">
                <label class="form-label" for="employee_id">Employee Code / ID *</label>
                <input type="text" id="employee_id" name="employee_id" class="form-control" required placeholder="e.g. FAC-CSE-001">
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
                <label class="form-label" for="department_id">Department *</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                    <?php endforeach; ?>
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
                <label class="form-label" for="email">Official Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="faculty@college.edu">
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="+91 9876543210">
            </div>

            <div class="form-group">
                <label class="form-label" for="qualification">Highest Qualification</label>
                <input type="text" id="qualification" name="qualification" class="form-control" placeholder="e.g. Ph.D. in Computer Science">
            </div>

            <div class="form-group">
                <label class="form-label" for="specialization">Specialization / Domain</label>
                <input type="text" id="specialization" name="specialization" class="form-control" placeholder="e.g. Machine Learning, Networks">
            </div>

            <div class="form-group">
                <label class="form-label" for="experience_years">Teaching Experience (Years)</label>
                <input type="number" step="0.5" id="experience_years" name="experience_years" class="form-control" value="0.0">
            </div>

            <div class="form-group">
                <label class="form-label" for="joining_date">Date of Joining</label>
                <input type="date" id="joining_date" name="joining_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; grid-column: 1 / -1;">
                <input type="checkbox" id="is_hod" name="is_hod" value="1">
                <label for="is_hod" style="font-size: 0.875rem; color: #a5b4fc; cursor: pointer; font-weight: 600;">Assign as Head of Department (HOD)</label>
            </div>
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem;">Onboard Faculty & Create Account</button>
        </div>
    </form>
</div>
