<?php if (isset($facets)): ?>
<?php foreach ($facets as $facet): ?>
<?php 
    $values = array();
    if ($this->input->get($facet['name']))
        $values = explode(',', $this->input->get($facet['name']));
    
    $collapsible = '';
    if (isset($facet['collapsible']) && $facet['collapsible'])
        $collapsible = ' collapsible';
?>
<?php if ($facet['items']): ?>
<div class="facet<?=$collapsible?>" data-vicflora-facet-name="<?=$facet['name']?>">
    <h4><?=$facet['label']?></h4>
    <ul>
        <?php foreach ($facet['items'] as $item): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => $facet['name'] . '_' . $item['name'],
                    'value' => $item['name'],
                    'checked' => in_array($item['name'], $values)
                ));
            ?>
            <?=form_label($item['label'] . ' (' . $item['count'] . ')', $facet['name'] . '_' . $item['name']); ?>
            
            
        </li>
        
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get($facet['name']))
            echo form_hidden($facet['name'], $this->input->get($facet['name']));
    ?>
</div>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>