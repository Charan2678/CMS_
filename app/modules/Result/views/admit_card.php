<?php
$eligibility = $data['eligibility'] ?? [];
$student     = $data['student'] ?? [];
$timetable   = $data['timetable'] ?? [];
$htNumber    = $data['ht_number'] ?? '';
$isEligible  = $eligibility['is_eligible'] ?? false;
$status      = $eligibility['status'] ?? 'blocked';
?>

<div style="max-width: 900px; margin: 0 auto;">
    <!-- Print CSS -->
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .panel { border: 1px solid #000 !important; box-shadow: none !important; }
            .admit-card-container { margin: 0 !important; padding: 0 !important; }
        }
    </style>

    <div class="no-print" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">🎓 Semester Examination Admit Card / Hall Ticket</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Official hall ticket required for entry into examination halls.</p>
        </div>
        <?php if ($isEligible): ?>
            <button onclick="window.print()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
                <span>🖨️</span> Print / Download Admit Card
            </button>
        <?php endif; ?>
    </div>

    <?php if (!$isEligible): ?>
        <!-- Blocked Alert Card -->
        <div class="panel" style="border-left: 5px solid #ef4444; background: rgba(239, 68, 68, 0.05); padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="font-size: 2.5rem; line-height: 1;">🛑</div>
                <div>
                    <h2 style="color: #ef4444; font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0;">Admit Card Blocked — Exam Ineligible</h2>
                    <p style="margin: 0 0 1rem 0; color: var(--text-primary); font-size: 0.9375rem;">
                        You are currently blocked from accessing your examination hall ticket due to unmet academic or fee criteria:
                    </p>
                    <ul style="margin: 0 0 1.25rem 1.25rem; padding: 0; color: #dc2626; font-weight: 600;">
                        <?php foreach (($eligibility['reasons'] ?? []) as $reason): ?>
                            <li style="margin-bottom: 0.35rem;"><?= e($reason) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <?php if (($eligibility['pending_dues'] ?? 0) > 0): ?>
                            <a href="/fee/payments" class="btn btn-primary btn-sm">💳 Clear Pending Dues Online</a>
                        <?php endif; ?>
                        <a href="/profile" class="btn btn-secondary btn-sm">📞 Contact Department HOD</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Official Printable Hall Ticket -->
        <div class="panel admit-card-container" style="background: #ffffff; color: #0f172a; border: 2px solid #0f172a; padding: 2rem; border-radius: 8px;">
            <!-- Header Seal & Title -->
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 60px; height: 60px; background: #0284c7; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.5rem;">
                        KEC
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.35rem; font-weight: 800; text-transform: uppercase; color: #0f172a;">Kuppam Engineering College</h2>
                        <p style="margin: 2px 0 0 0; font-size: 0.8125rem; color: #475569;">Approved by AICTE, New Delhi & Affiliated to JNTUA | Accredited NAAC A Grade</p>
                        <p style="margin: 2px 0 0 0; font-size: 0.875rem; font-weight: 700; color: #0284c7;">END SEMESTER REGULAR EXAMINATION HALL TICKET</p>
                    </div>
                </div>
                <!-- Verification QR Code representation -->
                <div style="text-align: center; border: 1px solid #cbd5e1; padding: 6px; border-radius: 6px; background: #f8fafc;">
                    <div style="width: 70px; height: 70px; background: #000; display: flex; align-items: center; justify-content: center; color: #fff; font-family: monospace; font-size: 9px; line-height: 1;">
                        [QR CODE]<br><?= e(substr($htNumber, -6)) ?>
                    </div>
                    <span style="font-size: 9px; font-weight: bold; display: block; margin-top: 3px;">VERIFIED</span>
                </div>
            </div>

            <!-- Student Profile Information Grid -->
            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                <div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b; width: 35%;">Hall Ticket No:</td>
                            <td style="padding: 4px 8px; font-weight: bold; color: #0284c7; font-family: monospace; font-size: 1rem;"><?= e($htNumber) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b;">Roll Number:</td>
                            <td style="padding: 4px 8px; font-weight: bold;"><?= e($student['roll_number']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b;">Student Name:</td>
                            <td style="padding: 4px 8px; font-weight: bold; text-transform: uppercase;"><?= e($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b;">Course & Dept:</td>
                            <td style="padding: 4px 8px;"><?= e($student['course_code']) ?> — <?= e($student['dept_name']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b;">Semester & Section:</td>
                            <td style="padding: 4px 8px;">Semester <?= e($student['sem_number']) ?> (Section <?= e($student['section_name']) ?>)</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 8px; font-weight: bold; color: #64748b;">Attendance Clearance:</td>
                            <td style="padding: 4px 8px;">
                                <span style="color: #16a34a; font-weight: bold;">
                                    <?= number_format($eligibility['attendance_pct'], 1) ?>% 
                                    <?= ($status === 'condoned') ? '(Condoned)' : '✓ Verified Eligible' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Photo Box -->
                <div style="text-align: center;">
                    <div style="width: 105px; height: 125px; border: 2px solid #0f172a; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <?php if (!empty($student['photo_path'])): ?>
                            <img src="<?= e($student['photo_path']) ?>" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 2.5rem;">👨‍🎓</span>
                        <?php endif; ?>
                    </div>
                    <span style="font-size: 10px; color: #64748b; margin-top: 4px; display: block;">Student Signature</span>
                </div>
            </div>

            <!-- Examination Timetable Grid -->
            <h3 style="font-size: 0.9375rem; font-weight: 700; margin: 0 0 0.75rem 0; text-transform: uppercase; color: #0f172a;">Registered Examination Schedule</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; margin-bottom: 2rem;">
                <thead>
                    <tr style="background: #0f172a; color: white; text-align: left;">
                        <th style="padding: 8px 12px; border: 1px solid #0f172a;">#</th>
                        <th style="padding: 8px 12px; border: 1px solid #0f172a;">Subject Code</th>
                        <th style="padding: 8px 12px; border: 1px solid #0f172a;">Subject Name</th>
                        <th style="padding: 8px 12px; border: 1px solid #0f172a;">Type</th>
                        <th style="padding: 8px 12px; border: 1px solid #0f172a;">Schedule Day</th>
                        <th style="padding: 8px 12px; border: 1px solid #0f172a; text-align: center;">Invigilator Sign</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($timetable)): ?>
                        <tr><td colspan="6" style="padding: 12px; text-align: center; color: #64748b;">No subject exams scheduled for this semester.</td></tr>
                    <?php else: ?>
                        <?php foreach ($timetable as $idx => $sub): ?>
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;"><?= $idx + 1 ?></td>
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: bold; font-family: monospace;"><?= e($sub['code']) ?></td>
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; font-weight: 600;"><?= e($sub['name']) ?></td>
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1; text-transform: capitalize;"><?= e($sub['type']) ?></td>
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;"><?= e(ucfirst($sub['day_of_week'] ?? 'As Announced')) ?></td>
                                <td style="padding: 8px 12px; border: 1px solid #cbd5e1;"></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Instructions & Signatures -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; border-top: 1px dashed #cbd5e1; padding-top: 1.5rem; font-size: 0.75rem; color: #475569;">
                <div>
                    <strong>Candidate Instructions:</strong>
                    <ol style="margin: 4px 0 0 1rem; padding: 0;">
                        <li>Candidate must carry this Admit Card and Official College ID Card to every examination session.</li>
                        <li>Electronic devices, mobile phones, or smartwatches are strictly prohibited in the exam hall.</li>
                        <li>Candidates must be seated 15 minutes prior to commencement of examination.</li>
                    </ol>
                </div>
                <div style="text-align: center; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="font-family: 'Brush Script MT', cursive, sans-serif; font-size: 1.5rem; color: #0284c7; font-weight: bold;">Controller of Exams</div>
                        <div style="border-top: 1px solid #0f172a; margin-top: 10px; padding-top: 4px; font-weight: bold; color: #0f172a;">Controller of Examinations</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
