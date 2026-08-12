<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\portal.php
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">💼 Student Placement Portal</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">View job offers, check campus recruitment eligibility criteria, and submit applications directly.</p>
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

    <div class="page-split">
        <!-- Left Side: Student eligibility status -->
        <div class="card" style="align-self: flex-start;">
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🎓</span> My Placement Profile
            </h2>
            
            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary); font-size: 0.8125rem;">Cumulative CGPA</span>
                    <strong style="color: var(--accent-color); font-size: 1rem;"><?= number_format($cgpa, 2) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary); font-size: 0.8125rem;">Active Backlogs</span>
                    <strong style="<?= $backlogs > 0 ? 'color: var(--danger);' : 'color: var(--success);' ?> font-size: 1rem;"><?= (int)$backlogs ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary); font-size: 0.8125rem;">Overall Attendance</span>
                    <strong style="<?= $attendancePct < 75.0 ? 'color: var(--danger);' : 'color: var(--success);' ?> font-size: 1rem;"><?= number_format($attendancePct, 1) ?>%</strong>
                </div>
            </div>

            <div class="alert" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1rem; border-radius: 6px; font-size: 0.8125rem; color: var(--text-primary);">
                💡 <strong>Notice</strong>: The system evaluates eligibility criteria in real-time using your published grade sheet records and active attendance logs. 
            </div>
        </div>

        <!-- Right Side: Active Company Drives -->
        <div>
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">Campus Job Openings & Status</h2>
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <?php if (empty($drives)): ?>
                    <div class="card" style="text-align: center; color: var(--text-secondary); padding: 3rem;">
                        📭 No active recruitment drives available at this time.
                    </div>
                <?php else: ?>
                    <?php foreach ($drives as $drive): ?>
                        <?php 
                        $driveId = (int)$drive['id'];
                        $isCgpaOk = ($cgpa >= (float)$drive['eligibility_cgpa']);
                        $isBacklogsOk = ($backlogs <= (int)$drive['max_backlogs']);
                        $isAttendanceOk = ($attendancePct >= 75.0);
                        $isEligible = ($isCgpaOk && $isBacklogsOk && $isAttendanceOk);
                        $hasApplied = isset($appliedMap[$driveId]);
                        $appStatus = $hasApplied ? $appliedMap[$driveId]['status'] : null;
                        ?>

                        <div class="card" style="border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 1rem; position: relative;">
                            
                            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                                <div>
                                    <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0; color: var(--text-primary);"><?= e($drive['title']) ?></h3>
                                    <span style="font-size: 0.8125rem; color: var(--text-secondary);"><?= e($drive['company_name']) ?> &bull; Designation: <?= e($drive['designation']) ?></span>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.125rem; font-weight: bold; color: var(--success);">₹<?= number_format((float)$drive['ctc_lpa'], 2) ?> LPA</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Salary CTC Package</div>
                                </div>
                            </div>

                            <div style="background: rgba(255,255,255,0.01); padding: 0.75rem; border-radius: 6px; font-size: 0.75rem; border: 1px dashed var(--border-color); display: flex; flex-wrap: wrap; gap: 1rem;">
                                <div>📅 <strong>Drive Date</strong>: <?= date('d M Y', strtotime($drive['drive_date'])) ?></div>
                                <div>📍 <strong>Venue</strong>: <?= e($drive['location']) ?></div>
                                <div>🎓 <strong>Min CGPA</strong>: <?= number_format((float)$drive['eligibility_cgpa'], 2) ?></div>
                                <div>⚠️ <strong>Max Backlogs</strong>: <?= (int)$drive['max_backlogs'] ?></div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.5rem; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <?php if ($hasApplied): ?>
                                        <span style="font-size: 0.8125rem; color: var(--text-secondary);">Application Status:</span>
                                        <?php if ($appStatus === 'applied'): ?>
                                            <span class="badge badge-info" style="font-weight: 700;">APPLIED &bull; PENDING REVIEW</span>
                                        <?php elseif ($appStatus === 'shortlisted'): ?>
                                            <span class="badge badge-warning" style="font-weight: 700;">SHORTLISTED &bull; INTERVIEWS ONGOING</span>
                                        <?php elseif ($appStatus === 'selected'): ?>
                                            <span class="badge badge-success" style="font-weight: 700;">🎉 OFFERED & SELECTED!</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger" style="font-weight: 700;">APPLICATION CLOSED</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($isEligible): ?>
                                            <span class="badge badge-success">✓ Eligible to Apply</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger" style="font-weight: 600;">🛑 Not Eligible:</span>
                                            <span style="font-size: 0.75rem; color: var(--danger);">
                                                <?php 
                                                $reasons = [];
                                                if (!$isCgpaOk) $reasons[] = 'CGPA below cutoff';
                                                if (!$isBacklogsOk) $reasons[] = 'Exceeds backlog limit';
                                                if (!$isAttendanceOk) $reasons[] = 'Attendance below 75%';
                                                echo implode(', ', $reasons);
                                                ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <?php if ($hasApplied): ?>
                                        <button class="btn" disabled style="background: rgba(255,255,255,0.05); color: var(--text-secondary); cursor: not-allowed; border: 1px solid var(--border-color);">
                                            ✓ Applied
                                        </button>
                                    <?php else: ?>
                                        <form method="POST" action="/placement/apply">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="drive_id" value="<?= $driveId ?>">
                                            <button type="submit" class="btn <?= $isEligible ? 'btn-primary' : 'btn-secondary' ?>" <?= $isEligible ? '' : 'disabled style="opacity: 0.35; cursor: not-allowed;"' ?>>
                                                Apply Now
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
