<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">💰 Accounts &amp; Transport Fees Ledger</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Comprehensive student fee collection ledger, transaction verification, receipts, and payment approvals</p>
    </div>
    <a href="/transport" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Transport Overview</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem; text-align: center;">
        <div style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 600;">TOTAL TRANSPORT STUDENTS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= number_format($paymentSum['total_students']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #10b981; cursor: pointer;" onclick="filterStatus('PAID')">
        <div style="font-size: 0.8125rem; color: #10b981; font-weight: 600;">PAID STUDENTS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #10b981; margin-top: 0.2rem;"><?= number_format($paymentSum['paid_students']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #f59e0b; cursor: pointer;" onclick="filterStatus('PAYMENT VERIFICATION PENDING')">
        <div style="font-size: 0.8125rem; color: #f59e0b; font-weight: 600;">VERIFICATION PENDING</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #d97706; margin-top: 0.2rem;"><?= number_format($paymentSum['partially_paid']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid var(--danger); cursor: pointer;" onclick="filterStatus('UNPAID')">
        <div style="font-size: 0.8125rem; color: var(--danger); font-weight: 600;">UNPAID STUDENTS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: var(--danger); margin-top: 0.2rem;"><?= number_format($paymentSum['unpaid_students']) ?></div>
    </div>
</div>

<!-- Complete Student Payment Ledger Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <span style="font-weight: 700;">Student Transport Payment Records &amp; Verifications</span>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <select id="statusFilter" class="form-control" onchange="filterRecords()" style="padding: 0.4rem 0.75rem; font-size: 0.8125rem; border: 1px solid var(--border-color); border-radius: 6px;">
                <option value="ALL">All Payment Statuses</option>
                <option value="PAID">PAID Only</option>
                <option value="PAYMENT VERIFICATION PENDING">PENDING VERIFICATION Only</option>
                <option value="UNPAID">UNPAID Only</option>
            </select>
            <input type="text" id="searchInput" placeholder="Search student or roll #..." class="form-control" onkeyup="filterRecords()" style="padding: 0.4rem 0.75rem; font-size: 0.8125rem; border: 1px solid var(--border-color); border-radius: 6px; width: 220px;">
            <button type="button" class="btn btn-primary" onclick="filterRecords()" style="font-size: 0.8125rem; padding: 0.4rem 0.875rem;">Filter</button>
            <button type="button" class="btn btn-secondary" onclick="resetFilter()" style="font-size: 0.8125rem; padding: 0.4rem 0.875rem;">Reset</button>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" id="ledgerTable">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Dept</th>
                    <th>Route &amp; Bus</th>
                    <th>Annual Fee</th>
                    <th>Amount Paid</th>
                    <th>Transaction ID</th>
                    <th>Payment Status</th>
                    <th>Actions / Verification</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studentStatus as $st): ?>
                    <tr class="ledger-row" 
                        data-id="<?= e($st['student_id']) ?>"
                        data-name="<?= e($st['name']) ?>"
                        data-dept="<?= e($st['department']) ?>"
                        data-route="<?= e($st['route']) ?>"
                        data-status="<?= e($st['status']) ?>">
                        
                        <td style="font-family: monospace; font-weight: 700; color: var(--accent-color);"><?= e($st['student_id']) ?></td>
                        <td style="font-weight: 700; color: var(--text-primary);"><?= e($st['name']) ?></td>
                        <td><span class="badge badge-info"><?= e($st['department']) ?></span></td>
                        <td style="font-size: 0.8125rem; color: var(--text-secondary);">
                            <strong><?= e($st['route_code'] ?? 'R-03') ?></strong> — <?= e($st['bus_number'] ?? 'BUS-03') ?>
                        </td>
                        <td style="font-weight: 600;">₹<?= number_format((float)$st['annual_fee'], 2) ?></td>
                        <td style="font-weight: 700; color: #10b981;">₹<?= number_format((float)$st['amount_paid'], 2) ?></td>
                        <td style="font-family: monospace; font-size: 0.8125rem;">
                            <?= !empty($st['transaction_id']) ? e($st['transaction_id']) : '<span style="color:var(--text-secondary);">—</span>' ?>
                        </td>
                        <td>
                            <?php if ($st['status'] === 'PAID'): ?>
                                <span class="badge badge-success">PAID</span>
                            <?php elseif ($st['status'] === 'PAYMENT VERIFICATION PENDING' || $st['verification_status'] === 'pending'): ?>
                                <span class="badge badge-warning" style="background: #f59e0b; color: #fff;">PENDING VERIFICATION ⏳</span>
                            <?php elseif ($st['status'] === 'REJECTED' || $st['verification_status'] === 'rejected'): ?>
                                <span class="badge badge-danger">REJECTED 🔴</span>
                            <?php else: ?>
                                <span class="badge badge-danger">UNPAID</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($st['payment_id']) && ($st['verification_status'] === 'pending' || $st['status'] === 'PAYMENT VERIFICATION PENDING')): ?>
                                <div style="display: flex; gap: 0.35rem;">
                                    <form method="POST" action="/transport/accounts" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="verify_payment">
                                        <input type="hidden" name="payment_id" value="<?= $st['payment_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #10b981;">Verify ✅</button>
                                    </form>
                                    <form method="POST" action="/transport/accounts" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="reject_payment">
                                        <input type="hidden" name="payment_id" value="<?= $st['payment_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Reject 🔴</button>
                                    </form>
                                </div>
                            <?php elseif ($st['status'] === 'PAID'): ?>
                                <span style="font-size: 0.75rem; color: #10b981; font-weight: 700;">Verified ✅</span>
                            <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--text-secondary);">Awaiting Payment</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterRecords() {
    var searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
    var statusVal = document.getElementById('statusFilter').value;
    var rows = document.querySelectorAll('.ledger-row');

    rows.forEach(function(row) {
        var id = row.getAttribute('data-id').toLowerCase();
        var name = row.getAttribute('data-name').toLowerCase();
        var dept = row.getAttribute('data-dept').toLowerCase();
        var route = row.getAttribute('data-route').toLowerCase();
        var status = row.getAttribute('data-status');

        var matchesSearch = (id.includes(searchVal) || name.includes(searchVal) || dept.includes(searchVal) || route.includes(searchVal));
        var matchesStatus = (statusVal === 'ALL' || status === statusVal);

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterStatus(status) {
    document.getElementById('statusFilter').value = status;
    filterRecords();
}

function resetFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'ALL';
    filterRecords();
}
</script>
