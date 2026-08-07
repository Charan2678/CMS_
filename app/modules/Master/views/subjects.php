<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Create Subject Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Subject
        </h2>

        <form method="POST" action="/master/subjects">
            <?= csrf_field() ?>

            <!-- Course Dropdown -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="course_id">Course *</label>
                <select id="course_id" name="course_id" class="form-control" required onchange="filterSemestersByCourse()">
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= e($c['code']) ?> — <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Dynamic Semester Dropdown -->
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="semester_id">Semester *</label>
                <select id="semester_id" name="semester_id" class="form-control" required disabled>
                    <option value="">-- Select Course First --</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Subject Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Data Structures & Algorithms">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="code">Subject Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. CS201">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="type">Subject Type *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="theory">Theory</option>
                    <option value="practical">Practical / Lab</option>
                    <option value="elective">Elective</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="credits">Credits</label>
                    <input type="number" step="0.5" id="credits" name="credits" class="form-control" value="3.0">
                </div>

                <div class="form-group">
                    <label class="form-label" for="max_internal_marks">Max Internal</label>
                    <input type="number" id="max_internal_marks" name="max_internal_marks" class="form-control" value="25">
                </div>

                <div class="form-group">
                    <label class="form-label" for="max_external_marks">Max External</label>
                    <input type="number" id="max_external_marks" name="max_external_marks" class="form-control" value="75">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pass_external_marks">Pass External</label>
                    <input type="number" id="pass_external_marks" name="pass_external_marks" class="form-control" value="30">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Subject</button>
        </form>
    </div>

    <!-- Subjects Directory Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📚</span> Academic Curriculum & Subjects Directory
        </h2>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>Course / Sem</th>
                        <th>Type</th>
                        <th>Credits</th>
                        <th>Max Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No subjects configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects as $sub): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($sub['code']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($sub['name']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($sub['course_code']) ?> (Sem <?= e($sub['semester_number']) ?>)</td>
                                <td>
                                    <span class="badge badge-info" style="text-transform: capitalize;"><?= e($sub['type']) ?></span>
                                </td>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($sub['credits']) ?></td>
                                <td style="color: var(--text-secondary); font-size: 0.8125rem;"><?= e($sub['max_internal_marks']) ?> Int / <?= e($sub['max_external_marks']) ?> Ext</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const allSemestersData = <?= json_encode($semesters) ?>;

    function filterSemestersByCourse() {
        const courseId = document.getElementById('course_id').value;
        const semSelect = document.getElementById('semester_id');

        semSelect.innerHTML = '<option value="">-- Select Semester --</option>';

        if (!courseId) {
            semSelect.disabled = true;
            semSelect.options[0].text = '-- Select Course First --';
            return;
        }

        const filteredSems = allSemestersData.filter(s => String(s.course_id) === String(courseId));

        if (filteredSems.length === 0) {
            semSelect.disabled = true;
            semSelect.options[0].text = 'No Semesters Found for Selected Course';
            return;
        }

        semSelect.disabled = false;
        filteredSems.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = `Sem ${s.number}`;
            semSelect.appendChild(opt);
        });
    }
</script>
