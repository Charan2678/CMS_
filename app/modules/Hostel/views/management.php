<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🏢 Hostel Management &amp; Resident Allocations</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Manage hostel blocks, student booking verification, payment QR, and resident bed allocations</p>
    </div>
    <a href="/hostel" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.45rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.85rem; background: rgba(2, 132, 199, 0.05); border: 1.5px solid #0284c7; color: #0284c7; transition: all 0.2s ease;">
        ← Back to Hostel Overview
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- 1. STUDENT HOSTEL BOOKING REQUESTS (WARDEN VERIFICATION DESK) -->
<div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid var(--accent-color);">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                📥 Student Hostel Booking Requests &amp; Payment Verifications
            </h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary);">Review student room bookings, verify bank UTRs, and confirm or reject bed allotments.</span>
        </div>
        <span class="badge badge-info" style="font-size: 0.8125rem;">
            <?= count($bookingRequests ?? []) ?> Total Requests
        </span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="background: var(--bg-main); border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem 1rem;">Student Details</th>
                    <th style="padding: 0.75rem 1rem;">Hostel &amp; Room</th>
                    <th style="padding: 0.75rem 1rem;">Bed #</th>
                    <th style="padding: 0.75rem 1rem;">Fee</th>
                    <th style="padding: 0.75rem 1rem;">Transaction ID / UTR</th>
                    <th style="padding: 0.75rem 1rem;">Status</th>
                    <th style="padding: 0.75rem 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookingRequests)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No student hostel booking requests found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookingRequests as $req): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem 1rem;">
                                <strong style="color: var(--text-primary); font-size: 0.875rem;"><?= e($req['first_name'] . ' ' . $req['last_name']) ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;"><?= e($req['roll_number']) ?> &bull; <?= e($req['course_name'] ?? 'B.Tech') ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">📞 <?= e($req['student_mobile'] ?? 'N/A') ?></div>
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                <strong><?= e($req['block_name']) ?></strong>
                                <div style="font-size: 0.75rem; color: var(--accent-color); font-weight: 700;">Room <?= e($req['room_number']) ?> (<?= ucfirst(e($req['room_type'])) ?>)</div>
                            </td>
                            <td style="padding: 0.75rem 1rem; font-weight: 800; font-size: 0.9375rem; color: var(--accent-color);">
                                Bed <?= e($req['bed_number']) ?>
                            </td>
                            <td style="padding: 0.75rem 1rem; font-weight: 800; color: var(--success);">
                                ₹<?= number_format((float)$req['hostel_fee'], 2) ?>
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                <code><?= e($req['transaction_id'] ?: 'Pending UTR') ?></code>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Date: <?= $req['payment_date'] ? e($req['payment_date']) : 'N/A' ?></div>
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                <?php if ($req['booking_status'] === 'confirmed'): ?>
                                    <span class="badge badge-success" style="font-size: 0.7rem;">CONFIRMED</span>
                                <?php elseif ($req['booking_status'] === 'payment_verification_pending'): ?>
                                    <span class="badge badge-info" style="font-size: 0.7rem;">VERIFICATION PENDING</span>
                                <?php elseif ($req['booking_status'] === 'payment_pending'): ?>
                                    <span class="badge badge-warning" style="font-size: 0.7rem;">PAYMENT PENDING</span>
                                <?php elseif ($req['booking_status'] === 'rejected'): ?>
                                    <span class="badge badge-danger" style="font-size: 0.7rem;" title="<?= e($req['rejection_reason'] ?? 'Rejected') ?>">REJECTED</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary" style="font-size: 0.7rem;"><?= strtoupper(e($req['booking_status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem 1rem;">
                                <?php if (in_array($req['booking_status'], ['payment_verification_pending', 'payment_pending'])): ?>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <!-- Confirm Form -->
                                        <form method="POST" action="/hostel/management" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="verify_booking">
                                            <input type="hidden" name="booking_id" value="<?= $req['id'] ?>">
                                            <input type="hidden" name="sub_action" value="confirm">
                                            <button type="submit" class="btn btn-sm btn-success" style="font-size: 0.7rem; font-weight: 700; padding: 0.3rem 0.6rem;">
                                                ✓ Verify &amp; Confirm
                                            </button>
                                        </form>

                                        <!-- Reject Button -->
                                        <button type="button" onclick="openRejectModal(<?= $req['id'] ?>, '<?= e(addslashes($req['first_name'] . ' ' . $req['last_name'])) ?>')" class="btn btn-sm btn-outline-danger" style="font-size: 0.7rem; font-weight: 700; padding: 0.3rem 0.6rem;">
                                            ✗ Reject
                                        </button>
                                    </div>
                                <?php elseif ($req['booking_status'] === 'confirmed'): ?>
                                    <span style="color: var(--success); font-size: 0.75rem; font-weight: 700;">✓ Allotted</span>
                                <?php elseif ($req['booking_status'] === 'rejected'): ?>
                                    <span style="color: var(--danger); font-size: 0.75rem; font-weight: 600;" title="<?= e($req['rejection_reason'] ?? '') ?>">Rejected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Forms Column -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Configurable Payment QR & UPI Form -->
        <div class="card" style="border-top: 4px solid #10b981;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                ⚙️ Hostel Payment QR &amp; UPI Configuration
            </div>
            <form method="POST" action="/hostel/management" style="padding: 1.25rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="update_qr_settings">

                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label class="form-label" style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem;">Hostel Payment QR Image Path</label>
                    <input type="text" name="qr_image" value="<?= e($paymentSettings['qr_image'] ?? '/assets/images/hostel_qr.png') ?>" class="form-control" required style="width: 100%; padding: 0.45rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.8125rem;">
                    <small style="color: var(--text-secondary); font-size: 0.68rem; display: block; margin-top: 0.15rem;">Replaceable image file path or URL for college QR code.</small>
                </div>

                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label class="form-label" style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem;">Hostel Payment UPI ID</label>
                    <input type="text" name="upi_id" value="<?= e($paymentSettings['upi_id'] ?? 'kec.hostel@upi') ?>" class="form-control" required style="width: 100%; padding: 0.45rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.8125rem; font-family: monospace;">
                </div>

                <div class="form-group" style="margin-bottom: 0.85rem;">
                    <label class="form-label" style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem;">Beneficiary Payee Name</label>
                    <input type="text" name="payee_name" value="<?= e($paymentSettings['payee_name'] ?? 'Kuppam Engineering College Hostel Account') ?>" class="form-control" required style="width: 100%; padding: 0.45rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.8125rem;">
                </div>

                <div class="form-group" style="margin-bottom: 1.15rem;">
                    <label class="form-label" style="display: block; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem;">Payment Instructions</label>
                    <textarea name="instructions" rows="2" class="form-control" style="width: 100%; padding: 0.45rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.8125rem;"><?= e($paymentSettings['instructions'] ?? 'Scan with GPay/PhonePe to pay hostel fee.') ?></textarea>
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; font-weight: 700; font-size: 0.8125rem;">
                    💾 Save Payment QR Settings
                </button>
            </form>
        </div>

        <!-- Allocate Student Form -->
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                🛌 Manual Student Bed Allocation
            </div>
            <form method="POST" action="/hostel/management" style="padding: 1.25rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="allocate_student">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Student *</label>
                    <select name="student_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="">-- Choose Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= e($s['roll_number']) ?> — <?= e($s['first_name'] . ' ' . $s['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Hostel Room *</label>
                    <select name="hostel_room_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="">-- Choose Room --</option>
                        <?php foreach ($rooms as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (int)$r['occupied_beds'] >= (int)$r['capacity'] ? 'disabled' : '' ?>>
                                <?= e($r['block_name']) ?> — Room <?= e($r['room_number']) ?> (Beds: <?= $r['occupied_beds'] ?>/<?= $r['capacity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Bed Allocation</button>
            </form>
        </div>

        <!-- Add Hostel Room Form -->
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                🔑 Add Hostel Room
            </div>
            <form method="POST" action="/hostel/management" style="padding: 1.25rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="create_room">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Hostel Block *</label>
                    <select name="hostel_block_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="">-- Choose Block --</option>
                        <?php foreach ($blocks as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Room Number *</label>
                    <input type="text" name="room_number" placeholder="e.g. A-302" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Bed Capacity</label>
                    <select name="capacity" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="2">2 Beds (Double)</option>
                        <option value="3">3 Beds (Triple)</option>
                        <option value="1">1 Bed (Single)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-secondary" style="width: 100%;">Create Room</button>
            </form>
        </div>
    </div>

    <!-- Allocation Table Column -->
    <div class="card">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
            Active Resident Allocations (<?= count($allocations) ?>)
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Hostel &amp; Room</th>
                        <th>Bed #</th>
                        <th>Allotted Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allocations)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                No resident allocations found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($allocations as $a): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--text-primary);"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></strong>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;"><?= e($a['roll_number']) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= e($a['block_name']) ?></span>
                                    <div style="font-weight: 600; margin-top: 0.2rem;">Room <?= e($a['room_number']) ?></div>
                                </td>
                                <td style="font-weight: 700;">Bed <?= (int)$a['bed_number'] ?></td>
                                <td style="font-size: 0.8125rem; color: var(--text-secondary);"><?= e($a['allotted_date']) ?></td>
                                <td>
                                    <?php if ($a['status'] === 'active'): ?>
                                        <span class="badge badge-success">Active Resident</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Vacated</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($a['status'] === 'active'): ?>
                                        <form method="POST" action="/hostel/management" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="vacate_student">
                                            <input type="hidden" name="allocation_id" value="<?= $a['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Vacate</button>
                                        </form>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- REJECT BOOKING MODAL -->
<div id="rejectBookingModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 14px; max-width: 440px; width: 100%; padding: 1.25rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--danger); margin: 0 0 0.5rem 0;">
            ❌ Reject Student Booking Request
        </h3>
        <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1rem 0;">
            Please specify the reason for rejecting <strong id="rejectStudentName" style="color: var(--text-primary);">-</strong>'s hostel booking.
        </p>

        <form method="POST" action="/hostel/management">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="verify_booking">
            <input type="hidden" name="sub_action" value="reject">
            <input type="hidden" name="booking_id" id="rejectBookingId">

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.35rem;">Rejection Reason *</label>
                <textarea name="rejection_reason" required placeholder="e.g. UTR transaction invalid or payment not received in bank account." rows="3" style="width: 100%; padding: 0.6rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-size: 0.8125rem;"></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary" style="flex: 1; font-weight: 700;">Cancel</button>
                <button type="submit" class="btn btn-danger" style="flex: 1; font-weight: 800;">Reject Request</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(bookingId, studentName) {
    document.getElementById('rejectBookingId').value = bookingId;
    document.getElementById('rejectStudentName').textContent = studentName;
    document.getElementById('rejectBookingModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectBookingModal').style.display = 'none';
}
</script>
