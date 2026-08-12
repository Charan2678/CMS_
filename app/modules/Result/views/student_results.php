<?php
/**
 * Student — My Examination Results & Marksheets
 *
 * Shows: Mid Examination 1–4 + Semester 1 & 2 per Academic Year.
 * Data comes from $examData (array of AY blocks) and $semesters (legacy fallback).
 *
 * Tab order per AY: Mid 1 | Mid 2 | Semester 1 | Mid 3 | Mid 4 | Semester 2
 * Tab switching is pure JS — no page reload.
 */

// ── Ordered tab definition ──────────────────────────────────────────────────
// Each tab has: key (unique within AY), type ('mid'|'semester'), label, semNumber (semester only)
$tabOrder = [
    ['key' => 'cia1', 'type' => 'mid',      'label' => 'Mid 1',      'semNum' => null],
    ['key' => 'cia2', 'type' => 'mid',      'label' => 'Mid 2',      'semNum' => null],
    ['key' => 'sem1', 'type' => 'semester', 'label' => 'Semester 1', 'semNum' => 1],
    ['key' => 'cia3', 'type' => 'mid',      'label' => 'Mid 3',      'semNum' => null],
    ['key' => 'cia4', 'type' => 'mid',      'label' => 'Mid 4',      'semNum' => null],
    ['key' => 'sem2', 'type' => 'semester', 'label' => 'Semester 2', 'semNum' => 2],
];

// ── Helper: grade colour ─────────────────────────────────────────────────────
function gradeColor(string $grade): string {
    return match(strtoupper($grade)) {
        'A+', 'A' => '#10b981',
        'B'       => '#0ea5e9',
        'C'       => '#f59e0b',
        'D'       => '#f97316',
        default   => '#ef4444',
    };
}

// ── Helper: resolve semester result for a given semester number within AY ────
function getSemResult(array $ayBlock, int $semNum): ?array {
    foreach ($ayBlock['semester_results'] as $sr) {
        if ((int)($sr['semester_number'] ?? 0) === $semNum) {
            return $sr;
        }
    }
    return null;
}
?>

<!-- ════════════════════════════════════════════════════════════════
     PAGE HEADER
════════════════════════════════════════════════════════════════ -->
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 class="page-title" style="font-size:1.75rem; font-weight:700; color:var(--text-primary); margin:0;">
            My Examination Results &amp; Marksheets
        </h1>
        <p style="color:var(--text-secondary); font-size:0.875rem; margin-top:0.25rem;">
            View your Mid Examination and Semester Examination results for each academic year.
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn-primary"
                style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.5rem 1.125rem;">
            <span>🖨️</span> Print / Download Marksheets
        </button>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     EMPTY STATE
════════════════════════════════════════════════════════════════ -->
<?php if (empty($examData)): ?>
    <div class="card" style="padding:2.5rem; text-align:center;">
        <div style="font-size:3rem; margin-bottom:0.75rem;">📋</div>
        <h2 style="font-size:1.25rem; color:var(--text-primary); margin-top:0;">No Examination Records Found</h2>
        <p style="color:var(--text-secondary); font-size:0.875rem; max-width:480px; margin:0.5rem auto 0;">
            Your academic placement has not been set up yet, or no examination records are linked to your student account.
            Please contact the examination cell if you believe this is an error.
        </p>
    </div>
<?php else: ?>

<!-- ════════════════════════════════════════════════════════════════
     ACADEMIC YEAR SELECTOR
════════════════════════════════════════════════════════════════ -->
<div style="display:flex; align-items:center; gap:0.875rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    <label for="aySelector" style="font-size:0.875rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">
        Academic Year
    </label>
    <select id="aySelector"
            onchange="switchAY(this.value)"
            style="padding:0.45rem 1rem; border-radius:8px; border:1.5px solid var(--border-color); background:var(--bg-card); color:var(--text-primary); font-size:0.9375rem; font-weight:700; cursor:pointer; min-width:160px;">
        <?php foreach ($examData as $idx => $ayBlock): ?>
            <option value="ay-<?= (int)$ayBlock['academic_year_id'] ?>">
                <?= e($ayBlock['academic_year_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- ════════════════════════════════════════════════════════════════
     PER-ACADEMIC-YEAR PANELS
════════════════════════════════════════════════════════════════ -->
<?php foreach ($examData as $idx => $ayBlock):
    $ayId    = (int)$ayBlock['academic_year_id'];
    $ayPanId = 'ay-' . $ayId;
    $firstTabKey = $tabOrder[0]['key'] . '-' . $ayId;
?>
<div id="<?= $ayPanId ?>" class="ay-panel" style="<?= $idx > 0 ? 'display:none;' : '' ?>">

    <!-- ── Examination Tabs ──────────────────────────────────── -->
    <div style="display:flex; gap:0; flex-wrap:nowrap; overflow-x:auto; background:var(--bg-card); border:1px solid var(--border-color); border-radius:12px 12px 0 0; border-bottom:none;">
        <?php foreach ($tabOrder as $tIdx => $tab):
            $tabId  = $tab['key'] . '-' . $ayId;
            $isMid  = $tab['type'] === 'mid';
            $isSem  = $tab['type'] === 'semester';

            // Determine published state for tab badge
            $tabPublished = true;
            if ($isMid) {
                $tabPublished = $ayBlock['mid_exams'][$tab['key']]['published'] ?? false;
            } elseif ($isSem) {
                $semRes = getSemResult($ayBlock, $tab['semNum']);
                $tabPublished = $semRes !== null && (int)($semRes['published'] ?? 0) === 1;
            }

            // Visual divider before Mid 3 (after Sem 1)
            $divider = ($tIdx === 3) ? 'border-left:2px dashed var(--border-color);' : '';
        ?>
        <button
            id="tab-<?= $tabId ?>"
            onclick="switchTab('<?= $tabId ?>', '<?= $ayPanId ?>')"
            class="exam-tab"
            data-ay="<?= $ayPanId ?>"
            style="
                flex:1;
                min-width:90px;
                padding:0.7rem 0.5rem 0.6rem;
                border:none;
                background:transparent;
                cursor:pointer;
                font-size:0.78rem;
                font-weight:700;
                color:var(--text-secondary);
                border-bottom:3px solid transparent;
                border-top-left-radius:<?= $tIdx === 0 ? '11px' : '0' ?>;
                border-top-right-radius:<?= $tIdx === count($tabOrder)-1 ? '11px' : '0' ?>;
                transition:all 0.18s ease;
                position:relative;
                <?= $divider ?>
            ">
            <?php if ($isSem): ?>
                <span style="display:block; font-size:0.9rem; margin-bottom:1px;">🎓</span>
            <?php endif; ?>
            <?= e($tab['label']) ?>
            <?php if (!$tabPublished): ?>
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:#f59e0b; margin-left:3px; vertical-align:middle;"></span>
            <?php endif; ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- ── Tab Content Panels ─────────────────────────────────── -->
    <?php foreach ($tabOrder as $tIdx => $tab):
        $tabId  = $tab['key'] . '-' . $ayId;
        $isMid  = $tab['type'] === 'mid';
        $isSem  = $tab['type'] === 'semester';
    ?>
    <div id="panel-<?= $tabId ?>"
         class="tab-panel"
         data-ay="<?= $ayPanId ?>"
         style="display:none; background:var(--bg-card); border:1px solid var(--border-color); border-top:none; border-radius:0 0 12px 12px; padding:1.5rem;">

        <?php if ($isMid):
            $midData  = $ayBlock['mid_exams'][$tab['key']] ?? null;
            $published = $midData['published'] ?? false;
        ?>

            <!-- ═══ MID EXAM PANEL ═══════════════════════════════ -->
            <?php if (!$published): ?>
                <!-- Not published -->
                <div style="text-align:center; padding:2rem 1rem;">
                    <div style="font-size:2.5rem; margin-bottom:0.5rem;">⏳</div>
                    <h3 style="font-size:1.1rem; font-weight:700; color:var(--text-primary); margin:0 0 0.4rem;">
                        <?= e($midData['label'] ?? $tab['label']) ?> — Results Not Published Yet
                    </h3>
                    <p style="color:var(--text-secondary); font-size:0.875rem; max-width:420px; margin:0 auto;">
                        Marks for this examination have not been entered or published by your faculty yet.
                        Please check back later.
                    </p>
                    <span style="display:inline-block; margin-top:1rem; padding:0.35rem 1rem; border-radius:20px; background:rgba(245,158,11,0.12); color:#92710a; font-size:0.78rem; font-weight:700; border:1px solid rgba(245,158,11,0.35);">
                        NOT PUBLISHED
                    </span>
                </div>
            <?php else:
                $subjects = $midData['subjects'] ?? [];
                $summary  = $midData['summary']  ?? [];
            ?>
                <!-- Exam header -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem; padding-bottom:1rem; border-bottom:1px solid var(--border-color);">
                    <div>
                        <span style="font-size:0.7rem; text-transform:uppercase; font-weight:700; color:var(--accent-color); letter-spacing:0.06em;">
                            <?= e($ayBlock['academic_year_name']) ?>
                        </span>
                        <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:0.2rem 0 0;">
                            <?= e($midData['label']) ?>
                        </h2>
                    </div>
                    <!-- Summary chips -->
                    <div style="display:flex; gap:0.625rem; flex-wrap:wrap; align-items:center;">
                        <div style="padding:0.35rem 0.75rem; border-radius:8px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--success); text-transform:uppercase;">Passed</div>
                            <div style="font-size:1.1rem; font-weight:800; color:var(--success);"><?= (int)$summary['passed'] ?></div>
                        </div>
                        <div style="padding:0.35rem 0.75rem; border-radius:8px; background:rgba(239,68,68,0.09); border:1px solid rgba(239,68,68,0.22); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--danger); text-transform:uppercase;">Failed</div>
                            <div style="font-size:1.1rem; font-weight:800; color:var(--danger);"><?= (int)$summary['failed'] ?></div>
                        </div>
                        <div style="padding:0.35rem 0.875rem; border-radius:8px; background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--accent-color); text-transform:uppercase;">Score</div>
                            <div style="font-size:1.1rem; font-weight:800; color:var(--accent-color);">
                                <?= number_format($summary['total_obtained'], 0) ?> / <?= number_format($summary['total_max'], 0) ?>
                            </div>
                        </div>
                        <div style="padding:0.35rem 0.875rem; border-radius:8px; background:rgba(139,92,246,0.09); border:1px solid rgba(139,92,246,0.22); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:#7c3aed; text-transform:uppercase;">Percentage</div>
                            <div style="font-size:1.1rem; font-weight:800; color:#7c3aed;"><?= number_format($summary['percentage'], 1) ?>%</div>
                        </div>
                        <div>
                            <?php if ($summary['overall_result'] === 'PASS'): ?>
                                <span class="badge badge-success" style="padding:0.5rem 1rem; font-size:0.875rem; border-radius:8px;">PASS</span>
                            <?php elseif ($summary['overall_result'] === 'FAIL'): ?>
                                <span class="badge badge-danger"   style="padding:0.5rem 1rem; font-size:0.875rem; border-radius:8px;">FAIL</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Marks table -->
                <div style="overflow-x:auto;">
                    <table class="table" style="text-align:left; font-size:0.875rem;">
                        <thead>
                            <tr>
                                <th style="width:100px;">Subject Code</th>
                                <th>Subject Name</th>
                                <th style="text-align:center; width:90px;">Marks Obtained</th>
                                <th style="text-align:center; width:90px;">Max Marks</th>
                                <th style="text-align:center; width:80px;">Percentage</th>
                                <th style="text-align:center; width:80px;">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subjects)): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--text-secondary);">No subject marks available.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subjects as $sub): ?>
                                    <tr>
                                        <td style="font-weight:700; color:var(--accent-color);"><?= e($sub['subject_code']) ?></td>
                                        <td style="font-weight:500; color:var(--text-primary);"><?= e($sub['subject_name']) ?></td>
                                        <td style="text-align:center; font-weight:700;"><?= number_format($sub['marks_obtained'], 1) ?></td>
                                        <td style="text-align:center; color:var(--text-secondary);"><?= number_format($sub['max_marks'], 0) ?></td>
                                        <td style="text-align:center;"><?= number_format($sub['percentage'], 1) ?>%</td>
                                        <td style="text-align:center;">
                                            <?php if ($sub['result'] === 'PASS'): ?>
                                                <span style="display:inline-block; padding:0.2rem 0.6rem; border-radius:20px; background:rgba(16,185,129,0.12); color:#059669; font-size:0.7rem; font-weight:800; border:1px solid rgba(16,185,129,0.3);">PASS</span>
                                            <?php else: ?>
                                                <span style="display:inline-block; padding:0.2rem 0.6rem; border-radius:20px; background:rgba(239,68,68,0.1); color:#dc2626; font-size:0.7rem; font-weight:800; border:1px solid rgba(239,68,68,0.25);">FAIL</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php elseif ($isSem):
            $semRes = getSemResult($ayBlock, $tab['semNum']);
            $isPublished = $semRes !== null && (int)($semRes['published'] ?? 0) === 1;
        ?>

            <!-- ═══ SEMESTER RESULT PANEL ════════════════════════ -->
            <?php if (!$semRes || !$isPublished): ?>
                <!-- Not published -->
                <div style="text-align:center; padding:2rem 1rem;">
                    <div style="font-size:2.5rem; margin-bottom:0.5rem;">📊</div>
                    <h3 style="font-size:1.1rem; font-weight:700; color:var(--text-primary); margin:0 0 0.4rem;">
                        <?= e($tab['label']) ?> — Results Not Published Yet
                    </h3>
                    <p style="color:var(--text-secondary); font-size:0.875rem; max-width:420px; margin:0 auto;">
                        Semester results are either being evaluated or pending official publishing by the examination cell.
                    </p>
                    <span style="display:inline-block; margin-top:1rem; padding:0.35rem 1rem; border-radius:20px; background:rgba(245,158,11,0.12); color:#92710a; font-size:0.78rem; font-weight:700; border:1px solid rgba(245,158,11,0.35);">
                        NOT PUBLISHED
                    </span>
                </div>
            <?php else: ?>
                <!-- Semester Header -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:1rem; padding-bottom:1rem; border-bottom:1px solid var(--border-color);">
                    <div>
                        <span style="font-size:0.7rem; text-transform:uppercase; font-weight:700; color:var(--accent-color); letter-spacing:0.06em;">
                            <?= e($semRes['course_name']) ?>  ·  <?= e($ayBlock['academic_year_name']) ?>
                        </span>
                        <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin:0.2rem 0 0;">
                            <?= e($semRes['semester_name'] ?? ('Semester ' . $tab['semNum'])) ?>
                            <span style="font-size:0.85rem; color:var(--text-secondary); font-weight:600;">— Official Marksheet</span>
                        </h2>
                    </div>
                    <!-- SGPA / CGPA / Grade chips -->
                    <div style="display:flex; gap:0.625rem; flex-wrap:wrap; align-items:center;">
                        <div style="padding:0.4rem 0.875rem; border-radius:8px; background:rgba(37,99,235,0.08); border:1px solid rgba(37,99,235,0.2); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--accent-color); text-transform:uppercase;">Semester SGPA</div>
                            <div style="font-size:1.25rem; font-weight:800; color:var(--accent-color);"><?= number_format((float)($semRes['sgpa'] ?? 0), 2) ?> <span style="font-size:0.7rem; color:var(--text-secondary);">/ 10</span></div>
                        </div>
                        <div style="padding:0.4rem 0.875rem; border-radius:8px; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--success); text-transform:uppercase;">Cumulative CGPA</div>
                            <div style="font-size:1.25rem; font-weight:800; color:var(--success);"><?= number_format((float)($semRes['cgpa'] ?? 0), 2) ?> <span style="font-size:0.7rem; color:var(--text-secondary);">/ 10</span></div>
                        </div>
                        <div style="padding:0.4rem 0.875rem; border-radius:8px; background:var(--bg-main); border:1px solid var(--border-color); text-align:center;">
                            <div style="font-size:0.65rem; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Total Marks</div>
                            <div style="font-size:1.1rem; font-weight:700; color:var(--text-primary);">
                                <?= number_format($semRes['total_marks'], 1) ?>
                                <span style="font-size:0.8rem; color:var(--accent-color); font-weight:700;">(<?= e($semRes['grade']) ?>)</span>
                            </div>
                            <div style="font-size:0.62rem; color:var(--text-secondary);">
                                Credits: <?= number_format((float)($semRes['earned_credits'] ?? 0), 1) ?> / <?= number_format((float)($semRes['sem_credits'] ?? 0), 1) ?>
                            </div>
                        </div>
                        <div>
                            <?php if ($semRes['result'] === 'pass'): ?>
                                <span class="badge badge-success" style="padding:0.5rem 1rem; font-size:0.875rem; border-radius:8px;">PASSED</span>
                            <?php elseif ($semRes['result'] === 'withheld'): ?>
                                <span style="padding:0.5rem 1rem; border-radius:8px; background:rgba(245,158,11,0.15); color:#92710a; font-size:0.875rem; font-weight:700; border:1px solid rgba(245,158,11,0.4);">WITHHELD</span>
                            <?php else: ?>
                                <span class="badge badge-danger" style="padding:0.5rem 1rem; font-size:0.875rem; border-radius:8px;">FAILED / BACKLOG</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Subject marks table -->
                <div style="overflow-x:auto;">
                    <table class="table" style="font-size:0.875rem;">
                        <thead>
                            <tr>
                                <th style="width:100px;">Subject Code</th>
                                <th>Subject Title</th>
                                <th style="text-align:center; width:70px;">Credits</th>
                                <th style="text-align:center;">Internal CIA</th>
                                <th style="text-align:center;">External Exam</th>
                                <th style="text-align:right; width:70px;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($semRes['subjects'])): ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; color:var(--text-secondary);">Detailed subject marks breakdown pending.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($semRes['subjects'] as $sub): ?>
                                    <tr>
                                        <td style="font-weight:700; color:var(--accent-color);"><?= e($sub['subject_code']) ?></td>
                                        <td style="font-weight:500; color:var(--text-primary);"><?= e($sub['subject_name']) ?></td>
                                        <td style="text-align:center; color:var(--text-secondary);"><?= number_format((float)$sub['credits'], 1) ?></td>
                                        <td style="text-align:center;"><?= $sub['internal_marks'] !== null ? number_format((float)$sub['internal_marks'], 1) : '<span style="color:var(--text-secondary);">N/A</span>' ?></td>
                                        <td style="text-align:center;"><?= number_format((float)$sub['external_marks'], 1) ?> / <?= number_format((float)$sub['external_max'], 0) ?></td>
                                        <td style="text-align:right;">
                                            <span style="font-weight:800; font-size:0.9375rem; color:<?= gradeColor($sub['grade'] ?? 'F') ?>;">
                                                <?= e($sub['grade'] ?? 'F') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Semester summary footer -->
                <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                    <?php
                    $pct = (float)($semRes['percentage'] ?? 0);
                    $passed = 0; $failed = 0;
                    foreach ($semRes['subjects'] as $ss) {
                        if (($ss['grade'] ?? 'F') !== 'F') $passed++; else $failed++;
                    }
                    ?>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">
                        <span style="font-weight:700; color:var(--text-primary);">Total Subjects:</span> <?= count($semRes['subjects']) ?>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">
                        <span style="font-weight:700; color:#10b981;">Subjects Passed:</span> <?= $passed ?>
                    </div>
                    <?php if ($failed > 0): ?>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">
                        <span style="font-weight:700; color:#ef4444;">Subjects Failed:</span> <?= $failed ?>
                    </div>
                    <?php endif; ?>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">
                        <span style="font-weight:700; color:var(--text-primary);">Percentage:</span> <?= number_format($pct, 2) ?>%
                    </div>
                    <?php if (!empty($semRes['published_at'])): ?>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">
                        <span style="font-weight:700; color:var(--text-primary);">Published:</span>
                        <?= date('d M Y', strtotime($semRes['published_at'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
    <?php endforeach; /* tab panels */ ?>

</div><!-- /.ay-panel -->
<?php endforeach; /* academic years */ ?>
<?php endif; /* empty check */ ?>

<!-- ════════════════════════════════════════════════════════════════
     JS — Tab + AY switching (zero dependencies)
════════════════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    // Active tab per AY (ayPanelId => tabId)
    var activeTab = {};

    // Activate a tab within a given AY panel
    function switchTab(tabId, ayPanId) {
        // Deactivate all tabs and panels in this AY
        document.querySelectorAll('.exam-tab[data-ay="' + ayPanId + '"]').forEach(function (btn) {
            btn.style.borderBottomColor = 'transparent';
            btn.style.color = 'var(--text-secondary)';
            btn.style.background = 'transparent';
        });
        document.querySelectorAll('.tab-panel[data-ay="' + ayPanId + '"]').forEach(function (panel) {
            panel.style.display = 'none';
        });

        // Activate selected tab
        var tabBtn = document.getElementById('tab-' + tabId);
        if (tabBtn) {
            tabBtn.style.borderBottomColor = 'var(--accent-color)';
            tabBtn.style.color = 'var(--accent-color)';
            tabBtn.style.background = 'rgba(2,132,199,0.06)';
        }

        // Show selected panel
        var panel = document.getElementById('panel-' + tabId);
        if (panel) panel.style.display = 'block';

        activeTab[ayPanId] = tabId;
    }

    // Switch visible academic year
    function switchAY(ayPanId) {
        document.querySelectorAll('.ay-panel').forEach(function (p) {
            p.style.display = 'none';
        });
        var target = document.getElementById(ayPanId);
        if (target) target.style.display = 'block';

        // Activate first tab for this AY if none active yet
        if (!activeTab[ayPanId]) {
            var firstTab = target.querySelector('.exam-tab');
            if (firstTab) {
                var tid = firstTab.id.replace('tab-', '');
                switchTab(tid, ayPanId);
            }
        }
    }

    // Expose globally for inline onclick handlers
    window.switchTab = switchTab;
    window.switchAY  = switchAY;

    // Initialise — open first AY, first tab
    document.addEventListener('DOMContentLoaded', function () {
        var firstAY = document.querySelector('.ay-panel');
        if (!firstAY) return;
        var ayPanId = firstAY.id;
        var firstTab = firstAY.querySelector('.exam-tab');
        if (firstTab) {
            var tid = firstTab.id.replace('tab-', '');
            switchTab(tid, ayPanId);
        }
    });
})();
</script>

<!-- ════════════════════════════════════════════════════════════════
     PRINT STYLES
════════════════════════════════════════════════════════════════ -->
<style>
@media print {
    .sidebar, nav, .btn-primary,
    .page-header > div:last-child,
    #aySelector, label[for="aySelector"],
    .exam-tab { display: none !important; }

    .ay-panel  { display: block !important; }
    .tab-panel { display: block !important; page-break-inside: avoid; margin-bottom: 1.5rem; }

    body, .card, .ay-panel { background: #fff !important; box-shadow: none !important; }
    table { font-size: 0.75rem !important; }
    th, td { padding: 4px 6px !important; }
}
</style>
