<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Quick Stat Metric Cards -->
<div class="grid-metrics" style="margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--accent-color);">
        <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">Hostel Blocks</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;"><?= count($blocks) ?></div>
    </div>
    <div class="card" style="border-left: 4px solid var(--accent-color);">
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
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--accent-color); background: var(--glass-bg);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <?= icon('building-2', 'icon-md') ?>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">
                    <?= !empty($myAllocation) ? 'My Hostel Room &amp; Mess Fee Portal' : 'Hostel Admission &amp; Room Fee Payment' ?>
                </h3>
                <span class="badge badge-info" style="font-size: 0.7rem; font-weight: 700;">Official Canara Bank QR</span>
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
            <a href="/fee/pay/hostel" class="btn-primary" style="text-decoration: none; font-weight: 800; padding: 0.65rem 1.35rem; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 8px;">
                <?= icon('credit-card', 'icon-xs') ?> Pay Hostel Fee (Scan QR)
            </a>
            <a href="/leave/apply" class="btn btn-secondary" style="text-decoration: none; font-weight: 700; padding: 0.65rem 1rem; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                <?= icon('door-open', 'icon-xs') ?> Apply Outpass
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($canAllocate)): ?>
<!-- Allocation & Management Grid -->
<div class="dashboard-grid-equal" style="margin-bottom: 2rem;">
    <!-- 1. Allocate Student Form -->
    <div class="card" style="border-top: 4px solid var(--success);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('user-plus', 'icon-sm') ?> Allocate Student to Room
        </h3>
        <form method="POST" action="/hostel">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="allocate_student">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Student *</label>
                <select name="student_id" class="form-control" required>
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
                <select name="hostel_room_id" class="form-control" required>
                    <option value="">-- Choose Room (Occupancy Status) --</option>
                    <?php foreach ($rooms as $rm): ?>
                        <?php $isFull = (int)$rm['occupied_beds'] >= (int)$rm['capacity']; ?>
                        <option value="<?= $rm['id'] ?>" <?= $isFull ? 'disabled style="color: var(--danger);"' : '' ?>>
                            <?= e($rm['block_name']) ?> &bull; Room <?= e($rm['room_number']) ?> (<?= $rm['occupied_beds'] ?>/<?= $rm['capacity'] ?> Beds <?= $isFull ? '- FULL' : 'Available' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('check-circle-2', 'icon-xs') ?> Confirm Room Allocation</button>
        </form>
    </div>

    <!-- 2. Create Room Form -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('plus', 'icon-sm') ?> Add New Hostel Room
        </h3>
        <form method="POST" action="/hostel">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_room">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Hostel Block *</label>
                    <select name="hostel_block_id" class="form-control" required>
                        <?php foreach ($blocks as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Room Number *</label>
                    <input type="text" name="room_number" placeholder="e.g. 101, B-204" class="form-control" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Bed Capacity *</label>
                    <input type="number" name="capacity" value="2" min="1" max="8" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Fee per Sem (₹)</label>
                    <input type="number" step="0.01" name="fee_per_semester" value="25000.00" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('plus', 'icon-xs') ?> Create Hostel Room</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 3. Active Resident Allocations Register -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('clipboard-list', 'icon-sm') ?> Resident Student Allocations Register
        </h2>
        <a href="/leave/outpasses" style="font-size: 0.8125rem; color: var(--accent-color); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">View Gate Outpasses <?= icon('arrow-right', 'icon-xs') ?></a>
    </div>

    <?php if (empty($allocations)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No active student room allocations currently.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Block &amp; Room</th>
                        <th>Allocated Date</th>
                        <th>Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $a): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.3rem; margin-top: 0.15rem;">
                                    <?= e($a['roll_number']) ?> &bull; <?= icon('phone', 'icon-xs') ?> <?= e($a['mobile'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($a['block_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Room <?= e($a['room_number']) ?> (Cap: <?= e($a['capacity'] ?? 2) ?>)</div>
                            </td>
                            <td style="color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M Y', strtotime($a['allocated_date'])) ?>
                            </td>
                            <td>
                                <?php if ($a['status'] === 'active'): ?>
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Active Resident</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Vacated</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
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
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('building-2', 'icon-sm') ?> Hostel Blocks Directory
        </h2>
    </div>

    <div class="table-responsive">
        <table class="table" style="font-size: 0.8125rem;">
            <thead>
                <tr>
                    <th>Block Name</th>
                    <th>Type</th>
                    <th>Warden</th>
                    <th>Contact</th>
                    <th style="text-align: center;">Hostel Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blocks as $b): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--accent-color);"><?= e($b['name']) ?></td>
                        <td><span class="badge badge-info" style="text-transform: uppercase;"><?= e($b['gender_type']) ?></span></td>
                        <td><?= e($b['warden_name'] ?? 'N/A') ?></td>
                        <td style="color: var(--text-secondary);"><?= e($b['warden_phone'] ?? 'N/A') ?></td>
                        <td style="text-align: center;">
                            <a href="/fee/pay/hostel" class="btn-primary" style="text-decoration: none; padding: 0.35rem 0.85rem; font-size: 0.75rem; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <?= icon('credit-card', 'icon-xs') ?> Pay ₹25,000 (QR)
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
