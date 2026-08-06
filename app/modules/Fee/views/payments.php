<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Fee Collection & Student Ledger</h2>
        <a href="/fee/assign" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem;">+ Mass Assign Fees</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/fee/payments" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
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
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Roll No</th>
                <th style="padding: 0.75rem;">Student Name</th>
                <th style="padding: 0.75rem;">Fee Category</th>
                <th style="padding: 0.75rem;">Course / Sem</th>
                <th style="padding: 0.75rem;">Final Amount</th>
                <th style="padding: 0.75rem;">Paid</th>
                <th style="padding: 0.75rem;">Status</th>
                <th style="padding: 0.75rem;">Collect Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($studentFees)): ?>
                <tr>
                    <td colspan="8" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No student fee records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($studentFees as $sf): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($sf['roll_number']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600;"><?= e($sf['first_name'] . ' ' . $sf['last_name']) ?></td>
                        <td style="padding: 0.75rem;"><?= e($sf['category_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sf['course_code']) ?> (Sem <?= e($sf['semester_number']) ?>)</td>
                        <td style="padding: 0.75rem; font-weight: 700;">₹<?= number_format((float)$sf['final_amount'], 2) ?></td>
                        <td style="padding: 0.75rem; color: #86efac;">₹<?= number_format((float)$sf['total_paid'], 2) ?></td>
                        <td style="padding: 0.75rem;">
                            <?php if ($sf['status'] === 'paid'): ?>
                                <span class="badge badge-success">PAID</span>
                            <?php elseif ($sf['status'] === 'partial'): ?>
                                <span class="badge badge-warning">PARTIAL</span>
                            <?php else: ?>
                                <span class="badge badge-danger">PENDING</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem;">
                            <?php if ($sf['status'] !== 'paid'): ?>
                                <button onclick="openPaymentModal(<?= $sf['id'] ?>, '<?= e($sf['first_name'] . ' ' . $sf['last_name']) ?>', <?= (float)$sf['final_amount'] - (float)$sf['total_paid'] ?>)" class="btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; width: auto;">Collect ₹</button>
                            <?php else: ?>
                                <span style="color: #86efac; font-size: 0.75rem; font-weight: 600;">Cleared</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal for Payment Collection -->
<div id="paymentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="auth-card" style="width: 100%; max-width: 440px;">
        <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Collect Fee Payment</h3>
        <p id="modalStudentName" style="color: #a5b4fc; font-weight: 600; margin-bottom: 1rem;"></p>

        <form method="POST" action="/fee/payments">
            <?= csrf_field() ?>
            <input type="hidden" id="modal_student_fee_id" name="student_fee_id" value="">

            <div class="form-group">
                <label class="form-label" for="amount_paid">Amount to Pay (₹) *</label>
                <input type="number" step="0.01" id="amount_paid" name="amount_paid" class="form-control" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label class="form-label" for="payment_method">Payment Method *</label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="cash">Cash</option>
                    <option value="online">Online / UPI / NetBanking</option>
                    <option value="dd">Demand Draft (DD)</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="transaction_id">Transaction Reference / DD / Cheque No</label>
                <input type="text" id="transaction_id" name="transaction_id" class="form-control" placeholder="Ref No...">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" onclick="closePaymentModal()" class="btn-primary" style="background: var(--bg-surface); border: 1px solid var(--border-color);">Cancel</button>
                <button type="submit" class="btn-primary">Record & Print Receipt</button>
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
