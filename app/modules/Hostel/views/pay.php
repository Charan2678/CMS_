<?php
/**
 * Student Hostel Fee Payment & QR Code View — Kuppam Engineering College
 */
$st  = $studentProfile ?? [];
$bk  = $booking ?? [];
$ps  = $paymentSettings ?? [];
$fee = (float)($bk['hostel_fee'] ?? 25000.00);
?>

<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Breadcrumb Header -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                💳 Hostel Fee Payment
            </h1>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0;">
                Scan official Hostel UPI QR Code and submit your bank transaction UTR reference.
            </p>
        </div>
        <a href="/hostel/booking" class="btn btn-sm btn-outline-secondary" style="font-weight: 700; font-size: 0.75rem;">
            ← Back to Booking
        </a>
    </div>

    <!-- Booking Details Summary Header Card -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <div style="font-weight: 800; font-size: 1.05rem; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <span>📋 Hostel Allotment Summary</span>
            <span class="badge badge-warning" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">UNPAID</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.8125rem;">
            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Student Name &amp; ID:</span>
                <strong><?= e(auth_name()) ?></strong> (<code><?= e($st['roll_number'] ?? $_SESSION['username'] ?? '2026-CSE-001') ?></code>)
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Hostel &amp; Room:</span>
                <strong><?= e($bk['block_name'] ?? 'Boys Hostel A') ?></strong> (Room <?= e($bk['room_number'] ?? '101') ?>, Bed <?= e($bk['bed_number'] ?? '1') ?>)
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Academic Session:</span>
                <strong>Academic Year 2026-2027 (Semester 1)</strong>
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Total Amount Payable:</span>
                <strong style="color: var(--success); font-size: 1.1rem;">₹<?= number_format($fee, 2) ?></strong>
            </div>
        </div>
    </div>

    <!-- QR Code & Transaction Submission Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        <!-- QR Code Display Card -->
        <div class="card" style="text-align: center; border-top: 4px solid #10b981;">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                📱 Scan QR Code to Pay Hostel Fee
            </h3>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1rem 0;">
                <?= e($ps['instructions'] ?? 'Scan with PhonePe, Google Pay, Paytm, or BHIM UPI') ?>
            </p>

            <!-- Configurable Payment QR Code Image -->
            <div style="display: inline-block; background: #ffffff; padding: 1.25rem; border-radius: 16px; border: 2px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.08); margin-bottom: 1.25rem;">
                <img src="<?= e($ps['qr_image'] ?? '/assets/images/hostel_qr.png') ?>?v=<?= time() ?>" alt="KEC Hostel Fee Payment QR Code" style="width: 220px; height: 220px; display: block; object-fit: contain; border-radius: 6px;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-top: 0.5rem;">
                    Official Institutional QR
                </div>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.875rem; font-size: 0.8125rem; text-align: left; display: flex; flex-direction: column; gap: 0.35rem;">
                <div><strong>UPI ID:</strong> <code style="color: var(--accent-color); font-weight: 700;"><?= e($ps['upi_id'] ?? 'kec.hostel@upi') ?></code></div>
                <div><strong>Beneficiary:</strong> <?= e($ps['payee_name'] ?? 'Kuppam Engineering College Hostel Account') ?></div>
                <div><strong>Purpose:</strong> Hostel Fee (Room <?= e($bk['room_number'] ?? '101') ?> Bed <?= e($bk['bed_number'] ?? '1') ?>)</div>
                <div><strong>Student ID:</strong> <code><?= e($st['roll_number'] ?? $_SESSION['username'] ?? '2026-CSE-001') ?></code></div>
            </div>
        </div>

        <!-- Transaction Reference Submission Card -->
        <div class="card" style="border-top: 4px solid var(--accent-color);">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                📝 Confirm Payment Transaction
            </h3>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1.25rem 0;">
                Enter your 12-digit UPI reference / bank UTR after making the payment for Warden counter verification.
            </p>

            <form method="POST" action="/hostel/pay">
                <?= csrf_field() ?>
                <div style="margin-bottom: 1.15rem;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem;">
                        12-Digit Bank UTR / UPI Transaction Reference *
                    </label>
                    <input type="text" name="transaction_id" required placeholder="e.g. 423598765432" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-family: monospace; font-size: 0.9375rem; letter-spacing: 0.05em;">
                    <small style="color: var(--text-secondary); font-size: 0.7rem; display: block; margin-top: 0.25rem;">
                        Found on GPay / PhonePe payment success screen under 'UPI Transaction ID'.
                    </small>
                </div>

                <div style="margin-bottom: 1.15rem;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem;">
                        Payment Date *
                    </label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.6rem 0.85rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem;">
                        Amount Paid (₹) *
                    </label>
                    <input type="number" step="0.01" name="amount" value="<?= $fee ?>" required style="width: 100%; padding: 0.6rem 0.85rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-weight: 700; font-size: 0.9375rem;">
                </div>

                <div style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 0.85rem; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                    <strong>ℹ️ Verification Note:</strong> Your booking status will change to <code>Payment Verification Pending</code>. Warden will confirm your bed allotment after verifying the UTR.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 800; font-size: 0.9375rem;">
                    🚀 Submit Payment for Warden Verification
                </button>
            </form>
        </div>
    </div>
</div>
