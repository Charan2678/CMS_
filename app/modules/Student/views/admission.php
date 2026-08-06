<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Student Admission Pipeline</h2>
        <span class="badge badge-info">Single Workflow Execution</span>
    </div>

    <form method="POST" action="/students/admission" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Section 1: Personal Information -->
        <h3 style="font-size: 1rem; color: #a5b4fc; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            1. Personal Information
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label" for="roll_number">Roll Number / Student ID *</label>
                <input type="text" id="roll_number" name="roll_number" class="form-control" required placeholder="e.g. 2026-CSE-001">
            </div>

            <div class="form-group">
                <label class="form-label" for="admission_number">Admission Reference No *</label>
                <input type="text" id="admission_number" name="admission_number" class="form-control" required placeholder="e.g. ADM-2026-1001">
            </div>

            <div class="form-group">
                <label class="form-label" for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required placeholder="First Name">
            </div>

            <div class="form-group">
                <label class="form-label" for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required placeholder="Last Name">
            </div>

            <div class="form-group">
                <label class="form-label" for="date_of_birth">Date of Birth *</label>
                <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="gender">Gender *</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="+91 9876543210">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Student Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="student@college.edu">
            </div>
        </div>

        <!-- Section 2: Guardian Details -->
        <h3 style="font-size: 1rem; color: #a5b4fc; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            2. Guardian Details
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label" for="guardian_name">Guardian Name *</label>
                <input type="text" id="guardian_name" name="guardian_name" class="form-control" required placeholder="Father / Mother / Guardian Name">
            </div>

            <div class="form-group">
                <label class="form-label" for="guardian_relationship">Relationship *</label>
                <select id="guardian_relationship" name="guardian_relationship" class="form-control" required>
                    <option value="father">Father</option>
                    <option value="mother">Mother</option>
                    <option value="guardian">Legal Guardian</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="guardian_mobile">Guardian Contact Mobile</label>
                <input type="text" id="guardian_mobile" name="guardian_mobile" class="form-control" placeholder="+91 9876543210">
            </div>

            <div class="form-group">
                <label class="form-label" for="guardian_occupation">Occupation</label>
                <input type="text" id="guardian_occupation" name="guardian_occupation" class="form-control" placeholder="Occupation / Business">
            </div>
        </div>

        <!-- Section 3: Academic Placement -->
        <h3 style="font-size: 1rem; color: #a5b4fc; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            3. Academic Placement
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label" for="academic_year_id">Academic Year *</label>
                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['is_current'] === 1 ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?> <?= (int)$ay['is_current'] === 1 ? '(Current)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="department_id">Department *</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="course_id">Course *</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="semester_id">Semester *</label>
                <select id="semester_id" name="semester_id" class="form-control" required>
                    <option value="">-- Select Semester --</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['display']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="section_id">Section *</label>
                <select id="section_id" name="section_id" class="form-control" required>
                    <option value="">-- Select Section --</option>
                    <?php foreach ($sections as $sec): ?>
                        <option value="<?= $sec['id'] ?>"><?= e($sec['course_code']) ?> - Sem <?= e($sec['semester_number']) ?> (Sec <?= e($sec['name']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Section 4: Document Upload -->
        <h3 style="font-size: 1rem; color: #a5b4fc; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
            4. Document Attachments
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label">Document 1 Type</label>
                <select name="document_types[]" class="form-control">
                    <option value="aadhar">Aadhar Card</option>
                    <option value="birth_cert">Birth Certificate</option>
                    <option value="tc">Transfer Certificate (TC)</option>
                    <option value="marksheet">Marksheet</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="documents[]" class="form-control">
            </div>
        </div>

        <!-- System Action Note -->
        <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.3); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
            <strong style="color: #a5b4fc;">⚡ Automated System Action:</strong>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Submitting this form will automatically generate the student record, assign current academic placement, attach uploaded documents, and provision an Admin-managed login user account (Username: Roll No, Default Password: <code>Student123!</code>).
            </p>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem;">Execute Student Admission</button>
        </div>
    </form>
</div>
