<div style="max-width: 650px; margin: 0 auto; padding-bottom: 2rem;">

    <!-- Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('credit-card', 'icon-sm') ?> My Digital Bus Pass
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">
                Official institutional transport credential linked to verified fee clearance.
            </p>
        </div>

        <div>
            <a href="/transport" class="btn btn-secondary" style="font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                <?= icon('arrow-left', 'icon-xs') ?> Transport Overview
            </a>
        </div>
    </div>

    <?php if (empty($subscription) && (empty($pass) || empty($pass['route_name']))): ?>
        <!-- No Subscription State -->
        <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: var(--bg-peach); color: var(--orange-accent); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; border: 1px solid var(--orange-border);">
                <?= icon('bus', 'icon-lg') ?>
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.5rem 0;">No Active Transport Subscription</h3>
            <p style="color: var(--text-secondary); font-size: 0.875rem; max-width: 420px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                You are currently not enrolled in any college bus route. Subscribe to an official route to generate your transport pass.
            </p>
            <a href="/transport" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 700;">
                <?= icon('bus', 'icon-xs') ?> View Transport Routes &amp; Fares
            </a>
        </div>

    <?php elseif (!empty($pass) && $pass['status'] === 'active'): ?>
        <!-- Active Paid Bus Pass -->
        <div class="card" style="margin-bottom: 1.5rem; border-top: 4px solid #3FA76A; background: var(--bg-surface);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="badge badge-green" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem; font-weight: 800;">
                        <?= icon('check-circle-2', 'icon-xs') ?> ACTIVE TRANSPORT CREDENTIAL
                    </span>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <a href="/transport/pass/<?= $pass['id'] ?>" target="_blank" class="btn btn-primary" style="text-decoration: none; font-size: 0.8125rem; font-weight: 700; padding: 0.45rem 1rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <?= icon('download', 'icon-xs') ?> View &amp; Download Bus Pass
                    </a>
                </div>
            </div>

            <!-- Pass Preview Box -->
            <div style="background: #FFFDF9; border: 1.5px solid #E5DFD5; border-radius: 16px; padding: 1.5rem; position: relative; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.04);">
                
                <div style="display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #ECECEC; padding-bottom: 0.85rem; margin-bottom: 1.15rem;">
                    <img src="/assets/images/logo.png" alt="KEC Logo" style="width: 38px; height: 38px; object-fit: contain;">
                    <div>
                        <div style="font-weight: 800; font-size: 0.95rem; color: var(--text-primary);">Kuppam Engineering College</div>
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #D79922; text-transform: uppercase; letter-spacing: 0.05em;">Digital Bus Pass</div>
                    </div>
                </div>

                <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
                    <div style="width: 68px; height: 80px; border-radius: 10px; background: #F3F4F6; border: 2px solid #D79922; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?php if (!empty($pass['photo_path'])): ?>
                            <img src="<?= e($pass['photo_path']) ?>" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 1.8rem; font-weight: 800; color: #D79922;"><?= strtoupper(substr($pass['first_name'] ?? 'S', 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin: 0;"><?= e($pass['first_name'] . ' ' . $pass['last_name']) ?></h2>
                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.785rem; color: #D79922; font-weight: 700; margin-top: 0.15rem;">
                            <?= e($pass['roll_number']) ?>
                        </div>
                        <div style="font-size: 0.785rem; color: var(--text-secondary); margin-top: 0.2rem;">
                            <?= e($pass['department_name'] ?? 'CSE') ?> &bull; Semester <?= e($pass['semester_number'] ?? '1') ?>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.8125rem; background: #FFFFFF; padding: 0.85rem 1rem; border-radius: 10px; border: 1px solid #ECECEC;">
                    <div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Assigned Bus</span>
                        <strong style="color: var(--text-primary);"><?= e($pass['bus_number'] ?? 'Campus Bus') ?></strong>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Pickup Stop</span>
                        <strong><?= e($pass['pickup_point'] ?? 'Campus') ?></strong>
                    </div>
                    <div style="grid-column: span 2;">
                        <span style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Subscribed Route</span>
                        <strong style="color: var(--orange-accent);"><?= e($pass['route_name']) ?></strong>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Pass Number</span>
                        <code style="font-weight: 800; color: var(--text-primary);"><?= e($pass['pass_number']) ?></code>
                    </div>
                    <div>
                        <span style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Validity Period</span>
                        <strong style="color: #065F46;"><?= date('d M Y', strtotime($pass['valid_from'])) ?> &ndash; <?= date('d M Y', strtotime($pass['valid_until'])) ?></strong>
                    </div>
                </div>

            </div>
        </div>

    <?php else: ?>
        <!-- Payment Pending / Pass Unavailable Card -->
        <div class="card" style="border-top: 4px solid var(--orange-accent); background: var(--bg-surface); padding: 2.25rem 1.75rem; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: #FEF3C7; color: #D97706; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.15rem; font-size: 1.5rem; border: 1px solid #FDE68A;">
                <?= icon('lock', 'icon-md') ?>
            </div>

            <span class="badge badge-peach" style="font-size: 0.75rem; padding: 0.35rem 0.75rem; font-weight: 800; margin-bottom: 0.75rem;">
                PAYMENT PENDING
            </span>

            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0.25rem 0 0.5rem 0;">
                Digital Bus Pass Unavailable
            </h2>

            <p style="color: var(--text-secondary); font-size: 0.875rem; max-width: 460px; margin: 0 auto 1.5rem auto; line-height: 1.5;">
                You are subscribed to <strong><?= e($subscription['route_name']) ?></strong> (Bus: <?= e($subscription['bus_number'] ?? 'Bus') ?>). Please complete your annual transport fee payment of <strong>₹<?= number_format((float)($subscription['fare'] ?? 18000), 2) ?></strong> to activate your official bus pass.
            </p>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem 1.25rem; max-width: 400px; margin: 0 auto 1.5rem auto; text-align: left; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--text-secondary);">Route Fee:</span>
                    <strong style="color: var(--text-primary);">₹<?= number_format((float)($subscription['fare'] ?? 18000), 2) ?> / year</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--text-secondary);">Payment Status:</span>
                    <span style="color: #D97706; font-weight: 700;">Unpaid (Dues Pending)</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-secondary);">Pass Status:</span>
                    <span style="color: var(--text-muted); font-weight: 700;">Locked until paid</span>
                </div>
            </div>

            <div>
                <a href="/fee/pay/transport" class="btn-primary" style="text-decoration: none; font-weight: 800; padding: 0.75rem 1.75rem; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 10px; box-shadow: var(--shadow-glow-primary);">
                    <?= icon('credit-card', 'icon-xs') ?> Pay Transport Fee (Scan Official QR)
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>
