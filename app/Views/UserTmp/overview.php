
<h2><?= esc($title) ?></h2>

<?php if (! empty($usertmp) && is_array($usertmp)): ?>

    <?php foreach ($usertmp as $usertmp_item): ?>

        <h3><?= esc($usertmp_item['title']) ?></h3>

        <div class="main">
            <?= esc($usertmp_item['body']) ?>
        </div>
        <p><a href="/usertmp/<?= esc($usertmp_item['token'], 'url') ?>">View article</a></p>

    <?php endforeach ?>

<?php else: ?>

    <h3>No UserTmp</h3>

    <p>Unable to find any UserTmp for you.</p>

<?php endif ?>