<div class="panel">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">🎫 Exam Eligibility & Hall Ticket Control Center</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Cross-check section-wise attendance (>=75%) & fee dues balance (== ₹0) before hall ticket issuance.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="badge badge-danger" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ⚠️ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="badge badge-success" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ✓ <?= e($success) ?>
        </div>
    <?php endif; ?>

    <!-- Filter Form -->
    <form method="GET" action="/admit-cards/manage" style="display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 1rem; margin-bottom: 2rem; background: rgba(255,255,255,0.03); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color);">
        <div>
            <label class="form-label" for="academic_year_id">Academic Year *</label>
            <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                <option value="">-- Select Academic Year --</option>
                <?php foreach ($academicYears as $ay): ?>
                    <option value="<?= $ay['id'] ?>" <?= ($academicYearId == $ay['id']) ? 'selected' : '' ?>>
                        <?= e($ay['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label" for="section_id">Section *</label>
            <select name="section_id" id="section_id" class="form-control" required>
                <option value="">-- Select Section --</option>
                <?php foreach ($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= ($sectionId == $sec['id']) ? 'selected' : '' ?>>
                        <?= e($sec['name']) ?> (<?= e($sec['semester_name'] ?? 'Sem ' . $sec['semester_id']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                🔍 Load Eligibility Report
            </button>
        </div>
    </form>

    <?php if ($sectionId > 0): ?>
        <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Section Eligibility Roster (<?= count($report) ?> Students)</h2>
        
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Attendance %</th>
                        <th>Fee Dues</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($report)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No students found in selected section placement.</td></tr>
                    <?php else: ?>
                        <?php foreach ($report as $item): ?>
                            <?php 
                                $st = $item['student']; 
                                $el = $item['eligibility'];
                                $status = $el['status'];
                            ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: bold;"><?= e($st['roll_number']) ?></td>
                                <td><strong><?= e($st['first_name'] . ' ' . $st['last_name']) ?></strong></td>
                                <td>
                                    <?php if ($el['attendance_pct'] >= 75.0): ?>
                                        <span class="badge badge-success"><?= number_format($el['attendance_pct'], 1) ?>%</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?= number_format($el['attendance_pct'], 1) ?>% Shortage</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($el['pending_dues'] <= 0): ?>
                                        <span class="badge badge-success">₹0 Clear</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">₹<?= number_format($el['pending_dues'], 2) ?> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($status === 'eligible'): ?>
                                        <span class="badge badge-success">✓ Eligible</span>
                                    <?php elseif ($status === 'condoned'): ?>
                                        <span class="badge badge-info">✓ Condoned</span>
                                    <?php elseif ($status === 'blocked_attendance'): ?>
                                        <span class="badge badge-danger">🛑 Blocked (Attendance)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">🛑 Blocked (Fee Dues)</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <?php if ($el['is_eligible']): ?>
                                        <a href="/admit-card?student_id=<?= $st['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">
                                            📄 View Admit Card
                                        </a>
                                    <?php elseif ($status === 'blocked_attendance'): ?>
                                        <form method="POST" action="/admit-cards/manage" style="display: inline-block;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="student_id" value="<?= $st['id'] ?>">
                                            <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                                            <input type="hidden" name="semester_id" value="<?= $item['semester_id'] ?>">
                                            <button type="submit" onclick="return confirm('Grant attendance condonation for <?= e($st['first_name']) ?>?')" class="btn btn-sm btn-warning">
                                                ⚖️ Condone Shortage
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="/fee/payments?student_id=<?= $st['id'] ?>" class="btn btn-sm btn-primary">
                                            💳 Collect Dues
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
