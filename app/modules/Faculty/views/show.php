<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">
            Faculty Profile: <?= e($faculty['first_name'] . ' ' . $faculty['last_name']) ?>
        </h1>
        <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
            <?= e($faculty['employee_id']) ?> — <?= e($faculty['designation_name']) ?>
        </div>
    </div>
    <a href="/faculty" class="btn btn-secondary" style="font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem; text-decoration: none;">
        &larr; Back to Faculty Directory
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 3fr; gap: 1.5rem;">
    <!-- Sidebar Profile Card -->
    <div class="panel" style="text-align: center;">
        <div style="width: 96px; height: 96px; border-radius: 50%; background: rgba(99, 102, 241, 0.2); color: #a5b4fc; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1rem auto; border: 2px solid var(--accent-color);">
            👨‍🏫
        </div>
        <h2 style="font-size: 1.25rem; font-weight: 700;"><?= e($faculty['first_name'] . ' ' . $faculty['last_name']) ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?= e($faculty['employee_id']) ?></p>
        <span class="badge badge-info" style="margin-bottom: 1rem;"><?= e($faculty['designation_name']) ?></span>

        <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem; text-align: left; font-size: 0.8125rem;">
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Department:</span>
                <strong><?= e($faculty['department_name']) ?> (<?= e($faculty['department_code']) ?>)</strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Joining Date:</span>
                <strong><?= e($faculty['joining_date']) ?></strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Portal Account:</span>
                <?php if (!empty($faculty['user_account_id'])): ?>
                    <span class="badge badge-success">Username: <?= e($faculty['username']) ?></span>
                <?php else: ?>
                    <span class="badge badge-warning">No Account</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Details & Subject Allocations -->
    <div>
        <!-- Profile Details Panel -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Professional & Contact Profile</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Qualification</span>
                    <strong><?= e($faculty['qualification'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Specialization</span>
                    <strong><?= e($faculty['specialization'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Experience</span>
                    <strong><?= e($faculty['experience_years']) ?> Years</strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Email</span>
                    <strong><?= e($faculty['email'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Mobile</span>
                    <strong><?= e($faculty['mobile'] ?? 'N/A') ?></strong>
                </div>
            </div>
        </div>

        <!-- Assigned Teaching Subjects & Sections -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Assigned Teaching Load</h3>
                <a href="/faculty/assign-subject" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.375rem 0.875rem; font-size: 0.8125rem;">+ Assign Subject</a>
            </div>

            <?php if (empty($faculty['assignments'])): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">No teaching subjects currently assigned to this faculty member.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem;">Academic Year</th>
                            <th style="padding: 0.75rem;">Subject Code & Name</th>
                            <th style="padding: 0.75rem;">Course / Sem</th>
                            <th style="padding: 0.75rem;">Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faculty['assignments'] as $asg): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($asg['academic_year_name']) ?></td>
                                <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;">
                                    <?= e($asg['subject_code']) ?> — <?= e($asg['subject_name']) ?>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($asg['course_code']) ?> (Sem <?= e($asg['semester_number']) ?>)</td>
                                <td style="padding: 0.75rem; font-weight: 700;">Section <?= e($asg['section_name']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
