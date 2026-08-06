<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Class Timetable Scheduler</h2>
    </div>

    <!-- Section Selection -->
    <form method="GET" action="/timetable" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 1.5rem;">
        <div>
            <label class="form-label">Academic Year *</label>
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
                    <option value="<?= $sec['id'] ?>" <?= $sectionId == $sec['id'] ? 'selected' : '' ?>>
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (Sec <?= e($sec['name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Timetable Grid</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0): ?>
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Add Slot Form -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Allocate Class Slot</h3>
            </div>

            <form method="POST" action="/timetable">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">

                <div class="form-group">
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

                <div class="form-group">
                    <label class="form-label" for="period_number">Period Number (1-8) *</label>
                    <input type="number" id="period_number" name="period_number" class="form-control" min="1" max="8" value="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject_id">Subject *</label>
                    <select id="subject_id" name="subject_id" class="form-control" required>
                        <option value="">-- Select Subject --</option>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub['id'] ?>"><?= e($sub['code']) ?> — <?= e($sub['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="faculty_id">Faculty *</label>
                    <select id="faculty_id" name="faculty_id" class="form-control" required>
                        <option value="">-- Select Faculty --</option>
                        <?php foreach ($facultyList as $fac): ?>
                            <option value="<?= $fac['id'] ?>"><?= e($fac['first_name'] . ' ' . $fac['last_name']) ?> (<?= e($fac['employee_id']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Allocate Slot</button>
            </form>
        </div>

        <!-- Grid Display -->
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Weekly Schedule Grid</h3>
            </div>

            <?php
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                        <th style="padding: 0.5rem;">Day</th>
                        <?php for ($p = 1; $p <= 6; $p++): ?>
                            <th style="padding: 0.5rem; text-align: center;">P<?= $p ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($days as $day): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.5rem; font-weight: 700; text-transform: capitalize; color: #a5b4fc;"><?= substr($day, 0, 3) ?></td>
                            <?php for ($p = 1; $p <= 6; $p++): ?>
                                <?php $slot = $grid[$day][$p] ?? null; ?>
                                <td style="padding: 0.5rem; text-align: center; background: <?= $slot ? 'rgba(99, 102, 241, 0.15)' : 'transparent' ?>; border: 1px solid var(--border-color);">
                                    <?php if ($slot): ?>
                                        <strong style="color: #86efac; display: block;"><?= e($slot['subject_code']) ?></strong>
                                        <span style="font-size: 0.6875rem; color: var(--text-secondary);"><?= e($slot['faculty_last_name']) ?></span>
                                    <?php else: ?>
                                        <span style="color: rgba(255, 255, 255, 0.2);">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
