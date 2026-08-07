<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="page-split">
    <!-- Create Section Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Class Section
        </h2>

        <form method="POST" action="/master/sections">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="academic_year_id">Academic Year *</label>
                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                    <option value="">-- Select Academic Year --</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['is_current'] === 1 ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?> <?= (int)$ay['is_current'] === 1 ? '(Active Session)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

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
                <label class="form-label" for="name">Section Name / Batch *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. A, B, or C">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" for="max_strength">Max Student Capacity</label>
                <input type="number" id="max_strength" name="max_strength" class="form-control" value="60" min="10" max="200">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Section</button>
        </form>
    </div>

    <!-- Sections Directory Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📑</span> Academic Sections & Batches Directory
        </h2>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Section</th>
                        <th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sections)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No sections configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sections as $sec): ?>
                            <?php 
                                $secName = $sec['name'] ?? '';
                                $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                            ?>
                            <tr>
                                <td style="color: var(--text-secondary);"><?= e($sec['academic_year_name']) ?></td>
                                <td style="font-weight: 700; color: var(--accent-color);"><?= e($sec['course_code']) ?></td>
                                <td style="font-weight: 600;">Sem <?= e($sec['semester_number']) ?></td>
                                <td style="font-weight: 800; color: var(--text-primary);"><?= e($cleanSec) ?></td>
                                <td>
                                    <span class="badge badge-info"><?= e($sec['max_strength']) ?> Capacity</span>
                                </td>
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
