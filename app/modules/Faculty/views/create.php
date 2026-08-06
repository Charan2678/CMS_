<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>👨‍🏫</span> Onboard Faculty Member
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Create teaching staff profile and auto-provision faculty portal login
            </div>
        </div>
        <span class="badge badge-info" style="font-size: 0.75rem;">Auto Provisions Login Account</span>
    </div>

    <form method="POST" action="/faculty/create">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 1.5rem;">
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
                <label for="is_hod" style="font-size: 0.875rem; color: var(--accent-color); cursor: pointer; font-weight: 700;">Assign as Head of Department (HOD)</label>
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 3rem; font-weight: 700;">Onboard Faculty & Create Account</button>
        </div>
    </form>
</div>
