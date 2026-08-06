<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canManage) ? '1fr 2fr' : '1fr' ?>; gap: 1.5rem;">
    <?php if (!empty($canManage)): ?>
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Add Book to Catalog</h2>
        </div>

        <form method="POST" action="/library">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="title">Book Title *</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Introduction to Algorithms">
            </div>

            <div class="form-group">
                <label class="form-label" for="author">Author *</label>
                <input type="text" id="author" name="author" class="form-control" required placeholder="e.g. Thomas H. Cormen">
            </div>

            <div class="form-group">
                <label class="form-label" for="isbn">ISBN Number</label>
                <input type="text" id="isbn" name="isbn" class="form-control" placeholder="e.g. 978-0262033848">
            </div>

            <div class="form-group">
                <label class="form-label" for="category">Category / Subject</label>
                <input type="text" id="category" name="category" class="form-control" placeholder="e.g. Computer Science">
            </div>

            <div class="form-group">
                <label class="form-label" for="total_copies">Total Copies</label>
                <input type="number" id="total_copies" name="total_copies" class="form-control" value="5" min="1">
            </div>

            <button type="submit" class="btn-primary">Add Book</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Library Book Inventory</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Title</th>
                    <th style="padding: 0.75rem;">Author</th>
                    <th style="padding: 0.75rem;">Category</th>
                    <th style="padding: 0.75rem;">Available / Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No books in catalog yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $b): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($b['title']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($b['author']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($b['category']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #86efac;"><?= e($b['available_copies']) ?> / <?= e($b['total_copies']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
