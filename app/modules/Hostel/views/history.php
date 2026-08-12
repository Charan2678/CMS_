<?php
/**
 * Student Hostel Payments & Booking History View — Kuppam Engineering College
 */
$hist = $history ?? [];
?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: var(--bg-surface); padding: 1.25rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <div>
            <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                📜 My Hostel Payments &amp; Booking History
            </h1>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0;">
                Audit log of all your past and active hostel room bookings and payment transactions.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="/hostel" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.45rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.85rem; background: rgba(2, 132, 199, 0.05); border: 1.5px solid #0284c7; color: #0284c7; transition: all 0.2s ease;">
                ← Back to Hostel Overview
            </a>
            <a href="/hostel/booking" class="btn btn-sm btn-primary" style="font-weight: 700; font-size: 0.75rem;">
                🏠 View Hostel Booking
            </a>
        </div>
    </div>

    <div class="card">
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                Hostel Booking &amp; Transaction History
            </h3>
        </div>

        <?php if (empty($hist)): ?>
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-secondary);">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏠</div>
                <div style="font-weight: 700; font-size: 0.9375rem;">No Hostel Booking History Found</div>
                <p style="font-size: 0.8125rem; margin-top: 0.25rem;">You haven't submitted any hostel room booking requests yet.</p>
                <a href="/hostel/booking" class="btn btn-sm btn-primary" style="margin-top: 0.75rem; font-weight: 700;">
                    Book Hostel Room &amp; Bed →
                </a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: var(--bg-main); border-bottom: 2px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem 1rem;">Academic Year</th>
                            <th style="padding: 0.75rem 1rem;">Semester</th>
                            <th style="padding: 0.75rem 1rem;">Hostel &amp; Block</th>
                            <th style="padding: 0.75rem 1rem;">Room &amp; Bed</th>
                            <th style="padding: 0.75rem 1rem;">Hostel Fee</th>
                            <th style="padding: 0.75rem 1rem;">Transaction ID / UTR</th>
                            <th style="padding: 0.75rem 1rem;">Payment Date</th>
                            <th style="padding: 0.75rem 1rem;">Booking Status</th>
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
                                    <code><?= e($h['transaction_id'] ?: 'N/A') ?></code>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?= $h['payment_date'] ? date('d M Y', strtotime($h['payment_date'])) : 'N/A' ?>
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <?php if ($h['booking_status'] === 'confirmed'): ?>
                                        <span class="badge badge-success" style="font-size: 0.7rem;">CONFIRMED</span>
                                    <?php elseif ($h['booking_status'] === 'payment_verification_pending'): ?>
                                        <span class="badge badge-info" style="font-size: 0.7rem;">VERIFICATION PENDING</span>
                                    <?php elseif ($h['booking_status'] === 'payment_pending'): ?>
                                        <span class="badge badge-warning" style="font-size: 0.7rem;">PAYMENT PENDING</span>
                                    <?php elseif ($h['booking_status'] === 'rejected'): ?>
                                        <span class="badge badge-danger" style="font-size: 0.7rem;" title="<?= e($h['rejection_reason'] ?? 'Rejected') ?>">REJECTED</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="font-size: 0.7rem;"><?= strtoupper(e($h['booking_status'])) ?></span>
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
