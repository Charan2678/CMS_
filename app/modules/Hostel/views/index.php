<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Quick Stat Metric Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--accent-color);">
        <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">Hostel Blocks</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;"><?= count($blocks) ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #8b5cf6;">
        <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">Total Rooms</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;"><?= count($rooms) ?></div>
    </div>
    <div class="card" style="border-left: 4px solid var(--success);">
        <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">Active Resident Students</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--success); margin-top: 0.25rem;">
            <?= count(array_filter($allocations, fn($a) => $a['status'] === 'active')) ?>
        </div>
    </div>
</div>

<?php if (!empty($isStudentOrParent)): ?>
<!-- Student Hostel Fee Payment Banner -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid #8b5cf6; background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(2, 132, 199, 0.05) 100%);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <span style="font-size: 1.35rem;">🛏️</span>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">
                    <?= !empty($myAllocation) ? 'My Hostel Room &amp; Mess Fee Portal' : 'Hostel Admission &amp; Room Fee Payment' ?>
                </h3>
                <span class="badge" style="background: #8b5cf6; color: #fff; font-size: 0.7rem; font-weight: 700;">Official Canara Bank QR</span>
            </div>
            <p style="margin: 0; font-size: 0.8125rem; color: var(--text-secondary);">
                <?php if (!empty($myAllocation)): ?>
                    Currently Allocated: <strong><?= e($myAllocation['block_name']) ?> &bull; Room <?= e($myAllocation['room_number']) ?></strong>. Scan and pay your semester hostel &amp; mess dues directly.
                <?php else: ?>
                    Pay semester hostel room rent &amp; mess advance directly via verified Canara Bank QR code (<code>106508632000311@cnrb</code>).
                <?php endif; ?>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/fee/pay/hostel" class="btn btn-primary" style="background: #8b5cf6; border-color: #8b5cf6; text-decoration: none; font-weight: 800; padding: 0.65rem 1.35rem; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);">
                <span>📱</span> Pay Hostel Fee (Scan QR)
            </a>
            <a href="/leave/apply" class="btn btn-outline-primary" style="text-decoration: none; font-weight: 700; padding: 0.65rem 1rem; font-size: 0.8125rem; border-radius: 8px; border: 1px solid var(--border-color); color: var(--text-primary);">
                <span>🚪</span> Apply Outpass
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($canAllocate)): ?>
<!-- Allocation & Management Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- 1. Allocate Student Form -->
    <div class="card" style="border-top: 4px solid var(--success);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>🛏️</span> Allocate Student to Room
        </h3>
        <form method="POST" action="/hostel">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="allocate_student">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Student *</label>
                <select name="student_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="">-- Choose Enrolled Student --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>">
                            <?= e($st['roll_number']) ?> — <?= e($st['first_name'] . ' ' . $st['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Hostel &amp; Room *</label>
                <select name="hostel_room_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="">-- Choose Room (Occupancy Status) --</option>
                    <?php foreach ($rooms as $rm): ?>
                        <?php $isFull = (int)$rm['occupied_beds'] >= (int)$rm['capacity']; ?>
                        <option value="<?= $rm['id'] ?>" <?= $isFull ? 'disabled style="color: var(--danger);"' : '' ?>>
                            <?= e($rm['block_name']) ?> &bull; Room <?= e($rm['room_number']) ?> (<?= $rm['occupied_beds'] ?>/<?= $rm['capacity'] ?> Beds <?= $isFull ? '- FULL' : 'Available' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%; padding: 0.6rem; font-weight: 700;">Confirm Room Allocation</button>
        </form>
    </div>

    <!-- 2. Create Room Form -->
    <div class="card" style="border-top: 4px solid #8b5cf6;">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>🚪</span> Add New Hostel Room
        </h3>
        <form method="POST" action="/hostel">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_room">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Hostel Block *</label>
                    <select name="hostel_block_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                        <?php foreach ($blocks as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Room Number *</label>
                    <input type="text" name="room_number" placeholder="e.g. 101, B-204" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Bed Capacity *</label>
                    <input type="number" name="capacity" value="2" min="1" max="8" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Fee per Sem (₹)</label>
                    <input type="number" step="0.01" name="fee_per_semester" value="25000.00" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;">Create Hostel Room</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 3. Active Resident Allocations Register -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>📋</span> Resident Student Allocations Register
        </h2>
        <a href="/leave/outpasses" style="font-size: 0.8125rem; color: var(--accent-color); font-weight: 600; text-decoration: none;">View Gate Outpasses &rarr;</a>
    </div>

    <?php if (empty($allocations)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No active student room allocations currently.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.65rem 0.75rem;">Student</th>
                        <th style="padding: 0.65rem 0.75rem;">Block &amp; Room</th>
                        <th style="padding: 0.65rem 0.75rem;">Allocated Date</th>
                        <th style="padding: 0.65rem 0.75rem;">Status</th>
                        <th style="padding: 0.65rem 0.75rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $a): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= e($a['roll_number']) ?> &bull; 📞 <?= e($a['mobile'] ?? 'N/A') ?></div>
                            </td>
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($a['block_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Room <?= e($a['room_number']) ?> (Cap: <?= e($a['capacity'] ?? 2) ?>)</div>
                            </td>
                            <td style="padding: 0.75rem; color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M Y', strtotime($a['allocated_date'])) ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($a['status'] === 'active'): ?>
                                    <span class="badge badge-success">Active Resident</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Vacated</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php if ($a['status'] === 'active' && !empty($canAllocate)): ?>
                                    <form method="POST" action="/hostel" style="display: inline;" onsubmit="return confirm('Are you sure you want to vacate this student from the hostel room?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="vacate_student">
                                        <input type="hidden" name="allocation_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; font-weight: 700;">
                                            Vacate
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Hostel Blocks Reference Table -->
<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>🏢</span> Hostel Blocks Directory
        </h2>
    </div>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                    <th style="padding: 0.65rem 0.75rem;">Block Name</th>
                    <th style="padding: 0.65rem 0.75rem;">Type</th>
                    <th style="padding: 0.65rem 0.75rem;">Warden</th>
                    <th style="padding: 0.65rem 0.75rem;">Contact</th>
                    <th style="padding: 0.65rem 0.75rem; text-align: center;">Hostel Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blocks as $b): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--accent-color);"><?= e($b['name']) ?></td>
                        <td style="padding: 0.75rem;"><span class="badge badge-info" style="text-transform: uppercase;"><?= e($b['gender_type']) ?></span></td>
                        <td style="padding: 0.75rem;"><?= e($b['warden_name'] ?? 'N/A') ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($b['warden_phone'] ?? 'N/A') ?></td>
                        <td style="padding: 0.75rem; text-align: center;">
                            <a href="/fee/pay/hostel" class="btn btn-sm btn-primary" style="background: #8b5cf6; border-color: #8b5cf6; text-decoration: none; padding: 0.35rem 0.85rem; font-size: 0.75rem; font-weight: 700; border-radius: 6px;">
                                💳 Pay ₹25,000 (QR)
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
