<?php
/**
 * Parent Portal — Ward Hostel Details & Payment Status View
 */
$hd = $hostelDetails ?? null;
$hist = $history ?? [];
$st = $studentProfile ?? [];
$wardName = trim(($st['first_name'] ?? '') . ' ' . ($st['last_name'] ?? '')) ?: ($_SESSION['ward_name'] ?? 'Ward');
?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Banner Header -->
    <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(14, 165, 233, 0.1) 100%); border: 1px solid rgba(37, 99, 235, 0.3); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--accent-color); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; font-weight: 700;">
                🏠
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-color); font-weight: 700;">Parent &amp; Guardian Oversight</div>
                <h2 style="margin: 0.15rem 0 0 0; font-size: 1.2rem; font-weight: 800; color: var(--text-primary);">
                    Student Hostel Details: <?= e($wardName) ?>
                </h2>
                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.15rem;">
                    Roll Number: <strong style="color: var(--text-primary);"><?= e($st['roll_number'] ?? $_SESSION['ward_roll_number'] ?? '2026-CSE-001') ?></strong>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="/hostel" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.45rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.85rem; background: rgba(2, 132, 199, 0.05); border: 1.5px solid #0284c7; color: #0284c7; transition: all 0.2s ease;">
                ← Back to Hostel Overview
            </a>
            <span class="badge badge-info" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">🔒 Read-Only Parent View</span>
        </div>
    </div>

    <!-- Active Hostel Allotment Card -->
    <?php if ($hd): ?>
        <div class="card" style="border-top: 4px solid var(--accent-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                    🏡 Current Hostel Residence
                </h3>
                <?php if ($hd['booking_status'] === 'confirmed'): ?>
                    <span class="badge badge-success" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">CONFIRMED &amp; ACTIVE</span>
                <?php elseif ($hd['booking_status'] === 'payment_verification_pending'): ?>
                    <span class="badge badge-info" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">VERIFICATION PENDING</span>
                <?php else: ?>
                    <span class="badge badge-warning" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">PAYMENT PENDING</span>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">STUDENT</div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                        <?= e($wardName) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">ID: <?= e($st['roll_number'] ?? '2026-CSE-001') ?></div>
                </div>

                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">HOSTEL &amp; BLOCK</div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                        <?= e($hd['block_name']) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">Type: <?= ucfirst(e($hd['gender_type'])) ?> Hostel</div>
                </div>

                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">ROOM &amp; BED NUMBER</div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--accent-color); margin-top: 0.25rem;">
                        Room <?= e($hd['room_number']) ?> (Bed <?= e($hd['bed_number']) ?>)
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">Type: <?= ucfirst(e($hd['room_type'])) ?></div>
                </div>

                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">HOSTEL FEE &amp; PAYMENT</div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--success); margin-top: 0.25rem;">
                        ₹<?= number_format((float)$hd['hostel_fee'], 2) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                        Status: <strong style="color: <?= $hd['payment_status'] === 'paid' ? 'var(--success)' : '#d97706' ?>;"><?= strtoupper(e($hd['payment_status'])) ?></strong>
                    </div>
                </div>

                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">WARDEN IN-CHARGE</div>
                    <div style="font-size: 0.9375rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                        <?= e($hd['warden_name']) ?>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">📞 Contact: <?= e($hd['warden_phone']) ?></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-secondary);">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏠</div>
            <div style="font-weight: 700; font-size: 1rem;">No Active Hostel Room Allotted</div>
            <p style="font-size: 0.8125rem; margin-top: 0.25rem;">Your ward has not booked or been assigned a hostel room for the current academic session.</p>
        </div>
    <?php endif; ?>

    <!-- Parent Payment Visibility Table -->
    <div class="card">
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                💳 Ward Hostel Fee &amp; Payment History
            </h3>
        </div>

        <?php if (empty($hist)): ?>
            <div style="text-align: center; padding: 1.5rem; color: var(--text-secondary); font-size: 0.8125rem;">
                No payment history records found for your ward.
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: var(--bg-main); border-bottom: 2px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem 1rem;">Academic Year</th>
                            <th style="padding: 0.75rem 1rem;">Semester</th>
                            <th style="padding: 0.75rem 1rem;">Hostel Block</th>
                            <th style="padding: 0.75rem 1rem;">Room &amp; Bed</th>
                            <th style="padding: 0.75rem 1rem;">Hostel Fee</th>
                            <th style="padding: 0.75rem 1rem;">Payment Date</th>
                            <th style="padding: 0.75rem 1rem;">Transaction UTR</th>
                            <th style="padding: 0.75rem 1rem;">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hist as $h): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem 1rem; font-weight: 700;"><?= e($h['academic_year']) ?></td>
                                <td style="padding: 0.75rem 1rem;"><?= e($h['semester']) ?></td>
                                <td style="padding: 0.75rem 1rem; font-weight: 700;"><?= e($h['block_name']) ?></td>
                                <td style="padding: 0.75rem 1rem; color: var(--accent-color); font-weight: 700;">
                                    Room <?= e($h['room_number']) ?> (Bed <?= e($h['bed_number']) ?>)
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 800; color: var(--success);">
                                    ₹<?= number_format((float)$h['hostel_fee'], 2) ?>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?= $h['payment_date'] ? date('d M Y', strtotime($h['payment_date'])) : 'N/A' ?>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <code><?= e($h['transaction_id'] ?: 'N/A') ?></code>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?php if ($h['payment_status'] === 'paid'): ?>
                                        <span class="badge badge-success" style="font-size: 0.7rem;">PAID</span>
                                    <?php elseif ($h['payment_status'] === 'verification_pending'): ?>
                                        <span class="badge badge-info" style="font-size: 0.7rem;">VERIFICATION PENDING</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="font-size: 0.7rem;">UNPAID</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
