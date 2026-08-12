<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\trainings.php
$isAdmin = in_array($role, ['super_admin', 'admin', 'tpo'], true);
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">📚 Pre-Placement Training Logs</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Schedule mock interviews, aptitude training workshops, and guest seminars.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="badge badge-danger" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ⚠️ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="badge badge-success" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ✓ <?= e($success) ?>
        </div>
    <?php endif; ?>

    <div class="<?= $isAdmin ? 'page-split' : 'card' ?>">
        <?php if ($isAdmin): ?>
            <!-- Left Side: Scheduling Form -->
            <div class="card">
                <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">Schedule Training Program</h2>
                
                <form method="POST" action="/placement/trainings" style="display: flex; flex-direction: column; gap: 1rem;">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label class="form-label" for="title">Session Title *</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Mock Coding Interview Prep" required>
                    </div>

                    <div>
                        <label class="form-label" for="trainer_name">Trainer / Guest Expert *</label>
                        <input type="text" name="trainer_name" id="trainer_name" class="form-control" placeholder="e.g. Mr. Sridhar (Amazon Alumni)" required>
                    </div>

                    <div>
                        <label class="form-label" for="topic">Core Topic / Syllabus</label>
                        <input type="text" name="topic" id="topic" class="form-control" placeholder="e.g. Algorithms & Data Structures Complexity">
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 0.5rem;">
                        <div>
                            <label class="form-label" for="scheduled_date">Schedule Datetime *</label>
                            <input type="datetime-local" name="scheduled_date" id="scheduled_date" class="form-control" required style="height: 38px;">
                        </div>
                        <div>
                            <label class="form-label" for="duration_hours">Duration (Hours) *</label>
                            <input type="number" name="duration_hours" id="duration_hours" class="form-control" value="2" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                        💾 Schedule Training Session
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Right Side: Scheduled Training List -->
        <div>
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Pre-Placement Training Calendar</h2>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Training Title & Topic</th>
                            <th>Trainer Name</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($trainings)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 3rem;">📭 No training sessions scheduled.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($trainings as $t): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($t['title']) ?></strong><br>
                                        <small class="text-secondary">Topic: <?= e($t['topic'] ?? 'General Prep') ?></small>
                                    </td>
                                    <td>
                                        <strong><?= e($t['trainer_name']) ?></strong>
                                    </td>
                                    <td>
                                        <strong style="color: var(--accent-color);"><?= date('d M Y, h:i A', strtotime($t['scheduled_date'])) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-success"><?= (int)$t['duration_hours'] ?> Hours</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
