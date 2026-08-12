<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🚌 Transport &amp; Bus Routes Management</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Manage college bus routes, fleet capacity, driver details, and student transport subscriptions</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <?php if (!empty($canManage)): ?>
            <a href="/transport" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Transport Overview</a>
        <?php else: ?>
            <a href="/transport/history" class="btn btn-secondary" style="font-size: 0.8125rem;">💳 My Transport Payments</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (empty($canManage)): ?>
    <?php if (!empty($activeSubscription)): ?>
        <!-- 10. STUDENT MY TRANSPORT DETAILS CARD -->
        <div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid <?= $activeSubscription['subscription_status'] === 'active' ? '#10b981' : '#f59e0b' ?>;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🚌</span> My Transport Subscription Details
                </h2>
                <div>
                    <?php if ($activeSubscription['subscription_status'] === 'active'): ?>
                        <span class="badge badge-success" style="font-size: 0.8125rem; padding: 0.4rem 0.8rem;">SUBSCRIPTION STATUS: ACTIVE ✅</span>
                    <?php elseif ($activeSubscription['subscription_status'] === 'payment_verification_pending'): ?>
                        <span class="badge badge-warning" style="background:#f59e0b; color:#fff; font-size: 0.8125rem; padding: 0.4rem 0.8rem;">PAYMENT VERIFICATION PENDING ⏳</span>
                    <?php else: ?>
                        <span class="badge badge-secondary" style="font-size: 0.8125rem; padding: 0.4rem 0.8rem;">PAYMENT PENDING 💳</span>
                    <?php endif; ?>
                </div>
            </div>

            <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); display: block; font-weight: 600;">SELECTED BUS ROUTE</span>
                    <strong style="font-size: 1rem; color: var(--text-primary); display: block; margin-top: 0.2rem;"><?= e($activeSubscription['route_name']) ?></strong>
                    <span class="badge badge-info" style="font-size: 0.7rem; margin-top: 0.25rem;">Code: <?= e($activeSubscription['route_code']) ?></span>
                </div>

                <div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); display: block; font-weight: 600;">BUS &amp; VEHICLE NUMBER</span>
                    <strong style="font-size: 1rem; color: #2563eb; display: block; margin-top: 0.2rem;"><?= e($activeSubscription['bus_number']) ?></strong>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;"><?= e($activeSubscription['bus_reg_number']) ?></span>
                </div>

                <div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); display: block; font-weight: 600;">DRIVER &amp; CONTACT</span>
                    <strong style="font-size: 0.9375rem; color: var(--text-primary); display: block; margin-top: 0.2rem;"><?= e($activeSubscription['driver_name']) ?></strong>
                    <a href="tel:<?= e($activeSubscription['driver_contact']) ?>" style="font-size: 0.8125rem; color: var(--accent-color); font-weight: 700; text-decoration: none;">📞 <?= e($activeSubscription['driver_contact']) ?></a>
                </div>

                <div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); display: block; font-weight: 600;">ANNUAL TRANSPORT FEE</span>
                    <strong style="font-size: 1.25rem; color: var(--text-primary); display: block; margin-top: 0.1rem;">₹<?= number_format((float)$activeSubscription['annual_fee'], 2) ?></strong>
                    <span class="badge <?= $activeSubscription['payment_status'] === 'paid' ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.7rem; margin-top: 0.25rem;">
                        Payment: <?= strtoupper(e($activeSubscription['payment_status'])) ?>
                    </span>
                </div>
            </div>

            <!-- Pickup & Drop Details Bar -->
            <div style="margin: 0 1.5rem 1rem 1.5rem; background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.15rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">🚏</div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">PICKUP POINT &amp; TIME</span>
                        <div style="font-weight: 800; color: var(--text-primary); font-size: 0.9375rem;"><?= e($activeSubscription['pickup_point']) ?></div>
                        <div style="font-size: 0.8125rem; color: #2563eb; font-weight: 700;">🕒 Pickup Time: <?= e($activeSubscription['pickup_time']) ?></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 42px; height: 42px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">🏫</div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700;">DROP POINT &amp; ARRIVAL</span>
                        <div style="font-weight: 800; color: var(--text-primary); font-size: 0.9375rem;"><?= e($activeSubscription['drop_point']) ?></div>
                        <div style="font-size: 0.8125rem; color: #10b981; font-weight: 700;">🏁 Campus Arrival: <?= e($activeSubscription['default_drop_time'] ?? '8:30 AM') ?></div>
                    </div>
                </div>
            </div>

            <?php 
                $pendingReq = null;
                if (!empty($changeRequests)) {
                    foreach ($changeRequests as $cr) {
                        if ($cr['request_status'] === 'pending') {
                            $pendingReq = $cr;
                            break;
                        }
                    }
                }
            ?>

            <?php if (!empty($pendingReq)): ?>
                <div style="margin: 0 1.5rem 1rem 1.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 8px; padding: 0.875rem 1.15rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <strong style="color: #d97706; font-size: 0.875rem;">⏳ Route Change Request Submitted (Pending Approval)</strong>
                        <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.2rem;">
                            Requested New Route: <strong><?= e($pendingReq['new_route_name']) ?> (<?= e($pendingReq['new_route_code']) ?>)</strong> — Bus <?= e($pendingReq['new_bus']) ?>
                        </div>
                    </div>
                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">STATUS: PENDING MANAGER APPROVAL</span>
                </div>
            <?php endif; ?>

            <div style="padding: 0 1.5rem 1.5rem 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <?php if ($activeSubscription['subscription_status'] === 'payment_pending'): ?>
                    <a href="/transport/pay" class="btn btn-primary" style="font-weight: 700;">💳 Pay Transport Fee Now (QR)</a>
                <?php endif; ?>
                <button type="button" onclick="scrollToRoutes()" class="btn btn-secondary" style="font-weight: 700; background: #0284c7; color: #fff; border: none;">
                    🔄 Change Route / Bus
                </button>
                <a href="/transport/history" class="btn btn-secondary" style="font-size: 0.8125rem;">🧾 Payment History &amp; Receipts</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. AVAILABLE BUS ROUTES (FOR STUDENT) -->
    <div class="card" id="availableRoutesCard">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
            <span>Available College Bus Routes &amp; Seating Capacity</span>
            <span style="font-size: 0.75rem; color: var(--text-secondary);">Real-time Fleet Availability</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; padding: 1.25rem;">
            <?php foreach ($routes as $r): ?>
                <?php $isFull = ((int)$r['available_seats'] <= 0); ?>
                <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0;"><?= e($r['route_name']) ?></h3>
                                <span class="badge badge-info" style="font-size: 0.7rem; margin-top: 0.25rem;">Route Code: <?= e($r['route_code']) ?></span>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.2rem; font-weight: 800; color: #2563eb;">₹<?= number_format((float)$r['annual_fee'], 0) ?></div>
                                <div style="font-size: 0.725rem; color: var(--text-secondary);">Annual Transport Fee</div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; font-size: 0.8125rem; margin-top: 0.875rem; background: var(--bg-surface); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div>
                                <span style="color: var(--text-secondary); display: block; font-size: 0.725rem;">BUS NUMBER</span>
                                <strong><?= e($r['bus_number']) ?></strong> (<?= e($r['bus_reg_number']) ?>)
                            </div>
                            <div>
                                <span style="color: var(--text-secondary); display: block; font-size: 0.725rem;">SEATING CAPACITY</span>
                                <strong style="color: <?= $isFull ? 'var(--danger)' : '#10b981' ?>;">
                                    <?= $isFull ? 'Bus Full' : (int)$r['available_seats'] . ' Available Seats' ?>
                                </strong>
                            </div>
                            <div>
                                <span style="color: var(--text-secondary); display: block; font-size: 0.725rem;">PICKUP POINT &amp; TIME</span>
                                🚏 <?= e($r['pickup_point']) ?> (<?= e($r['pickup_time']) ?>)
                            </div>
                            <div>
                                <span style="color: var(--text-secondary); display: block; font-size: 0.725rem;">DRIVER CONTACT</span>
                                👤 <?= e($r['driver_name']) ?> (<?= e($r['driver_contact']) ?>)
                            </div>
                        </div>

                        <?php if (!empty($r['stops'])): ?>
                            <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-secondary);">
                                📍 <strong>Route Stops:</strong> 
                                <?php foreach ($r['stops'] as $idx => $st): ?>
                                    <?= ($idx > 0 ? ' → ' : '') . e($st['stop_name']) . ' (' . e($st['pickup_time']) . ')' ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 0.875rem; text-align: right;">
                        <?php if (!empty($activeSubscription) && $activeSubscription['subscription_status'] === 'active'): ?>
                            <?php if ((int)$activeSubscription['route_id'] === (int)$r['id']): ?>
                                <span class="badge badge-success" style="display: block; text-align: center; padding: 0.6rem; font-size: 0.8125rem; font-weight: 700;">
                                    Current Active Route ✅
                                </span>
                            <?php elseif ($isFull): ?>
                                <button disabled class="btn btn-danger" style="width: 100%; opacity: 0.65; cursor: not-allowed; font-size: 0.8125rem;">
                                    🛑 Bus Full (0 Seats Available)
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" onclick="openRouteChangeModal(<?= $r['id'] ?>, '<?= e($r['route_name']) ?>', '<?= e($r['route_code']) ?>', '<?= e($r['bus_number']) ?>', '<?= e($r['pickup_point']) ?>', '<?= e($r['pickup_time']) ?>', '<?= e($r['drop_point']) ?>', '<?= e($r['driver_name']) ?>', '<?= e($r['driver_contact']) ?>', <?= (int)$r['available_seats'] ?>, <?= (float)$r['annual_fee'] ?>, <?= (float)$activeSubscription['annual_fee'] ?>, '<?= e($activeSubscription['route_name']) ?>')" style="width: 100%; font-weight: 700; background: #0284c7;">
                                    Change to This Bus 🔄
                                </button>
                            <?php endif; ?>
                        <?php elseif ($isFull): ?>
                            <button disabled class="btn btn-danger" style="width: 100%; opacity: 0.65; cursor: not-allowed; font-size: 0.8125rem;">
                                🛑 Bus Full (0 Seats)
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary" onclick="openRouteConfirmation(<?= $r['id'] ?>, '<?= e($r['route_name']) ?>', '<?= e($r['route_code']) ?>', '<?= e($r['bus_number']) ?>', '<?= e($r['pickup_point']) ?>', '<?= e($r['pickup_time']) ?>', '<?= e($r['drop_point']) ?>', <?= (float)$r['annual_fee'] ?>)" style="width: 100%; font-weight: 700;">
                                Select This Bus 🚌
                            </button>
                        <?php endif; ?>
                    </div>


                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. ROUTE SELECTION CONFIRMATION MODAL / DIALOG -->
    <div id="selectionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 520px; width: 100%; background: var(--bg-surface); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); overflow: hidden;">
            <div style="padding: 1.15rem 1.35rem; background: #2563eb; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800;">🚌 Confirm Transport Selection</h3>
                <button type="button" onclick="closeRouteConfirmation()" style="background: none; border: none; color: #fff; font-size: 1.25rem; cursor: pointer; font-weight: 800;">&times;</button>
            </div>
            
            <form method="POST" action="/transport/routes" style="padding: 1.35rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="select_route">
                <input type="hidden" name="route_id" id="modalRouteId">

                <div style="margin-bottom: 1.25rem; background: var(--bg-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--border-color); font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Selected Route:</span>
                        <strong id="modalRouteName" style="color: var(--text-primary);">—</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Route Code:</span>
                        <span id="modalRouteCode" class="badge badge-info">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Bus Number:</span>
                        <strong id="modalBusNumber" style="color: #2563eb;">—</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Pickup Point &amp; Time:</span>
                        <span id="modalPickup" style="font-weight: 700;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Drop Point:</span>
                        <span id="modalDrop" style="font-weight: 700;">—</span>
                    </div>
                    <div style="border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700;">Annual Transport Fee:</span>
                        <strong id="modalFee" style="font-size: 1.2rem; color: #10b981;">₹0.00</strong>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="closeRouteConfirmation()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 2; font-weight: 800;">Confirm Transport Selection 💳</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Route Transfer Request Modal -->
    <div id="changeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 520px; width: 100%; background: var(--bg-surface); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.25);">
            <div style="padding: 1.15rem 1.35rem; background: #0284c7; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800;">🔄 Request Route / Bus Change</h3>
                <button type="button" onclick="closeRouteChangeModal()" style="background: none; border: none; color: #fff; font-size: 1.25rem; cursor: pointer; font-weight: 800;">&times;</button>
            </div>
            <form method="POST" action="/transport/routes" style="padding: 1.35rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="request_change">
                <input type="hidden" name="new_route_id" id="changeNewRouteId">

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">Current Active Route:</span>
                        <strong id="changeCurrentRoute" style="color: var(--text-primary);">—</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);">New Requested Route:</span>
                        <strong id="changeNewRoute" style="color: #0284c7;">—</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Bus &amp; Vehicle Number:</span>
                        <span id="changeBusNumber" style="font-weight: 700;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Pickup Point &amp; Time:</span>
                        <span id="changePickup" style="font-weight: 700;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Drop Point &amp; Arrival:</span>
                        <span id="changeDrop" style="font-weight: 700;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Driver Details:</span>
                        <span id="changeDriver" style="font-weight: 700;">—</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary);">Available Capacity:</span>
                        <span id="changeSeats" style="font-weight: 700; color: #10b981;">—</span>
                    </div>

                    <!-- Fee Calculation Box -->
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.875rem; margin-top: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--text-secondary);">Current Route Fee:</span>
                            <span id="changeCurrentFee">₹0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; margin-bottom: 0.35rem;">
                            <span style="color: var(--text-secondary);">New Route Fee:</span>
                            <span id="changeNewFee">₹0.00</span>
                        </div>
                        <div style="border-top: 1px dashed var(--border-color); padding-top: 0.5rem; margin-top: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                            <strong style="font-size: 0.875rem;">Fee Difference:</strong>
                            <strong id="changeFeeDiff" style="font-size: 1.15rem; color: #2563eb;">₹0.00</strong>
                        </div>
                        <div id="changeFeeNote" style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.35rem; text-align: right;"></div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="closeRouteChangeModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" id="changeSubmitBtn" class="btn btn-primary" style="flex: 2; font-weight: 800; background: #0284c7;">Submit Change Request 🔄</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRouteConfirmation(id, name, code, bus, pickup, time, drop, fee) {
            document.getElementById('modalRouteId').value = id;
            document.getElementById('modalRouteName').innerText = name;
            document.getElementById('modalRouteCode').innerText = code;
            document.getElementById('modalBusNumber').innerText = bus;
            document.getElementById('modalPickup').innerText = pickup + ' (' + time + ')';
            document.getElementById('modalDrop').innerText = drop;
            document.getElementById('modalFee').innerText = '₹' + fee.toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('selectionModal').style.display = 'flex';
        }

        function closeRouteConfirmation() {
            document.getElementById('selectionModal').style.display = 'none';
        }

        function openRouteChangeModal(newId, newName, newCode, newBus, pickup, time, drop, driver, driverContact, seats, newFee, currentFee, currentRouteName) {
            document.getElementById('changeNewRouteId').value = newId;
            document.getElementById('changeCurrentRoute').innerText = currentRouteName;
            document.getElementById('changeNewRoute').innerText = newName + ' (' + newCode + ')';
            document.getElementById('changeBusNumber').innerText = newBus;
            document.getElementById('changePickup').innerText = pickup + ' (' + time + ')';
            document.getElementById('changeDrop').innerText = drop;
            document.getElementById('changeDriver').innerText = driver + ' (' + driverContact + ')';
            document.getElementById('changeSeats').innerText = seats + ' Seats Available';

            document.getElementById('changeCurrentFee').innerText = '₹' + currentFee.toLocaleString('en-IN', {minimumFractionDigits: 2});
            document.getElementById('changeNewFee').innerText = '₹' + newFee.toLocaleString('en-IN', {minimumFractionDigits: 2});

            var diff = newFee - currentFee;
            var diffElem = document.getElementById('changeFeeDiff');
            var noteElem = document.getElementById('changeFeeNote');
            var btnElem = document.getElementById('changeSubmitBtn');

            if (diff > 0) {
                diffElem.innerText = '+ ₹' + diff.toLocaleString('en-IN', {minimumFractionDigits: 2});
                diffElem.style.color = '#2563eb';
                noteElem.innerText = 'Additional amount payable via Transport QR Code upon request approval.';
                btnElem.innerText = 'Confirm & Submit Request (Pay +₹' + diff.toLocaleString('en-IN', {minimumFractionDigits: 0}) + ') 💳';
            } else if (diff === 0) {
                diffElem.innerText = '₹0.00';
                diffElem.style.color = '#10b981';
                noteElem.innerText = 'No additional payment required.';
                btnElem.innerText = 'Confirm Route Change 🔄';
            } else {
                diffElem.innerText = '- ₹' + Math.abs(diff).toLocaleString('en-IN', {minimumFractionDigits: 2});
                diffElem.style.color = '#f59e0b';
                noteElem.innerText = 'Pending Transport Adjustment: ₹' + Math.abs(diff).toLocaleString('en-IN', {minimumFractionDigits: 0}) + ' credit.';
                btnElem.innerText = 'Confirm Route Change 🔄';
            }

            document.getElementById('changeModal').style.display = 'flex';
        }

        function closeRouteChangeModal() {
            document.getElementById('changeModal').style.display = 'none';
        }

        function scrollToRoutes() {
            var elem = document.getElementById('availableRoutesCard');
            if (elem) elem.scrollIntoView({ behavior: 'smooth' });
        }
    </script>


<?php else: ?>
    <!-- STAFF VIEW (PRESERVED DESIGN FOR TRANSPORT MANAGER) -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Create Forms -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Add Route Card -->
            <div class="card">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                    ➕ Add New Bus Route
                </div>
                <form method="POST" action="/transport/routes" style="padding: 1.25rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="create_route">

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Route Name *</label>
                        <input type="text" name="route_name" placeholder="e.g. Bangarupalem Route" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Route Code</label>
                        <input type="text" name="route_code" placeholder="e.g. R-09" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Bus Number</label>
                        <input type="text" name="bus_number" placeholder="e.g. BUS-09" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Annual Fee (₹)</label>
                        <input type="number" name="fare" value="18000" step="500" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Route</button>
                </form>
            </div>

            <!-- Subscribe Student Form -->
            <div class="card">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                    🚌 Subscribe Student to Route
                </div>
                <form method="POST" action="/transport/routes" style="padding: 1.25rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="allocate_student">

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Student *</label>
                        <select name="student_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <option value="">-- Select Student --</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= e($s['roll_number']) ?> — <?= e($s['first_name'] . ' ' . $s['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Bus Route *</label>
                        <select name="transport_route_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <option value="">-- Choose Route --</option>
                            <?php foreach ($routes as $r): ?>
                                <option value="<?= $r['id'] ?>">
                                    <?= e($r['route_name']) ?> (₹<?= number_format((float)$r['annual_fee'], 0) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%;">Confirm Subscription</button>
                </form>
            </div>
        </div>

        <!-- Active Routes List -->
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                Active Bus Routes &amp; Fleet Inventory (<?= count($routes) ?> Routes)
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Route Name</th>
                            <th>Code &amp; Bus</th>
                            <th>Capacity</th>
                            <th>Available Seats</th>
                            <th>Annual Fee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $r): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($r['route_name']) ?></td>
                                <td style="font-family: monospace; color: var(--accent-color);"><?= e($r['route_code'] ?? 'R-01') ?> (<?= e($r['bus_number']) ?>)</td>
                                <td style="font-weight: 600;"><?= (int)($r['capacity'] ?? 50) ?> Seats</td>
                                <td><span class="badge badge-info"><?= (int)($r['available_seats'] ?? 15) ?> Available</span></td>
                                <td style="font-weight: 700;">₹<?= number_format((float)$r['annual_fee'], 2) ?></td>
                                <td><span class="badge badge-success">Active Fleet</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
