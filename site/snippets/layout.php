<?php foreach ($page->layout()->toLayouts() as $layout): ?>
    <div class="layout-section">
        <?php foreach ($layout->columns() as $column): ?>
            <div class="layout-column" style="flex: <?= $column->width() ?>;">
                <?php foreach ($column->blocks() as $block): ?>
                    <?php 
                    switch ($block->type()) {
                        case 'heading':
                            snippet('blocks/heading', ['block' => $block]);
                            break;
                        case 'text':
                            snippet('blocks/text', ['block' => $block]);
                            break;
                        case 'image':
                            snippet('blocks/image', ['block' => $block]);
                            break;
                        case 'table':
                            snippet('blocks/table', ['block' => $block]);
                            break;
                        case 'video':
                            snippet('blocks/video', ['block' => $block]);
                            break;
                        default:
                            snippet('blocks/' . $block->_key(), ['block' => $block]);
                            break;
                    }
                    ?>
                <?php endforeach ?>
                
            </div>
        <?php endforeach ?>
    </div>
<?php endforeach ?>