<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Bus Pass — <?= e($pass['pass_number']) ?> — KEC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Include html2canvas for high-res instant image download -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --primary: #172A46;
            --navy: #101E33;
            --gold: #D79922;
            --gold-light: #FFF4D9;
            --orange: #F28C28;
            --orange-light: #FFF1E4;
            --bg-ivory: #FFFCF8;
            --border-subtle: #E8E2D8;
            --success: #2F6B5A;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F4F2EB;
            color: var(--navy);
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Action Toolbar */
        .no-print {
            margin-bottom: 1.75rem;
            display: flex;
            gap: 0.85rem;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F6B26B 0%, #F28C28 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(242, 140, 40, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(242, 140, 40, 0.45);
        }

        .btn-success {
            background: linear-gradient(135deg, #34D399 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.4);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--navy);
            border-color: #D1D5DB;
        }

        /* ─── Premium Physical-Style Bus Pass Card ─── */
        .bus-pass-card {
            width: 100%;
            max-width: 520px;
            background: #FFFFFF;
            border-radius: 20px;
            border: 2px solid #E5DFD5;
            box-shadow: 0 16px 36px rgba(16, 30, 51, 0.12), 0 2px 6px rgba(16, 30, 51, 0.04);
            position: relative;
            overflow: hidden;
            padding: 1.75rem 1.85rem;
        }

        /* Top Metallic Border Strip */
        .bus-pass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 7px;
            background: linear-gradient(90deg, #D79922 0%, #F6B26B 50%, #D79922 100%);
        }

        /* Subtle College Seal Watermark in background */
        .watermark-bg {
            position: absolute;
            right: -25px;
            bottom: -25px;
            width: 220px;
            height: 220px;
            opacity: 0.04;
            pointer-events: none;
            background: url('/assets/images/logo.png') no-repeat center center;
            background-size: contain;
        }

        /* Header */
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 1rem;
            border-bottom: 1.5px solid var(--border-subtle);
            margin-bottom: 1.25rem;
        }

        .college-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .college-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .college-name {
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .pass-tagline {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--gold);
            font-weight: 800;
            margin-top: 0.15rem;
        }

        .pass-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .badge-active {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .badge-pending {
            background: #FFFBEB;
            color: #92400E;
            border: 1px solid #FDE68A;
        }

        .badge-suspended {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        /* Student Identity Section */
        .student-profile-row {
            display: flex;
            gap: 1.25rem;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .photo-frame {
            width: 82px;
            height: 98px;
            border-radius: 12px;
            background: #F9FAFB;
            border: 2px solid #D79922;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-meta h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--navy);
            margin: 0;
            line-height: 1.2;
        }

        .student-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--gold);
            margin-top: 0.2rem;
        }

        .student-academic {
            font-size: 0.8125rem;
            color: #4B5563;
            margin-top: 0.25rem;
            line-height: 1.4;
        }

        /* Transport Grid Info */
        .transport-details-grid {
            background: #FFFDF9;
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .grid-item {
            display: flex;
            flex-direction: column;
        }

        .item-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6B7280;
            margin-bottom: 0.15rem;
        }

        .item-value {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--navy);
        }

        .item-value.accent {
            color: var(--orange);
        }

        /* Security Foil & Verification Bar */
        .security-footer {
            border-top: 1.5px solid var(--border-subtle);
            padding-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #6B7280;
        }

        .pass-number-tag {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            color: var(--navy);
            font-size: 0.8125rem;
        }

        .hologram-seal {
            font-size: 0.6875rem;
            font-weight: 800;
            color: #D79922;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-transform: uppercase;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .bus-pass-card {
                box-shadow: none;
                border: 1.5px solid #000;
                max-width: 100%;
                margin: 0 auto;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button id="btnDownloadImage" onclick="downloadPassImage()" class="btn btn-success">
            📥 Download Card Image (PNG)
        </button>
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print / Save as PDF
        </button>
        <a href="/transport/pass" class="btn btn-secondary">
            &larr; Back to My Bus Pass
        </a>
    </div>

    <!-- Bus Pass Credential Card -->
    <div id="busPassCard" class="bus-pass-card">
        <div class="watermark-bg"></div>

        <!-- Header -->
        <div class="card-header">
            <div class="college-brand">
                <img src="/assets/images/logo.png" alt="KEC Logo" class="college-logo">
                <div>
                    <div class="college-name">Kuppam Engineering College</div>
                    <div class="pass-tagline">Official Student Transport Credential</div>
                </div>
            </div>
            <div>
                <?php if ($pass['status'] === 'active'): ?>
                    <span class="pass-badge badge-active">&bull; ACTIVE PASS</span>
                <?php elseif ($pass['status'] === 'suspended'): ?>
                    <span class="pass-badge badge-suspended">&bull; SUSPENDED</span>
                <?php else: ?>
                    <span class="pass-badge badge-pending">&bull; PAYMENT PENDING</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Student Profile Row -->
        <div class="student-profile-row">
            <div class="photo-frame">
                <?php if (!empty($pass['photo_path'])): ?>
                    <img src="<?= e($pass['photo_path']) ?>" alt="<?= e($pass['first_name']) ?>">
                <?php else: ?>
                    <div style="font-size: 2.2rem; color: #D79922; font-weight: 800;">
                        <?= strtoupper(substr($pass['first_name'] ?? 'S', 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="student-meta">
                <h2><?= e($pass['first_name'] . ' ' . $pass['last_name']) ?></h2>
                <div class="student-id">ROLL NO: <?= e($pass['roll_number']) ?></div>
                <div class="student-academic">
                    <strong><?= e($pass['department_name'] ?? 'CSE') ?></strong> (<?= e($pass['course_code'] ?? 'B.Tech') ?>)<br>
                    Semester <?= e($pass['semester_number'] ?? '1') ?><?= !empty($pass['section_name']) ? ' &bull; Sec ' . e($pass['section_name']) : '' ?>
                </div>
            </div>
        </div>

        <!-- Transport Assignment Grid -->
        <div class="transport-details-grid">
            <div class="grid-item">
                <span class="item-label">Assigned College Bus</span>
                <span class="item-value"><?= e($pass['bus_number'] ?? 'Campus Fleet') ?></span>
            </div>
            <div class="grid-item">
                <span class="item-label">Boarding Stop</span>
                <span class="item-value"><?= e($pass['pickup_point'] ?? 'Main Gate') ?></span>
            </div>
            <div class="grid-item" style="grid-column: span 2;">
                <span class="item-label">Subscribed Bus Route</span>
                <span class="item-value accent"><?= e($pass['route_name']) ?></span>
            </div>
            <div class="grid-item">
                <span class="item-label">Valid From</span>
                <span class="item-value"><?= date('d M Y', strtotime($pass['valid_from'])) ?></span>
            </div>
            <div class="grid-item">
                <span class="item-label">Valid Until</span>
                <span class="item-value" style="color: #065F46;"><?= date('d M Y', strtotime($pass['valid_until'])) ?></span>
            </div>
        </div>

        <!-- Security & Payment Footer -->
        <div class="security-footer">
            <div>
                <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: #9CA3AF;">PASS IDENTIFIER</div>
                <div class="pass-number-tag"><?= e($pass['pass_number']) ?></div>
            </div>

            <div style="text-align: right;">
                <div class="hologram-seal">★ VERIFIED TRANSPORT CREDENTIAL</div>
                <div style="font-size: 0.6875rem; color: #6B7280; margin-top: 0.15rem;">
                    Fee Paid: <strong>₹<?= number_format((float)$pass['amount_paid'], 2) ?></strong>
                </div>
            </div>
        </div>

    </div>

    <script>
        function downloadPassImage() {
            const card = document.getElementById('busPassCard');
            const btn = document.getElementById('btnDownloadImage');
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Generating Image...';
            btn.disabled = true;

            html2canvas(card, {
                scale: 2.5,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false
            }).then(function(canvas) {
                const link = document.createElement('a');
                link.download = '<?= e(str_replace([' ', '/', '-'], '_', $pass['pass_number'])) ?>_<?= e($pass['roll_number']) ?>.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = '✓ Downloaded!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, 2000);
            }).catch(function(err) {
                console.error(err);
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Could not download image directly. You can use the Print / Save as PDF button.');
            });
        }
    </script>

</body>
</html>
