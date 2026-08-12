<?php
$hasActivePass = (!empty($myBusPass) && $myBusPass['status'] === 'active');
$currentPickup = $mySubscription['pickup_point'] ?? ($myBusPass['pickup_point'] ?? '');
?>

<div style="max-width: 980px; margin: 0 auto; padding-bottom: 3rem;">

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.45rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.55rem;">
                <?= icon('bus', 'icon-md') ?> College Bus Transport Routes
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.84rem; margin: 0.25rem 0 0 0;">
                Official institutional bus routes, pickup stops, schedules, and annual transportation fares.
            </p>
        </div>

        <?php if ($hasActivePass): ?>
            <div style="display: flex; gap: 0.65rem; align-items: center;">
                <a href="/transport/pass/<?= $myBusPass['id'] ?>" target="_blank" class="btn btn-primary" style="font-size: 0.8125rem; font-weight: 800; padding: 0.55rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-glow-primary);">
                    <?= icon('id-card', 'icon-xs') ?> View Bus Pass
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($hasActivePass): ?>
        <!-- ========================================================= -->
        <!-- ACTIVE PAID SUBSCRIPTION STATUS CARD                       -->
        <!-- ========================================================= -->
        <div class="card" style="margin-bottom: 2rem; border-top: 4px solid #3FA76A; background: var(--bg-surface); padding: 1.35rem 1.65rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #ECFDF5; color: #065F46; display: flex; align-items: center; justify-content: center; border: 1px solid #A7F3D0; font-size: 1.4rem;">
                        <?= icon('check-circle-2', 'icon-sm') ?>
                    </div>
                    <div>
                        <div style="font-size: 0.725rem; font-weight: 800; color: #065F46; text-transform: uppercase; letter-spacing: 0.05em;">ACTIVE SUBSCRIPTION</div>
                        <h2 style="margin: 0.15rem 0 0 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">
                            <?= e($myBusPass['route_name']) ?>
                        </h2>
                        <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.2rem;">
                            Assigned Bus: <strong><?= e($myBusPass['bus_number']) ?></strong> &bull; Pickup Stop: <strong><?= e($myBusPass['pickup_point'] ?? 'Campus') ?></strong> &bull; Valid: <strong style="color: #065F46;"><?= date('d M Y', strtotime($myBusPass['valid_from'])) ?> &ndash; <?= date('d M Y', strtotime($myBusPass['valid_until'])) ?></strong>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.65rem; align-items: center;">
                    <a href="/transport/pass/<?= $myBusPass['id'] ?>" target="_blank" class="btn btn-primary" style="text-decoration: none; font-weight: 800; font-size: 0.8125rem; padding: 0.55rem 1.25rem; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 8px; box-shadow: var(--shadow-glow-primary);">
                        <?= icon('id-card', 'icon-xs') ?> View Bus Pass
                    </a>
                </div>
            </div>
        </div>

    <?php elseif (!empty($mySubscription)): ?>
        <!-- ========================================================= -->
        <!-- PAYMENT PENDING BANNER                                    -->
        <!-- ========================================================= -->
        <div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--orange-accent); background: var(--bg-surface); padding: 1.35rem 1.65rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
                <div>
                    <div style="font-size: 0.725rem; font-weight: 800; color: #D97706; text-transform: uppercase; letter-spacing: 0.05em;">FEE PAYMENT REQUIRED</div>
                    <h2 style="margin: 0.15rem 0 0 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">
                        Selected: <?= e($mySubscription['route_name']) ?>
                    </h2>
                    <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.2rem;">
                        Assigned Bus: <strong><?= e($mySubscription['bus_number'] ?? 'Bus') ?></strong> &bull; Pickup Stop: <strong><?= e($mySubscription['pickup_point'] ?? 'Campus') ?></strong> &bull; Annual Fare: <strong>₹<?= number_format((float)($mySubscription['fare'] ?? 15000), 2) ?>/yr</strong>
                    </div>
                </div>

                <div>
                    <a href="/fee/pay/transport" class="btn-primary" style="text-decoration: none; font-weight: 800; font-size: 0.8125rem; padding: 0.6rem 1.35rem; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: 8px; box-shadow: var(--shadow-glow-primary);">
                        <?= icon('credit-card', 'icon-xs') ?> Pay Transport Fee (₹<?= number_format((float)($mySubscription['fare'] ?? 15000), 2) ?>)
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ========================================================= -->
    <!-- PRIMARY SECTION: ADMIN-CREATED BUS ROUTES LISTING          -->
    <!-- ========================================================= -->
    <div style="margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.45rem;">
                <?= icon('map-pin', 'icon-sm') ?> Available College Bus Routes (<?= count($routes) ?>)
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.2rem 0 0 0;">
                <?= $hasActivePass ? 'You have an active bus pass. You can change your route or pickup stop anytime (nominal ₹99 reallocation fee applies).' : 'Select your commuting route and pickup stop to proceed with transport registration.' ?>
            </p>
        </div>
    </div>

    <!-- Route Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <?php foreach ($routes as $r): ?>
            <?php 
                $isSelected = (!empty($mySubscription['route_id']) && (int)$mySubscription['route_id'] === (int)$r['id']);
            ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 14px; transition: transform 0.18s ease, box-shadow 0.18s ease; position: relative; <?= $isSelected ? 'border: 2px solid var(--orange-accent);' : '' ?>">
                
                <div>
                    <!-- Route Header -->
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.85rem; gap: 0.5rem;">
                        <div>
                            <span class="badge <?= $isSelected ? 'badge-green' : 'badge-peach' ?>" style="font-size: 0.6875rem; font-weight: 800; margin-bottom: 0.35rem;">
                                <?= $isSelected ? '✓ YOUR ROUTE' : 'ROUTE #' . $r['id'] ?>
                            </span>
                            <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0; line-height: 1.3;">
                                <?= e($r['route_name']) ?>
                            </h3>
                        </div>
                        <span class="badge badge-green" style="font-size: 0.7rem; font-weight: 800; white-space: nowrap;">
                            ✓ <?= $r['available_seats'] ?? 45 ?> Seats Open
                        </span>
                    </div>

                    <!-- Route Pathway -->
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.75rem 0.85rem; margin-bottom: 0.85rem; font-size: 0.8125rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; color: var(--text-primary); font-weight: 600;">
                            <?= icon('map-pin', 'icon-xs', ['style' => 'color: var(--orange-accent);']) ?>
                            <span><?= e($r['start_point']) ?> &rarr; <?= e($r['end_point']) ?></span>
                        </div>
                        <?php if (!empty($r['vehicle_number'])): ?>
                            <div style="font-size: 0.725rem; color: var(--text-secondary); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.35rem;">
                                <?= icon('bus', 'icon-xs') ?> Assigned Bus: <strong><?= e($r['vehicle_number']) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Key Stops Badges -->
                    <div style="margin-bottom: 0.85rem;">
                        <span style="font-size: 0.6875rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.35rem;">
                            Key Route Stops
                        </span>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                            <?php foreach (array_slice($r['stops'] ?? [], 0, 4) as $stop): ?>
                                <span style="background: #FFF4E8; color: #C05621; border: 1px solid #FBD38D; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 600;">
                                    &bull; <?= e($stop) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Schedule Timings -->
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.35rem;">
                        <?= icon('clock', 'icon-xs') ?>
                        <span>Morning: <strong><?= e($r['timings']['morning_departure'] ?? '07:45 AM') ?></strong> &bull; Return: <strong><?= e($r['timings']['evening_departure'] ?? '04:30 PM') ?></strong></span>
                    </div>
                </div>

                <!-- Card Footer & Selection CTA -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                    <div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); display: block;">Annual Fee</span>
                        <span style="font-size: 1.1rem; font-weight: 800; color: var(--success);">
                            ₹<?= number_format((float)$r['fare'], 2) ?>
                        </span>
                    </div>

                    <button type="button" onclick="openRouteSelector(<?= htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8') ?>)" class="btn-primary" style="padding: 0.45rem 1.1rem; font-size: 0.8125rem; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <?= $isSelected ? 'Change Stop / Route' : 'Select Route &rarr;' ?>
                    </button>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- Interactive Route & Pickup Stop Selection Modal -->
    <div id="routeModal" onclick="handleBackdropClick(event)" style="display: none; position: fixed; inset: 0; background: rgba(16, 30, 51, 0.65); z-index: 99999; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(4px);">
        <div id="routeModalCard" class="card" style="max-width: 490px; width: 100%; border-radius: 16px; box-shadow: var(--shadow-xl); border-top: 4px solid var(--orange-accent); position: relative; background: var(--bg-surface); padding: 1.5rem;">
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem;">
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                    <?= icon('bus', 'icon-sm') ?> <span id="modalTitle">Confirm Route &amp; Stop</span>
                </h3>
                <button type="button" onclick="closeRouteSelector()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem; line-height: 1;">
                    &times;
                </button>
            </div>

            <!-- Route Modification Notice (for students who already paid) -->
            <div id="modalChangeNotice" style="display: none; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 0.75rem 0.95rem; margin-bottom: 1.15rem; font-size: 0.8125rem; color: #92400E;">
                <strong>★ Route Modification:</strong> You have an active bus pass. A nominal route reallocation fee of <strong>₹99.00</strong> applies to update your route/stop and reissue your digital bus pass.
            </div>

            <form method="POST" action="/transport/subscribe">
                <?= csrf_field() ?>
                <input type="hidden" name="route_id" id="modalRouteId" value="">

                <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; margin-bottom: 1.15rem; font-size: 0.84rem;">
                    <div style="font-weight: 800; color: var(--text-primary); font-size: 0.95rem;" id="modalRouteName">Route Name</div>
                    <div style="color: var(--text-secondary); margin-top: 0.25rem;" id="modalRoutePath">Start &rarr; End</div>
                    <div style="color: var(--orange-accent); font-weight: 700; margin-top: 0.25rem;" id="modalBusNumber">Bus: ...</div>
                </div>

                <div class="form-group" style="margin-bottom: 1.15rem;">
                    <label class="form-label" style="font-weight: 700; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">
                        Select Your Pickup Stop *
                    </label>
                    <select name="pickup_point" id="modalPickupSelect" class="form-control" required style="font-size: 0.875rem; font-weight: 600;">
                        <!-- Populated via JS -->
                    </select>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
                    <span style="font-size: 0.8125rem; font-weight: 700; color: #065F46;" id="modalFeeLabel">Annual Route Fare</span>
                    <span style="font-size: 1.15rem; font-weight: 900; color: #065F46;" id="modalRouteFare">₹15,000.00</span>
                </div>

                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="closeRouteSelector()" class="btn btn-secondary" style="flex: 1; font-weight: 700; font-size: 0.84rem;">
                        Cancel
                    </button>
                    <button type="submit" id="modalSubmitBtn" class="btn-primary" style="flex: 2; font-weight: 800; font-size: 0.84rem; padding: 0.65rem; border-radius: 8px; border: none; cursor: pointer;">
                        Proceed to Payment &rarr;
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
window.hasActivePass = <?= $hasActivePass ? 'true' : 'false' ?>;
window.currentPickup = "<?= addslashes($currentPickup) ?>";

function openRouteSelector(route) {
    document.getElementById('modalRouteId').value = route.id;
    document.getElementById('modalRouteName').innerText = route.route_name;
    document.getElementById('modalRoutePath').innerText = route.start_point + ' → ' + route.end_point;
    document.getElementById('modalBusNumber').innerText = 'Assigned Bus: ' + (route.vehicle_number || 'Campus Fleet');

    const select = document.getElementById('modalPickupSelect');
    select.innerHTML = '';
    
    const stops = route.stops && route.stops.length > 0 ? route.stops : ['Main Road', 'RTC Bus Stand', 'College Campus Gate'];
    stops.forEach(function(s) {
        const opt = document.createElement('option');
        opt.value = s;
        opt.innerText = s;
        if (s === window.currentPickup) {
            opt.selected = true;
        }
        select.appendChild(opt);
    });

    if (window.hasActivePass) {
        document.getElementById('modalTitle').innerText = 'Change Route / Pickup Stop';
        document.getElementById('modalChangeNotice').style.display = 'block';
        document.getElementById('modalFeeLabel').innerText = 'Route Modification Fee';
        document.getElementById('modalRouteFare').innerText = '₹99.00';
        document.getElementById('modalSubmitBtn').innerText = 'Pay ₹99 & Change Route →';
    } else {
        document.getElementById('modalTitle').innerText = 'Confirm Route & Stop';
        document.getElementById('modalChangeNotice').style.display = 'none';
        document.getElementById('modalFeeLabel').innerText = 'Annual Route Fare';
        document.getElementById('modalRouteFare').innerText = '₹' + parseFloat(route.fare).toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('modalSubmitBtn').innerText = 'Proceed to Payment →';
    }

    const modal = document.getElementById('routeModal');
    modal.style.display = 'flex';
}

function closeRouteSelector() {
    const modal = document.getElementById('routeModal');
    if (modal) modal.style.display = 'none';
}

function handleBackdropClick(e) {
    if (e.target && e.target.id === 'routeModal') {
        closeRouteSelector();
    }
}
</script>
