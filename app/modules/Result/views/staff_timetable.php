<?php
/**
 * Faculty — My Staff Timetable
 *
 * Displays the personal teaching timetable matrix allocated to the logged-in faculty member.
 * Format: 8 Periods + 2 Breaks (Morning Break, Lunch Break)
 * View-only. Faculty cannot create, edit, delete, or publish.
 */

$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
$facName = trim(($facultyInfo['first_name'] ?? '') . ' ' . ($facultyInfo['last_name'] ?? ''));
if (empty($facName)) {
    $facName = 'Faculty Member';
}
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;">My Staff Timetable</h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            Personal weekly lecture schedule for <strong><?= e($facName) ?></strong> (<?= e($facultyInfo['dept_name'] ?? 'Department') ?>).
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
            <span>🖨️</span> Print Timetable
        </button>
    </div>
</div>

<?php if (empty($is_published)): ?>
    <div class="card" style="padding: 3.5rem 2rem; text-align: center;">
        <div style="font-size: 3.5rem; margin-bottom: 0.75rem;">📅</div>
        <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.5rem;">
            Staff timetable has not been published yet.
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; max-width: 480px; margin: 0.5rem auto 0; line-height: 1.5;">
            Your HOD or department has not published your official staff teaching timetable for the current academic year yet.
            Please check back later once it is published.
        </p>
    </div>
<?php else: ?>

<!-- ── Legend ─────────────────────────────────────────────────── -->
<div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: center;">
    <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; letter-spacing: 0.03em;">LEGEND:</span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4);"></span> Assigned Lecture Class
    </span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: rgba(234,179,8,0.18); border: 1px solid rgba(234,179,8,0.45);"></span> Break
    </span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: var(--bg-main); border: 1px dashed var(--border-color);"></span> Free / Off Slot
    </span>
</div>

<div class="card" style="padding: 0;">
    <div style="overflow-x: auto; width: 100%;">
        <table class="table" style="text-align: center; border-collapse: collapse; width: 100%; min-width: 900px;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 110px; padding: 0.875rem 1rem; background: var(--bg-main); font-weight: 700; color: var(--text-primary); border-bottom: 2px solid var(--border-color);">
                        DAY / PERIOD
                    </th>
                    <?php foreach ($periodConfig as $col): ?>
                        <?php if ($col['type'] === 'break'): ?>
                            <th style="padding: 0.875rem 0.5rem; background: rgba(234, 179, 8, 0.12); color: #854d0e; font-weight: 700; border-bottom: 2px solid var(--border-color); width: 90px;">
                                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                    ☕ <?= e($col['label']) ?>
                                </div>
                                <div style="font-size: 0.7rem; font-weight: 500; opacity: 0.85; margin-top: 0.15rem;">
                                    <?= e($col['start']) ?> – <?= e($col['end']) ?>
                                </div>
                            </th>
                        <?php else: ?>
                            <th style="padding: 0.875rem 0.5rem; background: var(--bg-main); font-weight: 700; color: var(--text-primary); border-bottom: 2px solid var(--border-color);">
                                <div style="font-size: 0.85rem; font-weight: 700;">
                                    Period <?= (int)$col['number'] ?>
                                </div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); font-weight: 500; margin-top: 0.15rem;">
                                    <?= e($col['start']) ?> – <?= e($col['end']) ?>
                                </div>
                            </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($days as $day): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="text-align: left; font-weight: 700; text-transform: capitalize; padding: 1rem; background: var(--bg-main); color: var(--text-primary); border-right: 1px solid var(--border-color);">
                            <?= e($day) ?>
                        </td>

                        <?php foreach ($periodConfig as $col): ?>
                            <?php if ($col['type'] === 'break'): ?>
                                <td style="background: rgba(234, 179, 8, 0.07); color: #92400e; font-weight: 700; font-size: 0.78rem; vertical-align: middle; border-right: 1px solid var(--border-color);">
                                    <div style="letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.72rem;">
                                        <?= e($col['label']) ?>
                                    </div>
                                </td>
                            <?php else: ?>
                                <?php
                                $pNum = (int)$col['number'];
                                $slot = $grid[$day][$pNum] ?? null;
                                ?>
                                <td style="padding: 0.5rem; vertical-align: middle; border-right: 1px solid var(--border-color); <?= $slot ? 'background: rgba(16, 185, 129, 0.08);' : '' ?>">
                                    <?php if ($slot): ?>
                                        <div style="font-weight: 700; font-size: 0.875rem; color: var(--success);">
                                            <?= e($slot['subject_code']) ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-primary); margin-top: 0.15rem; font-weight: 600;">
                                            <?= e($slot['subject_name']) ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: var(--accent-color); font-weight: 700; margin-top: 0.2rem;">
                                            Class: <?= e($slot['section_name']) ?>
                                        </div>
                                        <?php if (!empty($slot['room_name'])): ?>
                                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                                📍 <?= e($slot['room_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: var(--border-color); font-size: 0.85rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ── Print Styles ─────────────────────────────────────────── -->
<style>
@media print {
    .sidebar, nav, .btn-primary, .page-header > div:last-child {
        display: none !important;
    }
    body, .card {
        background: #fff !important;
        box-shadow: none !important;
    }
    table {
        font-size: 0.7rem !important;
    }
    th, td {
        padding: 4px 6px !important;
    }
}
</style>
