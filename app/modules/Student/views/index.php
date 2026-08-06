<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Student Directory</h2>
        <a href="/students/admission" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem;">+ Admit New Student</a>
    </div>

    <!-- Filters & Search Bar -->
    <form method="GET" action="/students" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name, Roll No, Email..." value="<?= e($filters['search']) ?>">
        </div>

        <div>
            <label class="form-label">Department</label>
            <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $filters['department_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Course</label>
            <select name="course_id" class="form-control">
                <option value="">All Courses</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filters['course_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="graduated" <?= $filters['status'] === 'graduated' ? 'selected' : '' ?>>Graduated</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Filter</button>
        </div>
    </form>

    <!-- Student Table -->
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Roll No</th>
                <th style="padding: 0.75rem;">Student Name</th>
                <th style="padding: 0.75rem;">Department</th>
                <th style="padding: 0.75rem;">Course & Sem</th>
                <th style="padding: 0.75rem;">Section</th>
                <th style="padding: 0.75rem;">Status</th>
                <th style="padding: 0.75rem;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No student records found matching criteria.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $std): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($std['roll_number']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600;"><?= e($std['first_name'] . ' ' . $std['last_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($std['department_code'] ?? 'N/A') ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($std['course_code'] ?? 'N/A') ?> (Sem <?= e($std['semester_number'] ?? '1') ?>)</td>
                        <td style="padding: 0.75rem;">Sec <?= e($std['section_name'] ?? 'A') ?></td>
                        <td style="padding: 0.75rem;">
                            <?php if ($std['status'] === 'active'): ?>
                                <span class="badge badge-success">Active</span>
                            <?php elseif ($std['status'] === 'graduated'): ?>
                                <span class="badge badge-info">Graduated</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><?= e($std['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem;">
                            <a href="/students/<?= $std['id'] ?>" style="color: #a5b4fc; text-decoration: none; font-weight: 500;">View Profile →</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
