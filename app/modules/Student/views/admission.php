<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('user-plus', 'icon-md') ?> Student Admission Pipeline
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Provision new student record, academic placement, documents, and portal login
            </div>
        </div>
        <span class="badge badge-info" style="font-size: 0.75rem;">Single Workflow Execution</span>
    </div>

    <form method="POST" action="/students/admission" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Section 1: Personal Information -->
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent-color); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.45rem;">
            <?= icon('user', 'icon-xs') ?> 1. Personal &amp; Contact Details
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
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent-color); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.45rem;">
            <?= icon('users', 'icon-xs') ?> 2. Parent &amp; Guardian Details
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label" for="guardian_name">Guardian Full Name *</label>
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
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent-color); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.45rem;">
            <?= icon('graduation-cap', 'icon-xs') ?> 3. Academic Placement
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label" for="academic_year_id">Academic Year *</label>
                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['is_current'] === 1 ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?> <?= (int)$ay['is_current'] === 1 ? '(Active Session)' : '' ?>
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
                        <?php 
                            $secName = $sec['name'] ?? '';
                            $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                        ?>
                        <option value="<?= $sec['id'] ?>"><?= e($sec['course_code']) ?> - Sem <?= e($sec['semester_number']) ?> (<?= e($cleanSec) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Section 4: Document Upload -->
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent-color); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.45rem;">
            <?= icon('file-text', 'icon-xs') ?> 4. Document Attachments
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.25rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label class="form-label">Document Category</label>
                <select name="document_types[]" class="form-control">
                    <option value="aadhar">Aadhaar Card / Govt ID</option>
                    <option value="birth_cert">Birth Certificate</option>
                    <option value="tc">Transfer Certificate (TC)</option>
                    <option value="marksheet">Marksheet</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Upload File (PDF, JPG, PNG)</label>
                <input type="file" name="documents[]" class="form-control">
            </div>
        </div>

        <!-- System Action Note -->
        <div style="background: rgba(2, 132, 199, 0.08); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: var(--shadow-xs);">
            <strong style="color: var(--accent-color); font-size: 0.9375rem; display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('zap', 'icon-xs') ?> Automated System Provisioning:
            </strong>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0.35rem 0 0 0; line-height: 1.45;">
                Submitting this form will automatically generate the student record, assign academic placement, attach uploaded documents, and provision an Admin-managed login account (Username: Roll No, Default Password: <code>Student123!</code>).
            </p>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem; font-weight: 700;"><?= icon('user-check', 'icon-sm') ?> Execute Student Admission</button>
        </div>
    </form>
</div>
