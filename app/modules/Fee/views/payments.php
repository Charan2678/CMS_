<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($pendingVerifications)): ?>
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid #f59e0b; background: rgba(245, 158, 11, 0.03);">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
        <span style="display: flex; align-items: center; gap: 0.5rem;"><span>📱</span> Pending UPI / QR Verifications (<?= count($pendingVerifications) ?> Requests)</span>
        <span class="badge badge-warning">Action Required</span>
    </h2>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                    <th style="padding: 0.65rem 0.75rem;">Transaction Ref</th>
                    <th style="padding: 0.65rem 0.75rem;">Student</th>
                    <th style="padding: 0.65rem 0.75rem;">Fee Type</th>
                    <th style="padding: 0.65rem 0.75rem;">Amount</th>
                    <th style="padding: 0.65rem 0.75rem;">Bank UTR / Ref No</th>
                    <th style="padding: 0.65rem 0.75rem;">Submitted At</th>
                    <th style="padding: 0.65rem 0.75rem; text-align: center;">Counter Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingVerifications as $pv): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-family: monospace; font-weight: 700; color: var(--accent-color);"><?= e($pv['transaction_reference']) ?></td>
                        <td style="padding: 0.75rem;">
                            <div style="font-weight: 700; color: var(--text-primary);"><?= e($pv['first_name'] . ' ' . $pv['last_name']) ?></div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= e($pv['roll_number']) ?></div>
                        </td>
                        <td style="padding: 0.75rem;"><span class="badge badge-info" style="text-transform: uppercase;"><?= e($pv['fee_type']) ?></span></td>
                        <td style="padding: 0.75rem; font-weight: 800; color: var(--success); font-size: 0.9375rem;">₹<?= number_format((float)$pv['amount'], 2) ?></td>
                        <td style="padding: 0.75rem;">
                            <code style="background: rgba(245, 158, 11, 0.15); color: #d97706; padding: 3px 8px; border-radius: 4px; font-weight: 700;"><?= e($pv['utr_reference']) ?></code>
                        </td>
                        <td style="padding: 0.75rem; color: var(--text-secondary); white-space: nowrap;"><?= date('d M, h:i A', strtotime($pv['created_at'])) ?></td>
                        <td style="padding: 0.75rem; text-align: center;">
                            <form method="POST" action="/fee/payments" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_action" value="verify_utr">
                                <input type="hidden" name="transaction_id" value="<?= $pv['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-success" style="padding: 0.35rem 0.75rem; font-weight: 700; font-size: 0.75rem;">
                                    ✅ Confirm &amp; Post Receipt
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>💳</span> Fee Collection & Student Financial Ledger
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Process payments, generate receipts, and track outstanding balances
            </div>
        </div>

        <a href="/fee/assign" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.625rem 1.25rem; font-weight: 700;">
            + Mass Assign Fees
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/fee/payments" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end; background: var(--bg-main); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--border-color);">
        <div>
            <label class="form-label">Search Student</label>
            <input type="text" name="search" class="form-control" placeholder="Roll No, Name..." value="<?= e($filters['search']) ?>">
        </div>

        <div>
            <label class="form-label">Payment Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : '' ?>>Partial Paid</option>
                <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Fully Paid</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Filter Ledger</button>
        </div>
    </form>

    <!-- Student Fees Ledger Table -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Fee Category</th>
                    <th>Course / Sem</th>
                    <th>Final Amount</th>
                    <th>Paid Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($studentFees)): ?>
                    <tr>
                        <td colspan="8" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No student fee records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($studentFees as $sf): ?>
                        <tr>
                            <td style="font-weight: 800; color: var(--accent-color);"><?= e($sf['roll_number']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($sf['first_name'] . ' ' . $sf['last_name']) ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($sf['category_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($sf['course_code']) ?> (Sem <?= e($sf['semester_number']) ?>)</td>
                            <td style="font-weight: 700; color: var(--text-primary);">₹<?= number_format((float)$sf['final_amount'], 2) ?></td>
                            <td style="font-weight: 700; color: var(--success);">₹<?= number_format((float)$sf['total_paid'], 2) ?></td>
                            <td>
                                <?php if ($sf['status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID</span>
                                <?php elseif ($sf['status'] === 'partial'): ?>
                                    <span class="badge badge-warning">PARTIAL</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">PENDING</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sf['status'] !== 'paid'): ?>
                                    <button onclick="openPaymentModal(<?= $sf['id'] ?>, '<?= e($sf['first_name'] . ' ' . $sf['last_name']) ?>', <?= (float)$sf['final_amount'] - (float)$sf['total_paid'] ?>)" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; border-radius: 6px; width: auto;">Collect ₹</button>
                                <?php else: ?>
                                    <span class="badge badge-success" style="font-size: 0.75rem;">Cleared</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Payment Collection -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 100%; max-width: 440px; border-top: 4px solid var(--accent-color);">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 0.75rem;">Collect Fee Payment</h3>
        <p id="modalStudentName" style="color: var(--accent-color); font-weight: 700; margin-bottom: 1.25rem; font-size: 0.875rem;"></p>

        <form method="POST" action="/fee/payments">
            <?= csrf_field() ?>
            <input type="hidden" id="modal_student_fee_id" name="student_fee_id" value="">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="amount_paid">Amount to Pay (₹) *</label>
                <input type="number" step="0.01" id="amount_paid" name="amount_paid" class="form-control" required placeholder="0.00">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="payment_method">Payment Method *</label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="cash">Cash</option>
                    <option value="online">Online / UPI / NetBanking</option>
                    <option value="dd">Demand Draft (DD)</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="transaction_id">Transaction Ref / DD / Cheque No</label>
                <input type="text" id="transaction_id" name="transaction_id" class="form-control" placeholder="Ref No...">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="closePaymentModal()" class="btn-primary" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); width: 40%;">Cancel</button>
                <button type="submit" class="btn-primary" style="width: 60%;">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(feeId, studentName, balanceDue) {
    document.getElementById('modal_student_fee_id').value = feeId;
    document.getElementById('modalStudentName').innerText = studentName + ' (Balance Due: ₹' + balanceDue.toFixed(2) + ')';
    document.getElementById('amount_paid').value = balanceDue.toFixed(2);
    document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>
