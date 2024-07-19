<div class="table-wrapper">
<?php foreach ($block->tableitem()->toStructure() as $item): ?>
<div class="table-layout">
        <p><?= $item->category() ?></p>
        <p><?= $item->value() ?></p>
</div>
<?php endforeach ?>
</div>