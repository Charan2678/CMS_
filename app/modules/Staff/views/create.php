<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('briefcase', 'icon-md') ?> Onboard Non-Faculty Staff Member
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Create staff profile for Accounts, Library, Hostel, Transport, or Canteen and provision portal access
            </div>
        </div>
        <span class="badge badge-info" style="font-size: 0.75rem;">Auto Provisions Staff Portal Account</span>
    </div>

    <form method="POST" action="/staff/create">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
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
                    <option value="accounts">Accounts &amp; Finance</option>
                    <option value="library">Library Services</option>
                    <option value="hostel">Hostel Administration</option>
                    <option value="transport">Transport &amp; Logistics</option>
                    <option value="canteen">Canteen Services</option>
                    <option value="admin">General Administration</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="designation_id">Designation *</label>
                <select id="designation_id" name="designation_id" class="form-control" required>
                    <option value="">-- Select Designation --</option>
                    <?php foreach ($designations as $des): ?>
                        <option value="<?= $des['id'] ?>" data-dept="<?= e($des['department_type']) ?>"><?= e($des['name']) ?></option>
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

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem; font-weight: 700;"><?= icon('user-check', 'icon-sm') ?> Onboard Staff &amp; Create Account</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptSelect = document.getElementById('department_type');
    const desigSelect = document.getElementById('designation_id');
    const allOptions = Array.from(desigSelect.options);

    function filterDesignations() {
        const selectedDept = deptSelect.value;
        const currentVal = desigSelect.value;

        desigSelect.innerHTML = '';

        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = '-- Select Designation --';
        desigSelect.appendChild(defaultOpt);

        allOptions.forEach(opt => {
            if (opt.value && opt.dataset.dept === selectedDept) {
                const newOpt = opt.cloneNode(true);
                desigSelect.appendChild(newOpt);
            }
        });

        if (Array.from(desigSelect.options).some(o => o.value === currentVal)) {
            desigSelect.value = currentVal;
        }
    }

    deptSelect.addEventListener('change', filterDesignations);
    filterDesignations();
});
</script>
