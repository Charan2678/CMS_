<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>👨‍🎓</span> Institutional Student Directory
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Search, filter, and manage enrolled student profiles
            </div>
        </div>

        <a href="/students/admission" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.625rem 1.25rem; font-weight: 700;">
            + Admit New Student
        </a>
    </div>

    <!-- Filters & Search Bar — Full Width Grid -->
    <form method="GET" action="/students" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end; background: var(--bg-main); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--border-color); width: 100%;">
        <div>
            <label class="form-label">Search Query</label>
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
            <label class="form-label">Enrollment Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="graduated" <?= $filters['status'] === 'graduated' ? 'selected' : '' ?>>Graduated</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Apply Filters</button>
        </div>
    </form>

    <!-- Student Table — Full Width -->
    <div style="overflow-x: auto; width: 100%;">
        <table class="table">
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Course & Sem</th>
                    <th>Section</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No student records found matching criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $std): ?>
                        <?php 
                            $secName = $std['section_name'] ?? 'A';
                            $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                        ?>
                        <tr>
                            <td style="font-weight: 800; color: var(--accent-color);"><?= e($std['roll_number']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($std['first_name'] . ' ' . $std['last_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($std['department_code'] ?? 'N/A') ?></td>
                            <td style="color: var(--text-secondary);"><?= e($std['course_code'] ?? 'N/A') ?> (Sem <?= e($std['semester_number'] ?? '1') ?>)</td>
                            <td style="font-weight: 600;"><?= e($cleanSec) ?></td>
                            <td>
                                <?php if ($std['status'] === 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php elseif ($std['status'] === 'graduated'): ?>
                                    <span class="badge badge-info">Graduated</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= e($std['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="/students/<?= $std['id'] ?>" class="btn-primary" style="padding: 0.35rem 0.875rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-block;">View 360° Profile &rarr;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls Bar -->
    <?php if (!empty($pagination) && ($pagination['total_pages'] ?? 1) > 1): ?>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                Showing <strong><?= (($pagination['page'] - 1) * $pagination['per_page']) + 1 ?></strong> to <strong><?= min($pagination['total'], $pagination['page'] * $pagination['per_page']) ?></strong> of <strong><?= $pagination['total'] ?></strong> students
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <?php if ($pagination['page'] > 1): ?>
                    <a href="?page=<?= $pagination['page'] - 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="btn btn-secondary btn-sm">« Previous</a>
                <?php endif; ?>

                <span class="btn btn-sm btn-primary" style="pointer-events: none;">Page <?= $pagination['page'] ?> of <?= $pagination['total_pages'] ?></span>

                <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                    <a href="?page=<?= $pagination['page'] + 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="btn btn-secondary btn-sm">Next »</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
