<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
            <?= icon('clock', 'icon-md') ?> My Class Timetable
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            Weekly class schedule and lecture locations for your section (<?= e($studentAcademic['section_name'] ?? 'Section A') ?>).
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
            <?= icon('printer', 'icon-xs') ?> Print Timetable
        </button>
    </div>
</div>

<div class="card">
    <?php 
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $periods = [1, 2, 3, 4, 5, 6];
    ?>

    <div class="table-responsive">
        <table class="table" style="text-align: center;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 120px;">Day \ Period</th>
                    <?php foreach ($periods as $p): ?>
                        <th style="min-width: 140px;">PERIOD <?= $p ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($days as $day): ?>
                    <tr>
                        <td style="text-align: left; font-weight: 800; color: var(--accent-color); text-transform: capitalize; background: var(--bg-main);">
                            <?= $day ?>
                        </td>

                        <?php foreach ($periods as $p): ?>
                            <?php $slot = $grid[$day][$p] ?? null; ?>
                            <td style="vertical-align: top;">
                                <?php if ($slot): ?>
                                    <div style="background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.25); border-radius: 10px; padding: 0.625rem; box-shadow: var(--shadow-xs);">
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem;"><?= e($slot['subject_code']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.125rem;"><?= e($slot['subject_name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--accent-color); margin-top: 0.25rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                            <?= icon('school', 'icon-xs') ?> <?= e($slot['faculty_first_name']) ?> <?= e($slot['faculty_last_name']) ?>
                                        </div>
                                        <?php if (!empty($slot['room_name'])): ?>
                                            <div style="font-size: 0.6875rem; color: var(--text-secondary); margin-top: 0.125rem; display: flex; align-items: center; justify-content: center; gap: 0.2rem;">
                                                <?= icon('map-pin', 'icon-xs') ?> <?= e($slot['room_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div style="background: var(--bg-main); border: 1px dashed var(--border-color); border-radius: 10px; padding: 0.625rem; color: var(--text-secondary); font-size: 0.75rem; font-weight: 500;">
                                        Free Slot
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
