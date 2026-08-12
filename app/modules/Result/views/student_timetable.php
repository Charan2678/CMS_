<?php
/**
 * Student — My Class Timetable
 *
 * Columns: DAY | P1 | P2 | Morning Break | P3 | P4 | Lunch Break | P5 | P6 | P7 | P8
 * $periodConfig is passed from ResultController::studentTimetable() via ResultService::getPeriodConfig().
 * To change timings or add/remove periods, edit ResultService::getPeriodConfig() — do NOT edit this view.
 */

$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
$isParent = (auth_role() === 'parent');
$pageHeading = $isParent ? 'Ward Class Timetable' : 'My Class Timetable';
$pageDesc = $isParent 
    ? 'Weekly class schedule and lecture locations for your child\'s section (' . e($studentAcademic['section_name'] ?? 'Section') . ').'
    : 'Weekly class schedule and lecture locations for your section (' . e($studentAcademic['section_name'] ?? 'Section A') . ').';
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?= e($pageHeading) ?></h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            <?= $pageDesc ?>
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
            Class timetable has not been published yet.
        </h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; max-width: 480px; margin: 0.5rem auto 0; line-height: 1.5;">
            Your HOD or department has not published the official class timetable for 
            <strong><?= e($studentAcademic['section_name'] ?? 'your section') ?></strong> yet.
            Please check back later once it is published.
        </p>
    </div>
<?php else: ?>

<!-- ── Legend ─────────────────────────────────────────────────── -->
<div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: center;">
    <span style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600; letter-spacing: 0.03em;">LEGEND:</span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: rgba(2,132,199,0.15); border: 1px solid rgba(2,132,199,0.4);"></span> Class Period
    </span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: rgba(234,179,8,0.18); border: 1px solid rgba(234,179,8,0.45);"></span> Break
    </span>
    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.78rem; color: var(--text-secondary);">
        <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background: var(--bg-main); border: 1px dashed var(--border-color);"></span> Free Slot
    </span>
</div>

<div class="card" style="padding: 0;">
    <div style="overflow-x: auto; width: 100%;">
        <table class="table" style="text-align: center; border-collapse: collapse; width: 100%; min-width: 900px;">
            <!-- ── Table Head ──────────────────────────────────── -->
            <thead>
                <tr>
                    <!-- Day column -->
                    <th style="
                        text-align: left;
                        width: 110px;
                        min-width: 90px;
                        padding: 0.75rem 1rem;
                        position: sticky;
                        left: 0;
                        z-index: 2;
                        background: var(--bg-card);
                        border-right: 2px solid var(--border-color);
                        font-size: 0.8125rem;
                        letter-spacing: 0.05em;
                        text-transform: uppercase;
                    ">Day</th>

                    <?php foreach ($periodConfig as $col): ?>
                        <?php if ($col['type'] === 'break'): ?>
                            <!-- Break header -->
                            <th style="
                                min-width: 110px;
                                padding: 0.6rem 0.5rem;
                                background: rgba(234,179,8,0.14);
                                border-left: 2px dashed rgba(234,179,8,0.5);
                                border-right: 2px dashed rgba(234,179,8,0.5);
                                color: #92710a;
                                font-size: 0.7rem;
                                font-weight: 700;
                                letter-spacing: 0.06em;
                                text-transform: uppercase;
                            ">
                                <?= ($col['label'] === 'Morning Break') ? '☕' : '🍽️' ?>
                                <br>
                                <span style="font-size: 0.65rem; font-weight: 800;"><?= e($col['label']) ?></span>
                                <div style="font-size: 0.6rem; font-weight: 500; margin-top: 2px; opacity: 0.8;">
                                    <?= e($col['start']) ?> – <?= e($col['end']) ?>
                                </div>
                            </th>
                        <?php else: ?>
                            <!-- Period header -->
                            <th style="
                                min-width: 130px;
                                padding: 0.6rem 0.5rem;
                                font-size: 0.8rem;
                                font-weight: 700;
                                letter-spacing: 0.03em;
                            ">
                                <div><?= e($col['label']) ?></div>
                                <div style="font-size: 0.65rem; font-weight: 500; color: var(--text-secondary); margin-top: 2px;">
                                    <?= e($col['start']) ?> – <?= e($col['end']) ?>
                                </div>
                            </th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>

            <!-- ── Table Body ──────────────────────────────────── -->
            <tbody>
                <?php foreach ($days as $day): ?>
                    <tr>
                        <!-- Day label -->
                        <td style="
                            text-align: left;
                            font-weight: 700;
                            color: var(--accent-color);
                            text-transform: capitalize;
                            background: var(--bg-main);
                            padding: 0.75rem 1rem;
                            position: sticky;
                            left: 0;
                            z-index: 1;
                            border-right: 2px solid var(--border-color);
                            font-size: 0.875rem;
                        ">
                            <?= ucfirst($day) ?>
                        </td>

                        <?php foreach ($periodConfig as $col): ?>
                            <?php if ($col['type'] === 'break'): ?>
                                <!-- Break cell — non-editable, visually distinct -->
                                <td style="
                                    background: rgba(234,179,8,0.10);
                                    border-left: 2px dashed rgba(234,179,8,0.45);
                                    border-right: 2px dashed rgba(234,179,8,0.45);
                                    padding: 0.5rem 0.375rem;
                                    vertical-align: middle;
                                ">
                                    <div style="
                                        font-size: 0.7rem;
                                        font-weight: 700;
                                        color: #92710a;
                                        letter-spacing: 0.04em;
                                        text-transform: uppercase;
                                    ">
                                        <?= ($col['label'] === 'Morning Break') ? '☕ Break' : '🍽️ Lunch' ?>
                                    </div>
                                </td>
                            <?php else: ?>
                                <?php $slot = $grid[$day][$col['number']] ?? null; ?>
                                <td style="vertical-align: top; padding: 0.375rem 0.35rem;">
                                    <?php if ($slot): ?>
                                        <div style="
                                            background: rgba(2,132,199,0.10);
                                            border: 1px solid rgba(2,132,199,0.3);
                                            border-radius: 8px;
                                            padding: 0.5rem 0.5rem;
                                            text-align: left;
                                        ">
                                            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.8125rem; line-height: 1.2;">
                                                <?= e($slot['subject_code']) ?>
                                            </div>
                                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.1rem; line-height: 1.3;">
                                                <?= e($slot['subject_name']) ?>
                                            </div>
                                            <div style="font-size: 0.7rem; color: var(--accent-color); margin-top: 0.25rem; font-weight: 600;">
                                                👨‍🏫 <?= e($slot['faculty_first_name']) ?> <?= e($slot['faculty_last_name']) ?>
                                            </div>
                                            <?php if (!empty($slot['room_name'])): ?>
                                                <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 0.1rem;">
                                                    📍 <?= e($slot['room_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div style="
                                            background: var(--bg-main);
                                            border: 1px dashed var(--border-color);
                                            border-radius: 8px;
                                            padding: 0.5rem 0.375rem;
                                            color: var(--text-secondary);
                                            font-size: 0.7rem;
                                            font-weight: 500;
                                            text-align: center;
                                        ">
                                            Free Slot
                                        </div>
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
    /* Hide sidebar, nav, print button, legend */
    .sidebar, nav, .btn-primary, .page-header > div:last-child, .alert {
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
