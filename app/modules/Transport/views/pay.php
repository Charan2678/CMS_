<?php
$st = $studentProfile ?? [];
$sub = $subscription ?? [];
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">💳 Transport Fee Payment (QR Code)</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Scan the college transport QR code to pay your annual transport fee and submit your transaction ID for verification</p>
    </div>
    <a href="/transport/routes" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Bus Routes</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Left Column: Subscription & Fee Overview -->
    <div class="card" style="border-top: 4px solid #2563eb;">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
            📋 Transport Subscription Details
        </div>
        <div style="padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
            <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Student Name:</span>
                    <strong style="color: var(--text-primary);"><?= e(($st['first_name'] ?? 'Student') . ' ' . ($st['last_name'] ?? '')) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Student Roll / ID:</span>
                    <strong style="font-family: monospace; color: var(--accent-color);"><?= e($st['roll_number'] ?? '2026-CSE-001') ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Department:</span>
                    <strong><?= e($st['department_name'] ?? 'Computer Science') ?></strong>
                </div>
            </div>

            <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Selected Route:</span>
                    <strong style="color: var(--text-primary);"><?= e($sub['route_name']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Route Code:</span>
                    <span class="badge badge-info"><?= e($sub['route_code']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Bus Number:</span>
                    <strong style="color: #2563eb;"><?= e($sub['bus_number']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Pickup Point &amp; Time:</span>
                    <strong><?= e($sub['pickup_point']) ?> (<?= e($sub['pickup_time']) ?>)</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Drop Point:</span>
                    <strong><?= e($sub['drop_point']) ?></strong>
                </div>
            </div>

            <div style="background: rgba(37, 99, 235, 0.08); padding: 1rem; border-radius: 10px; border: 1px solid rgba(37, 99, 235, 0.25); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700; text-transform: uppercase;">AMOUNT PAYABLE</span>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #2563eb;">₹<?= number_format((float)$sub['annual_fee'], 2) ?></div>
                </div>
                <span class="badge badge-warning" style="background:#f59e0b; color:#fff; font-size: 0.8125rem; padding: 0.4rem 0.75rem;">
                    Status: <?= strtoupper(e($sub['payment_status'])) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Right Column: QR Code & Transaction ID Form -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- College Transport QR Code Card -->
        <div class="card" style="text-align: center; padding: 1.5rem; border-top: 4px solid #10b981;">
            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                📱 Scan QR Code to Pay Transport Fee
            </h3>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1rem 0;">
                Use PhonePe, Google Pay, Paytm, or any UPI App
            </p>

            <!-- Official Transport Payment QR Code -->
            <div style="display: inline-block; background: #ffffff; padding: 1rem; border-radius: 12px; border: 2px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 1rem;">
                <img src="/assets/images/transport_qr.png?v=<?= time() ?>" alt="KEC Transport Fee Payment QR Code" style="width: 220px; height: 220px; display: block; object-fit: contain; border-radius: 6px;">
            </div>


            <div style="font-size: 0.8125rem; background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); text-align: left; display: flex; flex-direction: column; gap: 0.35rem;">
                <div><strong>UPI ID:</strong> <span style="font-family: monospace; color: var(--accent-color);">kec.transport@upi</span></div>
                <div><strong>Beneficiary:</strong> Kuppam Engineering College Transport Account</div>
                <div><strong>Purpose:</strong> Transport Fee (<?= e($sub['route_code']) ?>)</div>
            </div>
        </div>

        <!-- Transaction ID Submission Form -->
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                📝 Submit Payment Confirmation (UTR / Transaction ID)
            </div>
            <form method="POST" action="/transport/pay" style="padding: 1.25rem;">
                <?= csrf_field() ?>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Transaction ID / UTR Number *</label>
                    <input type="text" name="transaction_id" placeholder="e.g. UTR123456789012" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Payment Date *</label>
                    <input type="date" name="payment_date" value="<?= date('Y-m-d') ?>" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Amount Paid (₹) *</label>
                    <input type="number" name="amount" value="<?= (float)$sub['annual_fee'] ?>" step="100" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px; font-weight: 700;">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%; background: #10b981; border-color: #10b981; font-weight: 800; padding: 0.65rem;">
                    Submit Payment for Verification 💳
                </button>
            </form>
        </div>
    </div>
</div>
