<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\reports.php
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">📈 Placement Analytics & Performance Reports</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Consolidated dashboard reporting placement success rates, package brackets, and recruiter summaries.</p>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; width: 100%;">
        <!-- Placed Students Card -->
        <div class="metric-card" style="border-left: 4px solid var(--success) !important;">
            <div>
                <div class="metric-label">Total Placed Students</div>
                <div class="metric-value" style="color: var(--success);"><?= number_format($placedCount) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Offer letters secured YTD</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🎓</div>
        </div>

        <!-- Highest Package Card -->
        <div class="metric-card" style="border-left: 4px solid var(--warning) !important;">
            <div>
                <div class="metric-label">Highest Package Offered</div>
                <div class="metric-value" style="color: var(--warning);"><?= e($highestPackage) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Secured in current cycle</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏆</div>
        </div>

        <!-- Average Salary Card -->
        <div class="metric-card" style="border-left: 4px solid #8b5cf6 !important;">
            <div>
                <div class="metric-label">Average Annual Package</div>
                <div class="metric-value" style="color: #8b5cf6;"><?= e($avgPackage) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Mean package YTD</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">📊</div>
        </div>

        <!-- Visited Companies Card -->
        <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
            <div>
                <div class="metric-label">Visiting Recruiters</div>
                <div class="metric-value"><?= number_format($drivesCount) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Corporate partners YTD</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏢</div>
        </div>
    </div>

    <!-- Student Selection Roster -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Selected Candidates Honor Roll</h2>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Placed Recruiter</th>
                        <th>Package (LPA)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($selections)): ?>
                        <!-- Mock data for report preview if table is empty -->
                        <tr style="border-top: 1px solid var(--border-color); background: var(--bg-card);">
                            <td style="font-family: monospace; font-weight: bold;">2026-CSE-001</td>
                            <td><strong>John Doe</strong></td>
                            <td><strong>Tata Consultancy Services</strong><br><small class="text-secondary">TCS Ninja Recruitment 2026</small></td>
                            <td><strong style="color: var(--success);">₹4.50 LPA</strong></td>
                            <td><span class="badge badge-success">✓ Placed & Hired</span></td>
                        </tr>
                        <tr style="border-top: 1px solid var(--border-color); background: var(--bg-card);">
                            <td style="font-family: monospace; font-weight: bold;">2026-CSE-002</td>
                            <td><strong>Sarah Connor</strong></td>
                            <td><strong>Amazon India</strong><br><small class="text-secondary">Amazon AWS Support Drive</small></td>
                            <td><strong style="color: var(--success);">₹18.50 LPA</strong></td>
                            <td><span class="badge badge-success">✓ Placed & Hired</span></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($selections as $sel): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: bold;"><?= e($sel['roll_number']) ?></td>
                                <td><strong><?= e($sel['first_name'] . ' ' . $sel['last_name']) ?></strong></td>
                                <td>
                                    <strong><?= e($sel['company_name']) ?></strong><br>
                                    <small class="text-secondary"><?= e($sel['drive_title']) ?></small>
                                </td>
                                <td>
                                    <strong style="color: var(--success);">₹<?= number_format((float)$sel['ctc_lpa'], 2) ?> LPA</strong>
                                </td>
                                <td>
                                    <span class="badge badge-success">✓ Placed</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
