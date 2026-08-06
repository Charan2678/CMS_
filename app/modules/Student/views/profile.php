<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<!-- Top Profile Header Card -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--accent-color);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <!-- Student Avatar Container with Interactive Photo Upload -->
            <div style="position: relative; text-align: center;">
                <?php if (!empty($student['photo_path'])): ?>
                    <img src="<?= e($student['photo_path']) ?>" alt="Profile Photo" style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-color); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <?php else: ?>
                    <div style="width: 90px; height: 90px; border-radius: 50%; background: rgba(2, 132, 199, 0.15); color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; border: 3px solid var(--accent-color);">
                        👨‍🎓
                    </div>
                <?php endif; ?>

                <!-- Photo Upload Trigger Button -->
                <form method="POST" action="/profile" enctype="multipart/form-data" style="margin-top: 0.5rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="upload_photo">
                    <label for="profile_photo_input" class="btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                        <span>📷</span> Upload Photo
                    </label>
                    <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*" style="display: none;" onchange="this.form.submit()">
                </form>
            </div>

            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <?= e($student['first_name'] . ' ' . $student['last_name']) ?>
                    <span class="badge badge-success" style="font-size: 0.75rem;">Active Student</span>
                </h1>
                <div style="font-size: 0.9375rem; color: var(--accent-color); font-weight: 700; margin-top: 0.25rem;">
                    Roll No: <?= e($student['roll_number']) ?> &bull; Admission No: <?= e($student['admission_number']) ?>
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    <?= e($student['course_name'] ?? 'B.Tech') ?> &bull; <?= e($student['department_name'] ?? 'Computer Science') ?> &bull; Semester <?= e($student['semester_number'] ?? '1') ?> (Section <?= e($student['section_name'] ?? 'A') ?>)
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 1: Personal Details & Documents Center -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Personal & Contact Information Form -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>👤</span> Personal & Contact Details
        </h2>

        <form method="POST" action="/profile">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="update_profile">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" required value="<?= e($student['first_name']) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required value="<?= e($student['last_name']) ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Mobile Phone Number *</label>
                    <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile number" value="<?= e($student['mobile'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Personal Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="student@example.com" value="<?= e($student['email'] ?? '') ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" required value="<?= e($student['date_of_birth']) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control">
                        <option value="male" <?= $student['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $student['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= $student['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-control">
                        <?php $bg = $student['blood_group'] ?? ''; ?>
                        <option value="A+" <?= $bg === 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= $bg === 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= $bg === 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= $bg === 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="O+" <?= $bg === 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= $bg === 'O-' ? 'selected' : '' ?>>O-</option>
                        <option value="AB+" <?= $bg === 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= $bg === 'AB-' ? 'selected' : '' ?>>AB-</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Permanent Residential Address</label>
                <input type="text" name="address_line1" class="form-control" placeholder="House No, Street, Landmark" value="<?= e($student['address_line1'] ?? '') ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?= e($student['city'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?= e($student['state'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Pincode</label>
                    <input type="text" name="pincode" class="form-control" value="<?= e($student['pincode'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn-primary">Save Personal Details</button>
        </form>
    </div>

    <!-- Document Upload & Management Right Column -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Upload Document Panel -->
        <div class="card">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>📤</span> Upload Document
            </h2>

            <form method="POST" action="/profile" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="upload_document">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Document Category *</label>
                    <select name="document_type" class="form-control" required>
                        <option value="aadhar">Aadhaar Card / Govt ID</option>
                        <option value="marksheet">10th / 12th / Diploma Marksheet</option>
                        <option value="birth_cert">Birth Certificate</option>
                        <option value="tc">Transfer Certificate (TC)</option>
                        <option value="other">Passport Photo / Other Document</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Document Title / Description *</label>
                    <input type="text" name="document_name" class="form-control" required placeholder="e.g. 12th Class Board Marksheet">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label">Choose File (PDF, JPG, PNG) *</label>
                    <input type="file" name="document_file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Upload File</button>
            </form>
        </div>

        <!-- Attached Documents List -->
        <div class="card">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>📎</span> My Uploaded Documents
            </h2>

            <?php if (empty($student['documents'])): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No documents uploaded yet.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($student['documents'] as $doc): ?>
                        <div style="background: var(--bg-main); padding: 0.875rem 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.875rem; color: var(--text-primary);"><?= e($doc['document_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; margin-top: 0.15rem;">
                                    Type: <?= e($doc['document_type']) ?> &bull; <?= date('d M Y', strtotime($doc['created_at'])) ?>
                                </div>
                            </div>
                            <a href="<?= e($doc['file_path']) ?>" target="_blank" class="badge badge-info" style="text-decoration: none;">Download</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Section 2: 2-Column Grid — Parent Information Card (Left) & Change Password Card (Right) -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Parent & Guardian Details Card (Left) -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>👨‍👩‍👧</span> Parent & Guardian Information
        </h2>

        <?php 
            $primaryG = !empty($student['guardians']) ? $student['guardians'][0] : [];
        ?>
        <form method="POST" action="/profile">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="save_guardian">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Parent / Guardian Full Name *</label>
                    <input type="text" name="guardian_name" class="form-control" required placeholder="e.g. Ramesh Kumar" value="<?= e($primaryG['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Relationship *</label>
                    <select name="guardian_relationship" class="form-control">
                        <?php $rel = $primaryG['relationship'] ?? 'father'; ?>
                        <option value="father" <?= $rel === 'father' ? 'selected' : '' ?>>Father</option>
                        <option value="mother" <?= $rel === 'mother' ? 'selected' : '' ?>>Mother</option>
                        <option value="guardian" <?= $rel === 'guardian' ? 'selected' : '' ?>>Guardian</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Parent Mobile Number *</label>
                    <input type="text" name="guardian_mobile" class="form-control" placeholder="10-digit mobile number" value="<?= e($primaryG['mobile'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Parent Email Address</label>
                    <input type="email" name="guardian_email" class="form-control" placeholder="parent@example.com" value="<?= e($primaryG['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Parent Occupation</label>
                <input type="text" name="guardian_occupation" class="form-control" placeholder="e.g. Business / Government Service / Engineer" value="<?= e($primaryG['occupation'] ?? '') ?>">
            </div>

            <button type="submit" class="btn-primary">Save Parent Information</button>
        </form>
    </div>

    <!-- Account Security & Change Password Card (Right, beside Parent Card) -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🔒</span> Account Security & Password
        </h2>

        <form method="POST" action="/profile">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="change_password">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Current Password *</label>
                <input type="password" name="current_password" class="form-control" required placeholder="Enter current password">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">New Password *</label>
                <input type="password" name="new_password" class="form-control" required placeholder="At least 8 chars, 1 upper, 1 number">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Confirm New Password *</label>
                <input type="password" name="confirm_password" class="form-control" required placeholder="Re-enter new password">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Update Password</button>
        </form>
    </div>
</div>
