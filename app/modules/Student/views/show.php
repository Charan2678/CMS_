<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">
            Student Profile: <?= e($student['first_name'] . ' ' . $student['last_name']) ?>
        </h1>
        <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
            Roll No: <?= e($student['roll_number']) ?> — Adm No: <?= e($student['admission_number']) ?>
        </div>
    </div>
    <a href="/students" class="btn btn-secondary" style="font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem; text-decoration: none;">
        &larr; Back to Students Directory
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 3fr; gap: 1.5rem;">
    <!-- Sidebar Card -->
    <div class="panel" style="text-align: center;">
        <div style="width: 96px; height: 96px; border-radius: 50%; background: rgba(99, 102, 241, 0.2); color: #a5b4fc; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1rem auto; border: 2px solid var(--accent-color);">
            👨‍🎓
        </div>
        <h2 style="font-size: 1.25rem; font-weight: 700;"><?= e($student['first_name'] . ' ' . $student['last_name']) ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;"><?= e($student['roll_number']) ?></p>

        <?php if ($student['status'] === 'active'): ?>
            <span class="badge badge-success">Active Enrolled</span>
        <?php else: ?>
            <span class="badge badge-danger"><?= e($student['status']) ?></span>
        <?php endif; ?>

        <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1rem; text-align: left; font-size: 0.8125rem;">
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Admission Ref:</span>
                <strong><?= e($student['admission_number']) ?></strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Admission Date:</span>
                <strong><?= e($student['admission_date']) ?></strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Portal Account:</span>
                <?php if (!empty($student['user_account_id'])): ?>
                    <span class="badge badge-info">Username: <?= e($student['username']) ?></span>
                    <form action="/students/<?= $student['id'] ?>/send-credentials" method="POST" style="margin-top: 0.75rem;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                            <span>📧</span> Send Student Login Email
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge badge-warning">No Login Account</span>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 0.75rem; margin-top: 1rem; border-top: 1px dashed var(--border-color); padding-top: 0.75rem;">
                <span style="color: var(--text-secondary); display: block; margin-bottom: 0.35rem;">Parent Portal Account:</span>
                <form action="/students/<?= $student['id'] ?>/provision-parent" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.75rem; background: rgba(37,99,235,0.1); color: var(--accent-color); border: 1px solid var(--accent-color);">
                        <span>👨‍👩‍👧</span> Provision / Reset Parent Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs / Details -->
    <div>
        <!-- Academic Placement Banner -->
        <div class="panel" style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(49, 16, 66, 0.6));">
            <div class="panel-header">
                <h3 class="panel-title">Current Academic Placement</h3>
                <span class="badge badge-info"><?= e($student['academic_year_name'] ?? '2026-2027') ?></span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Department</span>
                    <strong><?= e($student['department_name'] ?? 'N/A') ?> (<?= e($student['department_code'] ?? '') ?>)</strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Course</span>
                    <strong><?= e($student['course_name'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Semester</span>
                    <strong>Semester <?= e($student['semester_number'] ?? '1') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Section / Batch</span>
                    <strong>Section <?= e($student['section_name'] ?? 'A') ?></strong>
                </div>
            </div>
        </div>

        <!-- Personal & Guardian Info -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Personal & Contact Details</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; font-size: 0.875rem;">
                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Date of Birth</span>
                    <strong><?= e($student['date_of_birth']) ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Gender</span>
                    <strong style="text-transform: capitalize;"><?= e($student['gender']) ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Mobile</span>
                    <strong><?= e($student['mobile'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Email</span>
                    <strong><?= e($student['email'] ?? 'N/A') ?></strong>
                </div>

                <div>
                    <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Category</span>
                    <strong style="text-transform: uppercase;"><?= e($student['category']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Guardians -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Guardian Information</h3>
            </div>

            <?php if (empty($student['guardians'])): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">No guardian details registered.</p>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <?php foreach ($student['guardians'] as $g): ?>
                        <div style="background: rgba(15, 23, 42, 0.6); padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color); font-size: 0.875rem;">
                            <div style="font-weight: 700; color: #a5b4fc;"><?= e($g['name']) ?></div>
                            <div style="color: var(--text-secondary); font-size: 0.75rem; text-transform: capitalize; margin-bottom: 0.5rem;"><?= e($g['relationship']) ?></div>
                            <div>Mobile: <?= e($g['mobile'] ?? 'N/A') ?></div>
                            <div>Occupation: <?= e($g['occupation'] ?? 'N/A') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Documents Attachments -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Attached Documents</h3>
            </div>

            <?php if (empty($student['documents'])): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">No document files attached.</p>
            <?php else: ?>
                <ul style="list-style: none;">
                    <?php foreach ($student['documents'] as $doc): ?>
                        <li style="padding: 0.625rem 0; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; font-size: 0.875rem;">
                            <div>
                                📎 <strong><?= e($doc['document_name']) ?></strong>
                                <span style="color: var(--text-secondary); font-size: 0.75rem; text-transform: uppercase; margin-left: 0.5rem;">(<?= e($doc['document_type']) ?>)</span>
                            </div>
                            <a href="<?= e($doc['file_path']) ?>" target="_blank" class="badge badge-info" style="text-decoration: none;">Download File</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
