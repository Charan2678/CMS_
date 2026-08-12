<?php
/**
 * HOD Timetable Management — Student & Staff Timetables
 *
 * Provides HOD / Admin with separate tabs for:
 *   1. Student Timetable (by Department, Semester, Section)
 *   2. Staff Timetable (by Faculty Member, Department)
 *
 * Includes Conflict Validation and Publish / Unpublish Workflow.
 */

$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
$currentType = strtolower($type ?? 'student');
?>

<!-- ── ALERTS ────────────────────────────────────────────────── -->
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem; font-weight: 600; padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid rgba(239,68,68,0.3); background: rgba(239,68,68,0.1); color: #dc2626;">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem; font-weight: 600; padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid rgba(16,185,129,0.3); background: rgba(16,185,129,0.1); color: #059669;">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<!-- ── PAGE HEADER & TABS ────────────────────────────────────── -->
<div class="card" style="width: 100%; margin-bottom: 1.5rem; padding: 1.25rem 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>🗓️</span> Timetable Management (HOD Portal)
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">
                Manage, validate conflicts, and publish Student Class Timetables &amp; Staff Teaching Schedules.
            </p>
        </div>

        <!-- Timetable Type Navigation Tabs -->
        <div style="display: inline-flex; background: var(--bg-main); padding: 0.25rem; border-radius: 10px; border: 1px solid var(--border-color);">
            <a href="/timetable?type=student<?= $sectionId ? '&section_id='.$sectionId : '' ?><?= $academicYearId ? '&academic_year_id='.$academicYearId : '' ?>"
               style="padding: 0.5rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; text-decoration: none; transition: all 0.2s ease; <?= $currentType === 'student' ? 'background: var(--accent-color); color: #fff; box-shadow: 0 2px 4px rgba(37,99,235,0.25);' : 'color: var(--text-secondary);' ?>">
                🎓 Student Timetable
            </a>
            <a href="/timetable?type=staff<?= $facultyId ? '&faculty_id='.$facultyId : '' ?><?= $academicYearId ? '&academic_year_id='.$academicYearId : '' ?>"
               style="padding: 0.5rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.875rem; text-decoration: none; transition: all 0.2s ease; <?= $currentType === 'staff' ? 'background: var(--accent-color); color: #fff; box-shadow: 0 2px 4px rgba(37,99,235,0.25);' : 'color: var(--text-secondary);' ?>">
                👨‍🏫 Staff Timetable
            </a>
        </div>
    </div>
</div>

<?php if ($currentType === 'student'): ?>
    <!-- =========================================================================
         TAB A: STUDENT TIMETABLE MANAGEMENT
         ========================================================================= -->
    <div class="card" style="width: 100%; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem;">
            🎓 Select Class &amp; Academic Session
        </h3>
        
        <form method="GET" action="/timetable" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end; background: var(--bg-main); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--border-color);">
            <input type="hidden" name="type" value="student">
            
            <div>
                <label class="form-label" style="font-weight: 700;">Academic Session *</label>
                <select name="academic_year_id" class="form-control" required>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['id'] === $academicYearId ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700;">Section / Batch *</label>
                <select name="section_id" class="form-control" required>
                    <option value="">-- Select Class Section --</option>
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
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Class Matrix</button>
            </div>
        </form>
    </div>

    <?php if ($sectionId > 0): ?>
        <!-- Publication Banner -->
        <div class="card" style="margin-bottom: 1.5rem; background: var(--bg-main); border: 1.5px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div>
                    <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-secondary); letter-spacing: 0.05em;">PUBLICATION STATUS</span>
                    <div style="margin-top: 0.2rem;">
                        <?php if ($pubStatus === 'PUBLISHED'): ?>
                            <span class="badge badge-success" style="padding: 0.4rem 0.875rem; font-size: 0.875rem; border-radius: 6px;">
                                ✓ PUBLISHED (Visible to Students)
                            </span>
                        <?php else: ?>
                            <span style="padding: 0.4rem 0.875rem; font-size: 0.875rem; border-radius: 6px; background: rgba(245,158,11,0.15); color: #b45309; font-weight: 700; border: 1px solid rgba(245,158,11,0.35);">
                                ⏳ DRAFT (Hidden from Students)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <?php if ($pubStatus === 'PUBLISHED'): ?>
                    <form method="POST" action="/timetable">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                        <input type="hidden" name="_action" value="unpublish">
                        <button type="submit" class="btn-secondary" style="padding: 0.45rem 1rem; font-weight: 700;">Unpublish Timetable</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="/timetable">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="student">
                        <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                        <input type="hidden" name="_action" value="publish">
                        <button type="submit" class="btn-primary" style="padding: 0.45rem 1.25rem; font-weight: 700; background: var(--success); border-color: var(--success);">
                            🚀 Publish Student Timetable
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; width: 100%;">
            <!-- Allocation Form -->
            <div class="card" style="height: fit-content;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>➕</span> Allocate Class Slot
                </h3>

                <form method="POST" action="/timetable">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="student">
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
                        <label class="form-label" for="period_number">Period Number (1–8) *</label>
                        <select id="period_number" name="period_number" class="form-control" required>
                            <?php foreach ($periodConfig as $col): ?>
                                <?php if ($col['type'] === 'period'): ?>
                                    <option value="<?= (int)$col['number'] ?>">Period <?= (int)$col['number'] ?> (<?= e($col['start']) ?> – <?= e($col['end']) ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
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

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="faculty_id">Faculty Instructor *</label>
                        <select id="faculty_id" name="faculty_id" class="form-control" required>
                            <option value="">-- Select Faculty --</option>
                            <?php foreach ($facultyList as $fac): ?>
                                <option value="<?= $fac['id'] ?>"><?= e($fac['first_name'] . ' ' . $fac['last_name']) ?> (<?= e($fac['employee_id']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="room_id">Classroom / Room</label>
                        <select id="room_id" name="room_id" class="form-control">
                            <option value="">-- Optional Classroom --</option>
                            <?php foreach ($rooms as $rm): ?>
                                <option value="<?= $rm['id'] ?>"><?= e($rm['name']) ?> <?= !empty($rm['building_name']) ? '('.e($rm['building_name']).')' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Save Slot</button>
                </form>
            </div>

            <!-- Schedule Matrix Panel -->
            <div class="card" style="padding: 0;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                        📅 Weekly Class Schedule Matrix
                    </h3>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">8 Teaching Periods + 2 Breaks</span>
                </div>

                <div style="overflow-x: auto; width: 100%;">
                    <table class="table" style="border-collapse: collapse; min-width: 850px; text-align: center;">
                        <thead>
                            <tr>
                                <th style="text-align: left; width: 80px; font-size: 0.75rem; text-transform: uppercase; background: var(--bg-main);">Day</th>
                                <?php foreach ($periodConfig as $col): ?>
                                    <?php if ($col['type'] === 'break'): ?>
                                        <th style="min-width: 80px; background: rgba(234,179,8,0.12); color: #854d0e; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.5rem 0.25rem;">
                                            <?= ($col['label'] === 'Morning Break') ? '☕' : '🍽️' ?><br>
                                            <?= e($col['label']) ?><br>
                                            <span style="font-weight: 500; font-size: 0.6rem; opacity: 0.85;"><?= e($col['start']) ?>–<?= e($col['end']) ?></span>
                                        </th>
                                    <?php else: ?>
                                        <th style="text-align: center; min-width: 95px; font-size: 0.75rem; padding: 0.5rem 0.25rem; background: var(--bg-main);">
                                            P<?= (int)$col['number'] ?><br>
                                            <span style="font-weight: 500; font-size: 0.62rem; color: var(--text-secondary);"><?= e($col['start']) ?>–<?= e($col['end']) ?></span>
                                        </th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($days as $day): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="font-weight: 800; text-transform: uppercase; color: var(--accent-color); font-size: 0.78rem; text-align: left; background: var(--bg-main);">
                                        <?= substr($day, 0, 3) ?>
                                    </td>
                                    <?php foreach ($periodConfig as $col): ?>
                                        <?php if ($col['type'] === 'break'): ?>
                                            <td style="background: rgba(234,179,8,0.08); font-size: 0.65rem; font-weight: 700; color: #854d0e; text-transform: uppercase;">
                                                <?= ($col['label'] === 'Morning Break') ? '☕' : '🍽️' ?>
                                            </td>
                                        <?php else: ?>
                                            <?php $slot = $grid[$day][$col['number']] ?? null; ?>
                                            <td style="text-align: center; background: <?= $slot ? 'rgba(2,132,199,0.1)' : 'transparent' ?>; padding: 0.4rem 0.25rem; position: relative;">
                                                <?php if ($slot): ?>
                                                    <strong style="color: var(--text-primary); display: block; font-size: 0.75rem;"><?= e($slot['subject_code']) ?></strong>
                                                    <span style="font-size: 0.65rem; color: var(--accent-color); font-weight: 700; display: block;"><?= e($slot['faculty_last_name'] ?? 'Faculty') ?></span>
                                                    <?php if (!empty($slot['room_name'])): ?>
                                                        <span style="font-size: 0.6rem; color: var(--text-secondary); display: block;">📍 <?= e($slot['room_name']) ?></span>
                                                    <?php endif; ?>
                                                    <!-- Delete Slot Form -->
                                                    <form method="POST" action="/timetable" style="margin-top: 0.2rem;" onsubmit="return confirm('Delete this slot?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="type" value="student">
                                                        <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                                                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                                                        <input type="hidden" name="slot_id" value="<?= (int)$slot['id'] ?>">
                                                        <input type="hidden" name="_action" value="delete">
                                                        <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 0.65rem; cursor: pointer; padding: 0;" title="Delete Slot">🗑️ Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span style="color: var(--border-color); opacity: 0.4; font-size: 0.75rem;">—</span>
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
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- =========================================================================
         TAB B: STAFF TIMETABLE MANAGEMENT
         ========================================================================= -->
    <div class="card" style="width: 100%; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem;">
            👨‍🏫 Select Faculty &amp; Academic Session
        </h3>
        
        <form method="GET" action="/timetable" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end; background: var(--bg-main); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--border-color);">
            <input type="hidden" name="type" value="staff">

            <div>
                <label class="form-label" style="font-weight: 700;">Academic Session *</label>
                <select name="academic_year_id" class="form-control" required>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['id'] === $academicYearId ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-weight: 700;">Faculty / Staff Member *</label>
                <select name="faculty_id" class="form-control" required>
                    <option value="">-- Select Faculty --</option>
                    <?php foreach ($facultyList as $fac): ?>
                        <option value="<?= $fac['id'] ?>" <?= $facultyId == $fac['id'] ? 'selected' : '' ?>>
                            <?= e($fac['first_name'] . ' ' . $fac['last_name']) ?> (<?= e($fac['employee_id']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Faculty Matrix</button>
            </div>
        </form>
    </div>

    <?php if ($facultyId > 0): ?>
        <!-- Publication Banner for Staff -->
        <div class="card" style="margin-bottom: 1.5rem; background: var(--bg-main); border: 1.5px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div>
                    <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; color: var(--text-secondary); letter-spacing: 0.05em;">STAFF PUBLICATION STATUS</span>
                    <div style="margin-top: 0.2rem;">
                        <?php if ($pubStatus === 'PUBLISHED'): ?>
                            <span class="badge badge-success" style="padding: 0.4rem 0.875rem; font-size: 0.875rem; border-radius: 6px;">
                                ✓ PUBLISHED (Visible to Faculty Member)
                            </span>
                        <?php else: ?>
                            <span style="padding: 0.4rem 0.875rem; font-size: 0.875rem; border-radius: 6px; background: rgba(245,158,11,0.15); color: #b45309; font-weight: 700; border: 1px solid rgba(245,158,11,0.35);">
                                ⏳ DRAFT (Hidden from Faculty Member)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <?php if ($pubStatus === 'PUBLISHED'): ?>
                    <form method="POST" action="/timetable">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="staff">
                        <input type="hidden" name="faculty_id" value="<?= $facultyId ?>">
                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                        <input type="hidden" name="_action" value="unpublish_staff">
                        <button type="submit" class="btn-secondary" style="padding: 0.45rem 1rem; font-weight: 700;">Unpublish Staff Timetable</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="/timetable">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="staff">
                        <input type="hidden" name="faculty_id" value="<?= $facultyId ?>">
                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                        <input type="hidden" name="_action" value="publish_staff">
                        <button type="submit" class="btn-primary" style="padding: 0.45rem 1.25rem; font-weight: 700; background: var(--success); border-color: var(--success);">
                            🚀 Publish Staff Timetable
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; width: 100%;">
            <!-- Allocation Form for Staff -->
            <div class="card" style="height: fit-content;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>➕</span> Assign Lecture Slot
                </h3>

                <form method="POST" action="/timetable">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="staff">
                    <input type="hidden" name="faculty_id" value="<?= $facultyId ?>">
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
                        <label class="form-label" for="period_number">Period Number (1–8) *</label>
                        <select id="period_number" name="period_number" class="form-control" required>
                            <?php foreach ($periodConfig as $col): ?>
                                <?php if ($col['type'] === 'period'): ?>
                                    <option value="<?= (int)$col['number'] ?>">Period <?= (int)$col['number'] ?> (<?= e($col['start']) ?> – <?= e($col['end']) ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
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

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" for="section_id">Target Class / Section *</label>
                        <select id="section_id" name="section_id" class="form-control" required>
                            <option value="">-- Select Target Section --</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= $sec['id'] ?>">
                                    <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (Section <?= e($sec['name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" for="room_id">Classroom / Room</label>
                        <select id="room_id" name="room_id" class="form-control">
                            <option value="">-- Optional Classroom --</option>
                            <?php foreach ($rooms as $rm): ?>
                                <option value="<?= $rm['id'] ?>"><?= e($rm['name']) ?> <?= !empty($rm['building_name']) ? '('.e($rm['building_name']).')' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Save Staff Slot</button>
                </form>
            </div>

            <!-- Faculty Schedule Matrix Panel -->
            <div class="card" style="padding: 0;">
                <div style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0;">
                        📅 Personal Teaching Schedule Matrix
                    </h3>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">8 Teaching Periods + 2 Breaks</span>
                </div>

                <div style="overflow-x: auto; width: 100%;">
                    <table class="table" style="border-collapse: collapse; min-width: 850px; text-align: center;">
                        <thead>
                            <tr>
                                <th style="text-align: left; width: 80px; font-size: 0.75rem; text-transform: uppercase; background: var(--bg-main);">Day</th>
                                <?php foreach ($periodConfig as $col): ?>
                                    <?php if ($col['type'] === 'break'): ?>
                                        <th style="min-width: 80px; background: rgba(234,179,8,0.12); color: #854d0e; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; padding: 0.5rem 0.25rem;">
                                            <?= ($col['label'] === 'Morning Break') ? '☕' : '🍽️' ?><br>
                                            <?= e($col['label']) ?><br>
                                            <span style="font-weight: 500; font-size: 0.6rem; opacity: 0.85;"><?= e($col['start']) ?>–<?= e($col['end']) ?></span>
                                        </th>
                                    <?php else: ?>
                                        <th style="text-align: center; min-width: 95px; font-size: 0.75rem; padding: 0.5rem 0.25rem; background: var(--bg-main);">
                                            P<?= (int)$col['number'] ?><br>
                                            <span style="font-weight: 500; font-size: 0.62rem; color: var(--text-secondary);"><?= e($col['start']) ?>–<?= e($col['end']) ?></span>
                                        </th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($days as $day): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="font-weight: 800; text-transform: uppercase; color: var(--accent-color); font-size: 0.78rem; text-align: left; background: var(--bg-main);">
                                        <?= substr($day, 0, 3) ?>
                                    </td>
                                    <?php foreach ($periodConfig as $col): ?>
                                        <?php if ($col['type'] === 'break'): ?>
                                            <td style="background: rgba(234,179,8,0.08); font-size: 0.65rem; font-weight: 700; color: #854d0e; text-transform: uppercase;">
                                                <?= ($col['label'] === 'Morning Break') ? '☕' : '🍽️' ?>
                                            </td>
                                        <?php else: ?>
                                            <?php $slot = $grid[$day][$col['number']] ?? null; ?>
                                            <td style="text-align: center; background: <?= $slot ? 'rgba(16,185,129,0.1)' : 'transparent' ?>; padding: 0.4rem 0.25rem;">
                                                <?php if ($slot): ?>
                                                    <strong style="color: var(--success); display: block; font-size: 0.75rem;"><?= e($slot['subject_code']) ?></strong>
                                                    <span style="font-size: 0.65rem; color: var(--text-primary); font-weight: 700; display: block;"><?= e($slot['section_name'] ?? 'Section') ?></span>
                                                    <?php if (!empty($slot['room_name'])): ?>
                                                        <span style="font-size: 0.6rem; color: var(--text-secondary); display: block;">📍 <?= e($slot['room_name']) ?></span>
                                                    <?php endif; ?>
                                                    <!-- Delete Slot Form -->
                                                    <form method="POST" action="/timetable" style="margin-top: 0.2rem;" onsubmit="return confirm('Delete this slot?');">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="type" value="staff">
                                                        <input type="hidden" name="faculty_id" value="<?= $facultyId ?>">
                                                        <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                                                        <input type="hidden" name="slot_id" value="<?= (int)$slot['id'] ?>">
                                                        <input type="hidden" name="_action" value="delete">
                                                        <button type="submit" style="background: none; border: none; color: #ef4444; font-size: 0.65rem; cursor: pointer; padding: 0;" title="Delete Slot">🗑️ Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span style="color: var(--border-color); opacity: 0.4; font-size: 0.75rem;">—</span>
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
        </div>
    <?php endif; ?>
<?php endif; ?>
