<?php
/**
 * Student Hostel & Room Booking View — Kuppam Engineering College
 */
$st = $studentProfile ?? [];
$ab = $activeBooking ?? null;
?>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: var(--bg-surface); padding: 1.25rem 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <div>
            <h1 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.25rem 0;">
                🏠 Hostel &amp; Room Booking
            </h1>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0;">
                Select your hostel block, room, and available bed for Academic Year 2026-2027.
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="/hostel/history" class="btn btn-sm btn-outline-secondary" style="font-size: 0.75rem; font-weight: 700;">
                📜 Booking &amp; Payment History
            </a>
        </div>
    </div>

    <?php if ($ab): ?>
        <!-- ACTIVE BOOKING STATE BANNER & DETAILS -->
        <?php if ($ab['booking_status'] === 'payment_pending'): ?>
            <div style="background: rgba(245, 158, 11, 0.1); border: 2px solid #f59e0b; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 800; color: #b45309; font-size: 1rem;">⏳ Hostel Booking Payment Pending</div>
                    <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                        You have selected <strong><?= e($ab['block_name']) ?></strong> (Room <strong><?= e($ab['room_number']) ?></strong>, Bed <strong><?= e($ab['bed_number']) ?></strong>). Please complete payment to submit your booking for Warden verification.
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="/hostel/pay" class="btn btn-warning" style="font-weight: 800; font-size: 0.875rem; padding: 0.6rem 1.25rem;">
                        💳 Proceed to Payment (₹<?= number_format((float)$ab['hostel_fee'], 2) ?>) →
                    </a>
                    <form method="POST" action="/hostel/booking" style="margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_action" value="change_hostel">
                        <input type="hidden" name="booking_id" value="<?= $ab['id'] ?>">
                        <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Are you sure you want to cancel this selection and select a different hostel, room, or bed?');" style="font-weight: 700; font-size: 0.8125rem; padding: 0.6rem 1rem; background: var(--bg-surface);">
                            🔄 Change Hostel / Room
                        </button>
                    </form>
                </div>
            </div>
        <?php elseif ($ab['booking_status'] === 'payment_verification_pending'): ?>
            <div style="background: rgba(2, 132, 199, 0.1); border: 2px solid #0284c7; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-weight: 800; color: #0284c7; font-size: 1rem;">🔍 Payment Verification Pending</div>
                    <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                        Your UTR / Transaction ID (<code><?= e($ab['transaction_id']) ?></code>) has been submitted. Warden is verifying your payment for <strong><?= e($ab['block_name']) ?></strong>, Room <strong><?= e($ab['room_number']) ?></strong>.
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="badge badge-info" style="font-size: 0.8125rem; padding: 0.5rem 1rem;">Pending Warden Approval</span>
                    <form method="POST" action="/hostel/booking" style="margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_action" value="change_hostel">
                        <input type="hidden" name="booking_id" value="<?= $ab['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Are you sure you want to cancel this request and select a different room or bed?');" style="font-weight: 700; font-size: 0.75rem; background: var(--bg-surface);">
                            🔄 Change Hostel / Room
                        </button>
                    </form>
                </div>
            </div>

        <?php elseif ($ab['booking_status'] === 'confirmed'): ?>
            <!-- MY HOSTEL DETAILS CARD -->
            <div class="card" style="border-top: 4px solid var(--success);">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--success); letter-spacing: 0.05em;">Confirmed Allotment</span>
                        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0.1rem 0 0 0;">
                            🏠 My Hostel Details
                        </h2>
                    </div>
                    <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.5rem 1rem;">✅ CONFIRMED &amp; ALLOTTED</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">HOSTEL &amp; BLOCK</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                            <?= e($ab['block_name']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Type: <?= ucfirst(e($ab['gender_type'])) ?> Hostel</div>
                    </div>

                    <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">ROOM &amp; BED NUMBER</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: var(--accent-color); margin-top: 0.25rem;">
                            Room <?= e($ab['room_number']) ?> &bull; Bed <?= e($ab['bed_number']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Room Type: <?= ucfirst(e($ab['room_type'])) ?></div>
                    </div>

                    <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">HOSTEL FEE &amp; STATUS</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: var(--success); margin-top: 0.25rem;">
                            ₹<?= number_format((float)$ab['hostel_fee'], 2) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--success); font-weight: 700; margin-top: 0.25rem;">Payment Status: PAID</div>
                    </div>

                    <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 700;">WARDEN IN-CHARGE</div>
                        <div style="font-size: 0.9375rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">
                            <?= e($ab['warden_name']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">📞 <?= e($ab['warden_phone']) ?></div>
                    </div>
                </div>

                <div style="background: rgba(37, 99, 235, 0.06); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: 8px; padding: 1rem; font-size: 0.8125rem;">
                    <strong>📍 Hostel Location &amp; Address:</strong> Kuppam Engineering College Main Campus Hostel Complex, KES Nagar, Kuppam, AP - 517425.
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- HOSTEL SELECTION SECTION (If no active booking) -->
    <?php if (!$ab): ?>
        <!-- Hostel Blocks Overview Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
            <?php foreach ($hostels as $h): ?>
                <div class="card" style="border-top: 4px solid var(--accent-color); position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <div>
                            <span class="badge badge-info" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                <?= ucfirst(e($h['gender_type'])) ?> Hostel
                            </span>
                            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0.35rem 0 0 0;">
                                <?= e($h['name']) ?>
                            </h3>
                        </div>
                        <span class="badge <?= $h['available_beds'] > 0 ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.75rem; font-weight: 800;">
                            <?= $h['available_beds'] > 0 ? $h['available_beds'] . ' Beds Available' : 'No Beds Available' ?>
                        </span>
                    </div>

                    <div style="font-size: 0.8125rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1rem; background: var(--bg-main); padding: 0.75rem; border-radius: 8px;">
                        <div><strong>Fee:</strong> <span style="color: var(--success); font-weight: 800;">₹<?= number_format((float)$h['fee'], 2) ?> / Semester</span></div>
                        <div><strong>Warden:</strong> <?= e($h['warden_name']) ?> (📞 <?= e($h['warden_phone']) ?>)</div>
                        <div><strong>Facilities:</strong> <?= e($h['facilities']) ?></div>
                    </div>

                    <a href="/hostel/booking?block_id=<?= $h['id'] ?>" class="btn <?= $selectedBlockId == $h['id'] ? 'btn-primary' : 'btn-outline-primary' ?>" style="width: 100%; text-align: center; font-weight: 700; font-size: 0.8125rem;">
                        <?= $selectedBlockId == $h['id'] ? '✓ Viewing Rooms Below' : 'View Rooms &amp; Select Bed' ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Rooms & Bed Selection Grid -->
        <div class="card">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                    🚪 Available Rooms &amp; Bed Selection
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                    Click on any available green bed icon below to review booking summary and reserve your bed.
                </p>
            </div>

            <?php if (empty($rooms)): ?>
                <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                    No rooms found for this block.
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <?php foreach ($rooms as $r): ?>
                        <div style="border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; background: var(--bg-surface);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <div style="font-weight: 800; font-size: 0.9375rem; color: var(--text-primary);">
                                    Room <?= e($r['room_number']) ?> <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">(Floor <?= e($r['floor']) ?>)</span>
                                </div>
                                <span class="badge <?= $r['available_beds_count'] > 0 ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.7rem;">
                                    <?= $r['available_beds_count'] > 0 ? $r['available_beds_count'] . ' Available' : 'Room Full' ?>
                                </span>
                            </div>

                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                                Type: <strong><?= ucfirst(e($r['type'])) ?> (<?= e($r['capacity']) ?> Sharing)</strong> &bull; Fee: <strong style="color: var(--success);">₹<?= number_format((float)$r['fee_per_semester'], 2) ?></strong>
                            </div>

                            <!-- Beds List -->
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php foreach ($r['beds'] as $bed): ?>
                                    <?php if ($bed['status'] === 'available'): ?>
                                        <button type="button" onclick="openBookingModal(<?= $selectedBlockId ?>, '<?= e(addslashes($r['block_name'])) ?>', <?= $r['id'] ?>, '<?= e($r['room_number']) ?>', '<?= ucfirst(e($r['type'])) ?>', <?= $bed['bed_number'] ?>, <?= $r['fee_per_semester'] ?>)" class="btn btn-sm btn-success" style="font-size: 0.75rem; font-weight: 700; padding: 0.35rem 0.65rem; border-radius: 6px; display: flex; align-items: center; gap: 0.25rem;">
                                            🛏️ Bed <?= $bed['bed_number'] ?> (Select)
                                        </button>
                                    <?php else: ?>
                                        <button type="button" disabled class="btn btn-sm btn-secondary" style="font-size: 0.75rem; padding: 0.35rem 0.65rem; border-radius: 6px; opacity: 0.6; cursor: not-allowed;">
                                            🔒 Bed <?= $bed['bed_number'] ?> (Occupied)
                                        </button>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- HOSTEL BOOKING SUMMARY MODAL -->
<div id="bookingSummaryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 16px; max-width: 480px; width: 100%; padding: 1.5rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 0;">
                📋 Hostel Booking Summary
            </h3>
            <button type="button" onclick="closeBookingModal()" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-secondary);">&times;</button>
        </div>

        <form method="POST" action="/hostel/booking">
            <?= csrf_field() ?>
            <input type="hidden" name="hostel_block_id" id="modalBlockId">
            <input type="hidden" name="hostel_room_id" id="modalRoomId">
            <input type="hidden" name="bed_number" id="modalBedNumber">
            <input type="hidden" name="hostel_fee" id="modalFeeInput">

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; font-size: 0.8125rem; display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Student Name:</span>
                    <strong><?= e(auth_name()) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Student Roll Number:</span>
                    <code><?= e($st['roll_number'] ?? $_SESSION['username'] ?? '2026-CSE-001') ?></code>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Selected Hostel:</span>
                    <strong id="modalBlockName">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Room &amp; Room Type:</span>
                    <span id="modalRoomDetails">-</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Selected Bed:</span>
                    <strong id="modalBedDetails" style="color: var(--accent-color);">-</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-weight: 700; color: var(--text-primary);">Hostel Fee (Semester):</span>
                    <strong id="modalFeeText" style="color: var(--success); font-size: 1rem;">₹25,000.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Booking Status:</span>
                    <span class="badge badge-warning" style="font-size: 0.7rem;">Payment Pending</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="closeBookingModal()" class="btn btn-secondary" style="flex: 1; font-weight: 700;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 2; font-weight: 800;">Proceed to Payment 💳</button>
            </div>
        </form>
    </div>
</div>

<script>
function openBookingModal(blockId, blockName, roomId, roomNumber, roomType, bedNumber, fee) {
    document.getElementById('modalBlockId').value = blockId;
    document.getElementById('modalRoomId').value = roomId;
    document.getElementById('modalBedNumber').value = bedNumber;
    document.getElementById('modalFeeInput').value = fee;

    document.getElementById('modalBlockName').textContent = blockName;
    document.getElementById('modalRoomDetails').textContent = 'Room ' + roomNumber + ' (' + roomType + ')';
    document.getElementById('modalBedDetails').textContent = 'Bed ' + bedNumber;
    document.getElementById('modalFeeText').textContent = '₹' + Number(fee).toLocaleString('en-IN', {minimumFractionDigits: 2});

    document.getElementById('bookingSummaryModal').style.display = 'flex';
}

function closeBookingModal() {
    document.getElementById('bookingSummaryModal').style.display = 'none';
}
</script>
