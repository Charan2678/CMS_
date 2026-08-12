<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Result\views\exam_timetable.php
$isAdmin = in_array($role, ['super_admin', 'admin', 'head_of_coe'], true);
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">🗓️ Exam Timetable Control Center</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Create, view, and schedule regular and arrear examination timetables per subject and room allocation.</p>
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

    <?php if ($isAdmin): ?>
        <!-- COE / ADMIN CONTROL PORTAL -->
        <div class="dashboard-grid-2">
            <!-- Left: Selector & Scheduler -->
            <div>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Select Exam Cohort</h2>
                    
                    <!-- Selection Form in Grid -->
                    <form method="GET" action="/exam-timetable" id="filterForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; background: rgba(255,255,255,0.03); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Academic Year *</label>
                            <select class="form-control" name="academic_year_id" onchange="this.form.submit()" required>
                                <option value="">-- Year --</option>
                                <?php foreach ($academicYears as $ay): ?>
                                    <option value="<?= (int) $ay['id'] ?>" <?= $academicYearId === (int)$ay['id'] ? 'selected' : '' ?>><?= e($ay['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Department *</label>
                            <select class="form-control" name="department_id" onchange="var c=document.getElementsByName('course_id')[0]; if(c){c.value='';} var s=document.getElementsByName('semester_id')[0]; if(s){s.value='';} this.form.submit();" required>
                                <option value="">-- Dept --</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= (int) $d['id'] ?>" <?= $departmentId === (int)$d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Course *</label>
                            <select class="form-control" name="course_id" onchange="var s=document.getElementsByName('semester_id')[0]; if(s){s.value='';} this.form.submit();" required <?= empty($courses) ? 'disabled' : '' ?>>
                                <option value="">-- Course --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= $courseId === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Semester *</label>
                            <select class="form-control" name="semester_id" onchange="this.form.submit()" required <?= empty($semesters) ? 'disabled' : '' ?>>
                                <option value="">-- Semester --</option>
                                <?php foreach ($semesters as $s): ?>
                                    <option value="<?= (int) $s['id'] ?>" <?= $semesterId === (int)$s['id'] ? 'selected' : '' ?>>Sem <?= e($s['number']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">Exam Type *</label>
                            <select class="form-control" name="exam_type" onchange="this.form.submit()" required>
                                <option value="regular" <?= $examType === 'regular' ? 'selected' : '' ?>>✍️ Regular Exam</option>
                                <option value="arrear" <?= $examType === 'arrear' ? 'selected' : '' ?>>⚠️ Arrear Exam</option>
                            </select>
                        </div>
                    </form>

                    <!-- Subjects Grid / Scheduler -->
                    <?php if ($semesterId > 0 && $academicYearId > 0): ?>
                        <div style="margin-bottom: 1rem; padding: 0.5rem 0;">
                            <span class="badge <?= $examType === 'regular' ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.8125rem; text-transform: uppercase;">
                                <?= $examType === 'regular' ? '✍️ Regular' : '⚠️ Arrear' ?> Examination Timetable Scheduler
                            </span>
                        </div>

                        <form method="POST" action="/exam-timetable">
                            <?= csrf_field() ?>
                            <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                            <input type="hidden" name="department_id" value="<?= $departmentId ?>">
                            <input type="hidden" name="course_id" value="<?= $courseId ?>">
                            <input type="hidden" name="semester_id" value="<?= $semesterId ?>">
                            <input type="hidden" name="exam_type" value="<?= $examType ?>">

                            <div style="overflow-x: auto;">
                                <table class="table" style="width: 100%; border-collapse: collapse; margin-bottom: 1.5rem;">
                                    <thead>
                                        <tr>
                                            <th>Subject Details</th>
                                            <th style="width: 170px;">Exam Date</th>
                                            <th style="width: 220px;">Session Time</th>
                                            <th style="width: 180px;">Allotted Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($subjectsList)): ?>
                                            <tr>
                                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No subjects registered in this semester.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($subjectsList as $subj): ?>
                                                <?php 
                                                $subjId = (int) $subj['id'];
                                                $sched = $schedules[$subjId] ?? ['exam_date' => '', 'time_slot' => '', 'room_id' => ''];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= e($subj['name']) ?></strong><br>
                                                        <small class="text-secondary" style="font-family: monospace; font-size: 0.75rem;"><?= e($subj['code']) ?> &bull; <?= ucfirst($subj['type']) ?></small>
                                                    </td>
                                                    <td>
                                                        <input type="date" class="form-control" name="exam_date[<?= $subjId ?>]" value="<?= e($sched['exam_date']) ?>" style="height: 38px;">
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="time_slot[<?= $subjId ?>]" style="height: 38px;">
                                                            <option value="09:30 AM - 12:30 PM" <?= $sched['time_slot'] === '09:30 AM - 12:30 PM' ? 'selected' : '' ?>>🌅 Morning (09:30 - 12:30)</option>
                                                            <option value="02:00 PM - 05:00 PM" <?= $sched['time_slot'] === '02:00 PM - 05:00 PM' ? 'selected' : '' ?>>🌇 Afternoon (14:00 - 17:00)</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="room_id[<?= $subjId ?>]" style="height: 38px;">
                                                            <option value="">-- Room --</option>
                                                            <?php foreach ($rooms as $rOption): ?>
                                                                <option value="<?= (int) $rOption['id'] ?>" <?= (int)$sched['room_id'] === (int)$rOption['id'] ? 'selected' : '' ?>><?= e($rOption['name']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-primary">
                                    💾 Save <?= ucfirst($examType) ?> schedules
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-secondary); padding: 3rem 1rem;">
                            ☝️ Please select Academic Year, Department, Course, and Semester to schedule exams.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Roll Number Search Panel -->
            <div>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">🔍 Student Exam Preview</h2>
                    <p style="color: var(--text-secondary); font-size: 0.8125rem; margin-bottom: 1rem;">Verify roll number schedules & quick link to hall ticket generation.</p>

                    <form method="GET" action="/exam-timetable" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                        <input type="text" class="form-control" name="roll_number" placeholder="Enter Roll No (e.g. 2026-CSE-001)" value="<?= e($rollNumber) ?>" required>
                        <button type="submit" class="btn btn-primary" style="height: 38px;">Search</button>
                    </form>

                    <?php if (!empty($rollNumber)): ?>
                        <hr style="border-color: var(--border-color); margin: 1.5rem 0;">
                        <?php if ($studentInfo): ?>
                            <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1rem;">
                                <h3 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 0.25rem;">👤 <?= e($studentInfo['first_name']) ?> <?= e($studentInfo['last_name']) ?></h3>
                                <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1rem;">Roll No: <strong style="font-family: monospace;"><?= e($studentInfo['roll_number']) ?></strong></p>
                                
                                <a href="/admit-card?student_id=<?= (int)$studentInfo['student_id'] ?>" target="_blank" class="btn btn-secondary w-100" style="text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; margin-bottom: 0.5rem;">
                                    🎫 View / Print Hall Ticket
                                </a>
                            </div>

                            <!-- Consolidated schedules preview for search -->
                            <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">✍️ Regular Exams</h4>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1rem;">
                                <?php if (empty($subjectsList)): ?>
                                    <div class="text-muted">No exams.</div>
                                <?php else: ?>
                                    <?php foreach ($subjectsList as $sub): ?>
                                        <?php $sc = $schedules[$sub['id']] ?? null; ?>
                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding: 0.35rem 0;">
                                            <span><?= e($sub['code']) ?></span>
                                            <span><?= $sc && !empty($sc['exam_date']) ? date('d-m-Y', strtotime($sc['exam_date'])) : 'TBD' ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <h4 style="font-size: 0.875rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--danger);">⚠️ Arrear Exams</h4>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                <?php if (empty($arrearSubjectsList)): ?>
                                    <div class="text-muted">No arrears recorded (All Clear! ✨).</div>
                                <?php else: ?>
                                    <?php foreach ($arrearSubjectsList as $sub): ?>
                                        <?php $sc = $arrearSchedules[$sub['id']] ?? null; ?>
                                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding: 0.35rem 0;">
                                            <span style="color: var(--danger); font-weight: 600;"><?= e($sub['code']) ?></span>
                                            <span><?= $sc && !empty($sc['exam_date']) ? date('d-m-Y', strtotime($sc['exam_date'])) : 'TBD' ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="badge badge-danger" style="display: block; text-align: center; padding: 0.5rem;">Student not found.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- STUDENT TIMETABLE PORTAL (Regular & Arrear Tabs) -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">🗓️ My End-Semester Examination Timetable</h2>
            <?php if ($studentInfo): ?>
                <p style="color: var(--text-secondary); font-size: 0.8125rem; margin-bottom: 1.5rem;">Exam dates & room allocations for <strong><?= e($studentInfo['first_name']) ?> <?= e($studentInfo['last_name']) ?></strong> (Roll No: <strong style="font-family: monospace;"><?= e($studentInfo['roll_number']) ?></strong>)</p>
            <?php endif; ?>

            <!-- REGULAR EXAMS SECTION -->
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary);">
                    <span style="color: var(--success);">✍️</span> Regular Semester Examination Timetable
                </h3>
                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Subject Details</th>
                                <th style="width: 180px;">Exam Date</th>
                                <th style="width: 220px;">Session / Time</th>
                                <th style="width: 180px;">Allotted Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subjectsList)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">📭 No regular exams scheduled for your semester placement.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subjectsList as $subj): ?>
                                    <?php 
                                    $subjId = (int) $subj['id'];
                                    $sched = $schedules[$subjId] ?? null;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($subj['name']) ?></strong><br>
                                            <small class="text-secondary" style="font-family: monospace; font-size: 0.75rem;"><?= e($subj['code']) ?> &bull; <?= ucfirst($subj['type']) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($sched && !empty($sched['exam_date'])): ?>
                                                <strong style="color: var(--accent-color);"><?= date('d M Y', strtotime($sched['exam_date'])) ?></strong>
                                            <?php else: ?>
                                                <span style="color: var(--text-secondary); font-style: italic;">TBD</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="color: var(--text-secondary);">
                                            <?= $sched ? e($sched['time_slot']) : 'TBD' ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $roomName = 'TBD';
                                            if ($sched && !empty($sched['room_id'])) {
                                                foreach ($rooms as $rm) {
                                                    if ((int)$rm['id'] === (int)$sched['room_id']) {
                                                        $roomName = $rm['name'];
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <span class="badge badge-success"><?= e($roomName) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ARREAR EXAMS SECTION -->
            <div>
                <h3 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: var(--danger);">
                    <span>⚠️</span> Arrear Semester Examination Timetable
                </h3>
                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Subject Details</th>
                                <th style="width: 180px;">Exam Date</th>
                                <th style="width: 220px;">Session / Time</th>
                                <th style="width: 180px;">Allotted Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($arrearSubjectsList)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 3rem; background: rgba(16,185,129,0.02); border-radius: 8px;">
                                        ✨ <strong>No Active Arrears Recorded.</strong> All Clear! Keep up the good work!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($arrearSubjectsList as $subj): ?>
                                    <?php 
                                    $subjId = (int) $subj['id'];
                                    $sched = $arrearSchedules[$subjId] ?? null;
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= e($subj['name']) ?></strong><br>
                                            <small class="text-secondary" style="font-family: monospace; font-size: 0.75rem;"><?= e($subj['code']) ?> &bull; <span class="badge badge-danger" style="font-size: 0.625rem; padding: 0.15rem 0.35rem;">ARREAR</span></small>
                                        </td>
                                        <td>
                                            <?php if ($sched && !empty($sched['exam_date'])): ?>
                                                <strong style="color: var(--danger);"><?= date('d M Y', strtotime($sched['exam_date'])) ?></strong>
                                            <?php else: ?>
                                                <span style="color: var(--text-secondary); font-style: italic;">TBD</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="color: var(--text-secondary);">
                                            <?= $sched ? e($sched['time_slot']) : 'TBD' ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $roomName = 'TBD';
                                            if ($sched && !empty($sched['room_id'])) {
                                                foreach ($rooms as $rm) {
                                                    if ((int)$rm['id'] === (int)$sched['room_id']) {
                                                        $roomName = $rm['name'];
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <span class="badge badge-success"><?= e($roomName) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
