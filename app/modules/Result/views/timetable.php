<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('clock', 'icon-md') ?> Class Timetable Scheduler &amp; Matrix
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Select section to allocate weekly periods and view class schedule grid
            </div>
        </div>
    </div>

    <!-- Section Selection — Full Width Grid -->
    <form method="GET" action="/timetable" class="filter-bar">
        <div>
            <label class="form-label">Academic Session *</label>
            <select name="academic_year_id" class="form-control" required>
                <?php foreach ($academicYears as $ay): ?>
                    <option value="<?= $ay['id'] ?>" <?= (int)$ay['id'] === $academicYearId || ((int)$ay['is_current'] === 1 && $academicYearId === 0) ? 'selected' : '' ?>>
                        <?= e($ay['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Section / Batch *</label>
            <select name="section_id" class="form-control" required>
                <option value="">-- Select Section --</option>
                <?php foreach ($sections as $sec): ?>
                    <?php 
                        $secName = $sec['name'] ?? '';
                        $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                    ?>
                    <option value="<?= $sec['id'] ?>" <?= $sectionId == $sec['id'] ? 'selected' : '' ?>>
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (<?= e($cleanSec) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;"><?= icon('search', 'icon-xs') ?> Load Schedule Matrix</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0): ?>
    <div class="dashboard-grid-2" style="width: 100%;">
        <!-- Add Slot Form -->
        <div class="card">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('plus', 'icon-sm') ?> Allocate Class Slot
            </h3>

            <form method="POST" action="/timetable">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="day_of_week">Day of Week *</label>
                    <select id="day_of_week" name="day_of_week" class="form-control" required>
                        <option value="monday">Monday</option>
                        <option value="tuesday">Tuesday</option>
                        <option value="wednesday">Wednesday</option>
                        <option value="thursday">Thursday</option>
                        <option value="friday">Friday</option>
                        <option value="saturday">Saturday</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="period_number">Period Number (1-6) *</label>
                    <input type="number" id="period_number" name="period_number" class="form-control" min="1" max="6" value="1" required>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="subject_id">Subject *</label>
                    <select id="subject_id" name="subject_id" class="form-control" required>
                        <option value="">-- Select Subject --</option>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub['id'] ?>"><?= e($sub['code']) ?> — <?= e($sub['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="faculty_id">Faculty Instructor *</label>
                    <select id="faculty_id" name="faculty_id" class="form-control" required>
                        <option value="">-- Select Faculty --</option>
                        <?php foreach ($facultyList as $fac): ?>
                            <option value="<?= $fac['id'] ?>"><?= e($fac['first_name'] . ' ' . $fac['last_name']) ?> (<?= e($fac['employee_id']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;"><?= icon('plus', 'icon-xs') ?> Allocate Slot</button>
            </form>
        </div>

        <!-- Weekly Schedule Grid Panel -->
        <div class="card">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('calendar-days', 'icon-sm') ?> Weekly Class Schedule Matrix
            </h3>

            <?php
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <?php for ($p = 1; $p <= 6; $p++): ?>
                                <th style="text-align: center;">Period <?= $p ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days as $day): ?>
                            <tr>
                                <td style="font-weight: 800; text-transform: uppercase; color: var(--accent-color);"><?= substr($day, 0, 3) ?></td>
                                <?php for ($p = 1; $p <= 6; $p++): ?>
                                    <?php $slot = $grid[$day][$p] ?? null; ?>
                                    <td style="text-align: center; background: <?= $slot ? 'rgba(2, 132, 199, 0.08)' : 'var(--bg-main)' ?>; border: 1px solid var(--border-color); padding: 0.625rem 0.35rem; border-radius: 6px;">
                                        <?php if ($slot): ?>
                                            <strong style="color: var(--text-primary); display: block; font-size: 0.8125rem;"><?= e($slot['subject_code']) ?></strong>
                                            <span style="font-size: 0.6875rem; color: var(--accent-color); font-weight: 700;"><?= e($slot['faculty_last_name']) ?></span>
                                        <?php else: ?>
                                            <span style="color: var(--text-secondary); opacity: 0.4;">—</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
