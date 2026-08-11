<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                💳 Fee Checkout &amp; Secure Payment
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">
                Kuppam Engineering College Official Online Fee Payment Portal
            </p>
        </div>
        <a href="/fee/payments" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Dues Ledger</a>
    </div>

    <div style="display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 1.5rem;">
        <!-- Left: Summary Card -->
        <div class="card" style="border-top: 4px solid <?= $upiDetails['badge_color'] ?>; height: fit-content;">
            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: <?= $upiDetails['badge_color'] ?>;">
                <?= e($upiDetails['title']) ?>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin: 0.35rem 0 1rem 0;">
                <?= e($fee['category_name']) ?>
            </h3>

            <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                    <span style="color: var(--text-secondary);">Student:</span>
                    <strong><?= e($fee['first_name'] . ' ' . $fee['last_name']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                    <span style="color: var(--text-secondary);">Roll Number:</span>
                    <strong><?= e($fee['roll_number']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                    <span style="color: var(--text-secondary);">Semester:</span>
                    <strong>Sem <?= e($fee['semester_number']) ?> (<?= e($fee['course_code']) ?>)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem;">
                    <span style="color: var(--text-secondary);">Total Fee:</span>
                    <span>₹<?= number_format((float)$fee['final_amount'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-top: 0.35rem;">
                    <span style="color: var(--text-secondary);">Already Paid:</span>
                    <span style="color: var(--success);">₹<?= number_format((float)$fee['total_paid'], 2) ?></span>
                </div>
            </div>

            <div style="background: rgba(37,99,235,0.08); border: 1px solid rgba(37,99,235,0.3); border-radius: 8px; padding: 1rem; text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">Net Payable Amount</div>
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--accent-color); margin-top: 0.25rem;">
                    ₹<?= number_format((float)$dueBalance, 2) ?>
                </div>
            </div>
        </div>

        <!-- Right: Payment Options Tabs -->
        <div class="card" style="border-top: 4px solid var(--accent-color);">
            <div style="display: flex; gap: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                <button type="button" onclick="switchPayTab('upi')" id="tabBtnUpi" class="btn btn-sm btn-primary" style="font-weight: 700; font-size: 0.8125rem;">
                    📱 UPI QR Scan &amp; Pay
                </button>
                <button type="button" onclick="switchPayTab('netbanking')" id="tabBtnNetbanking" class="btn btn-sm btn-secondary" style="font-weight: 700; font-size: 0.8125rem;">
                    🏦 Netbanking / Cards
                </button>
            </div>

            <!-- Tab 1: Multi-QR UPI Section -->
            <div id="payTabUpi">
                <div style="text-align: center; margin-bottom: 1.25rem;">
                    <div style="display: inline-block; background: #ffffff; padding: 1.25rem; border-radius: 16px; border: 2px solid var(--border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.08); max-width: 270px; width: 100%;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-bottom: 0.65rem; color: #0284c7; font-weight: 800; font-size: 0.8125rem;">
                            <span>🏛️</span> Official Institutional QR
                        </div>
                        <?php if (!empty($upiDetails['qr_image'])): ?>
                            <img src="<?= e($upiDetails['qr_image']) ?>" alt="Official UPI QR Code" style="width: 210px; height: 210px; object-fit: contain; display: block; margin: 0 auto; border-radius: 8px;">
                        <?php else: ?>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= rawurlencode($upiUri) ?>" alt="UPI Payment QR Code" style="width: 200px; height: 200px; display: block; margin: 0 auto; border-radius: 8px;">
                        <?php endif; ?>
                        
                        <div style="margin-top: 0.75rem;">
                            <a href="<?= e($upiUri) ?>" class="btn btn-sm btn-outline-primary" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 700; text-decoration: none; padding: 0.35rem 0.85rem; border-radius: 20px; border: 1px solid var(--accent-color); color: var(--accent-color);">
                                <span>📲</span> Open in UPI App
                            </a>
                        </div>
                        
                        <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; margin-top: 0.5rem;">
                            Scan with GPay, PhonePe, Paytm, BHIM, or any UPI App
                        </div>
                    </div>
                </div>

                <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.875rem; font-size: 0.8125rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                        <span style="color: var(--text-secondary);">Verified Payee VPA:</span>
                        <code style="color: var(--accent-color); font-weight: 700;"><?= e($upiDetails['vpa']) ?></code>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                        <span style="color: var(--text-secondary);">Beneficiary Name:</span>
                        <strong><?= e($upiDetails['payee_name']) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                        <span style="color: var(--text-secondary);">Bank &amp; Account:</span>
                        <span><?= e($upiDetails['bank_name']) ?></span>
                    </div>
                    <?php if (!empty($upiDetails['merchant_code'])): ?>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">NPCI Merchant Category (MCC):</span>
                        <span><code><?= e($upiDetails['merchant_code']) ?></code> (Educational Institutions)</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- UTR Submission Form -->
                <form method="POST" action="/fee/submit-utr">
                    <?= csrf_field() ?>
                    <input type="hidden" name="student_fee_id" value="<?= $fee['id'] ?>">
                    <input type="hidden" name="student_id" value="<?= $fee['student_id'] ?>">
                    <input type="hidden" name="fee_type" value="<?= $feeType ?>">
                    <input type="hidden" name="amount" value="<?= $dueBalance ?>">

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-weight: 700; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">
                            Enter 12-Digit Bank UTR / UPI Transaction Reference *
                        </label>
                        <input type="text" name="utr_number" required placeholder="e.g. 423512345678" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-family: monospace; font-size: 0.9375rem; letter-spacing: 0.05em;">
                        <small style="color: var(--text-secondary); display: block; margin-top: 0.25rem; font-size: 0.7rem;">Found on your GPay/PhonePe payment successful screen under 'UPI transaction ID'.</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-weight: 800; font-size: 0.9375rem;">
                        🚀 Submit UTR for Counter Verification
                    </button>
                </form>
            </div>

            <!-- Tab 2: Netbanking & Instant Card Gateway -->
            <div id="payTabNetbanking" style="display: none;">
                <form method="POST" action="/fee/instant-pay">
                    <?= csrf_field() ?>
                    <input type="hidden" name="student_fee_id" value="<?= $fee['id'] ?>">
                    <input type="hidden" name="student_id" value="<?= $fee['student_id'] ?>">
                    <input type="hidden" name="fee_type" value="<?= $feeType ?>">
                    <input type="hidden" name="amount" value="<?= $dueBalance ?>">

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-weight: 700; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Payment Method</label>
                        <select name="payment_method" class="form-control" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-size: 0.8125rem;">
                            <option value="netbanking">🏦 Netbanking (SBI, ICICI, HDFC, Axis, Canara)</option>
                            <option value="debit_card">💳 Debit Card / RuPay</option>
                            <option value="credit_card">💳 Credit Card (Visa / Mastercard)</option>
                        </select>
                    </div>

                    <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.8125rem;">
                        <div style="font-weight: 700; color: var(--success); margin-bottom: 0.25rem;">🔒 256-Bit SSL Encrypted Payment Gateway</div>
                        <p style="color: var(--text-secondary); margin: 0; font-size: 0.75rem;">Instant online settlement will immediately update your fee balance and generate your downloadable PDF receipt.</p>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%; padding: 0.75rem; font-weight: 800; font-size: 0.9375rem;">
                        💳 Pay ₹<?= number_format((float)$dueBalance, 2) ?> Instantly
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchPayTab(tab) {
    var upi = document.getElementById('payTabUpi');
    var nb  = document.getElementById('payTabNetbanking');
    var btnUpi = document.getElementById('tabBtnUpi');
    var btnNb  = document.getElementById('tabBtnNetbanking');

    if (tab === 'upi') {
        upi.style.display = 'block';
        nb.style.display  = 'none';
        btnUpi.className  = 'btn btn-sm btn-primary';
        btnNb.className   = 'btn btn-sm btn-secondary';
    } else {
        upi.style.display = 'none';
        nb.style.display  = 'block';
        btnUpi.className  = 'btn btn-sm btn-secondary';
        btnNb.className   = 'btn btn-sm btn-primary';
    }
}
</script>
