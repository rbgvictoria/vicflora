<!-- Taxonomic status -->
<div class="facet" data-vicflora-facet-name="taxonomicStatus">
<?php 
    $values = array();
    if ($this->input->get('taxonomicStatus'))
        $values = explode(',', $this->input->get('taxonomicStatus'))
?>
    <h4>Taxonomic status</h4>
    <ul>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'taxonomicStatus_accepted',
                    'value' => 'accepted',
                    'checked' => in_array('accepted', $values)
                ));
            ?>
            <?php $num = isset($facets['taxonomicStatus']['accepted']) ? $facets['taxonomicStatus']['accepted'] : 0; ?>
            <?=form_label('Accepted (' . $num . ')', 'taxonomicStatus_accepted'); ?>
        </li>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'taxonomicStatus_notCurrent',
                    'value' => 'notCurrent',
                    'checked' => in_array('notCurrent', $values)
                ));
            ?>
            <?php $num = isset($facets['taxonomicStatus']['notCurrent']) ? $facets['taxonomicStatus']['notCurrent'] : 0; ?>
            <?=form_label('Not current (' . $num . ')', 'taxonomicStatus_notCurrent'); ?>
        </li>
    </ul>
    <?php
        if ($this->input->get('taxonomicStatus'))
            echo form_hidden('taxonomicStatus', $this->input->get('taxonomicStatus'));
    ?>
</div>

<div class="facet" data-vicflora-facet-name="taxonType">
    <?php
        $values = array();
        if ($this->input->get('taxonType')) {
            $values = explode(',', $this->input->get('taxonType'));
        }
    ?>
    <h4>Taxon type</h4>
    <ul>
        <li>
            <?=form_checkbox(array(
                'id' => 'taxonType_endTaxa',
                'value' => 'endTaxa',
                'checked' => in_array('endTaxa', $values)
            )); ?>
            <?php $num = (isset($facets['taxonType']['endTaxa'])) ? $facets['taxonType']['endTaxa'] : 0; ?>
            <?=form_label('End taxa (' . $num . ')', 'taxonType_endTaxa'); ?>
        </li>
        <li>
            <?=form_checkbox(array(
                'id' => 'taxonType_parentTaxa',
                'value' => 'parentTaxa',
                'checked' => in_array('parentTaxa', $values)
            )); ?>
            <?php $num = (isset($facets['taxonType']['parentTaxa'])) ? $facets['taxonType']['parentTaxa'] : 0; ?>
            <?=form_label('Parent taxa (' . $num . ')', 'taxonType_parentTaxa') ?>
        </li>
    </ul>
    <?=($values) ? form_hidden('taxonType', $this->input->get('taxonType')) : FALSE; ?>
</div>

<div class="facet" data-vicflora-facet-name="occurrenceStatus">
<?php 
    $values = array();
    if ($this->input->get('occurrenceStatus'))
        $values = explode(',', $this->input->get('occurrenceStatus'))
?>
    <h4>Occurrence status</h4>
    <ul>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'occurrenceStatus_present',
                    'value' => 'present',
                    'checked' => in_array('present', $values)
                ));
            ?>
            <?php $num = isset($facets['occurrenceStatus']['present']) ? $facets['occurrenceStatus']['present'] : 0; ?>
            <?=form_label('Present (' . $num . ')', 'occurrenceStatus_present'); ?>
            <ul>
                <li>
                    <?=form_checkbox(
                        array(
                            'id' => 'occurrenceStatus_endemic',
                            'value' => 'endemic',
                            'checked' => in_array('endemic', $values)
                        ));
                    ?>
                    <?php $num = isset($facets['occurrenceStatus']['endemic']) ? $facets['occurrenceStatus']['endemic'] : 0; ?>
                    <?=form_label('Endemic (' . $num . ')', 'occurrenceStatus_endemic'); ?>
                </li>
            </ul>
        </li>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'occurrenceStatus_absent',
                    'value' => 'absent',
                    'checked' => in_array('absent', $values)
                ));
            ?>
            <?php $num = isset($facets['occurrenceStatus']['absent']) ? $facets['occurrenceStatus']['absent'] : 0; ?>
            <?=form_label('Absent (' . $num . ')', 'occurrenceStatus_absent'); ?>
            <ul>
                <li>
                    <?=form_checkbox(
                        array(
                            'id' => 'occurrenceStatus_extinct',
                            'value' => 'extinct',
                            'checked' => in_array('extinct', $values)
                        ));
                    ?>
                    <?php $num = isset($facets['occurrenceStatus']['extinct']) ? $facets['occurrenceStatus']['extinct'] : 0; ?>
                    <?=form_label('Extinct (' . $num . ')', 'occurrenceStatus_extinct'); ?>
                </li>
            </ul>
        </li>
    </ul>
    <?php
        if ($this->input->get('occurrenceStatus'))
            echo form_hidden('occurrenceStatus', $this->input->get('occurrenceStatus'));
    ?>
</div>

<div class="facet" data-vicflora-facet-name="establishmentMeans">
<?php 
    $values = array();
    if ($this->input->get('establishmentMeans'))
        $values = explode(',', $this->input->get('establishmentMeans'))
?>
    <h4>Establishment means</h4>
    <ul>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'establishmentMeans_native',
                    'value' => 'native',
                    'checked' => in_array('native', $values)
                ));
            ?>
            <?php $num = isset($facets['establishmentMeans']['native']) ? $facets['establishmentMeans']['native'] : 0; ?>
            <?=form_label('Native (' . $num . ')', 'establishmentMeans_accepted'); ?>
        </li>
        <li>
            <?=form_checkbox(
                array(
                    'id' => 'establishmentMeans_introduced',
                    'value' => 'introduced',
                    'checked' => in_array('introduced', $values)
                ));
            ?>
            <?php $num = isset($facets['establishmentMeans']['introduced']) ? $facets['establishmentMeans']['introduced'] : 0; ?>
            <?=form_label('Introduced (' . $num . ')', 'establishmentMeans_introduced'); ?>
        </li>
    </ul>
    <?php
        if ($this->input->get('establishmentMeans'))
            echo form_hidden('establishmentMeans', $this->input->get('establishmentMeans'));
    ?>
</div>

<?php 
    $items = array(
        'EX' => 'EX',
        'CR' => 'CR',
        'EN' => 'EN',
        'VU' => 'VU',
    );
    
    $values = array();
    if ($this->input->get('epbc'))
        $values = explode(',', $this->input->get('epbc'))
?>
<div class="facet" data-vicflora-facet-name="epbc">
    <h4>EPBC</h4>
    <ul>
        <?php foreach($items as $val => $label): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => "epbc_$val",
                    'value' => $val,
                    'checked' => in_array($val, $values)
                ));
            ?>
            <?php $num = isset($facets['epbc'][$val]) ? $facets['epbc'][$val] : 0; ?>
            <?=form_label($label . ' (' . $num . ')', "epbc_$val"); ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get('epbc'))
            echo form_hidden('epbc', $this->input->get('epbc'));
    ?>
</div>

<?php 
    $items = array(
        'x' => 'Extinct',
        'e' => 'Endangered',
        'v' => 'Vulnerable',
        'r' => 'Rare',
        'k' => 'Data deficient',
    );
    
    $values = array();
    if ($this->input->get('vrot'))
        $values = explode(',', $this->input->get('vrot'))
?>
<div class="facet" data-vicflora-facet-name="vrot">
    <h4>VROT</h4>
    <ul>
        <?php foreach($items as $val => $label): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => "vrot_$val",
                    'value' => $val,
                    'checked' => in_array($val, $values)
                ));
            ?>
            <?php $num = isset($facets['vrot'][$val]) ? $facets['vrot'][$val] : 0; ?>
            <?=form_label($label . ' (' . $num . ')', "vrot_$val"); ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get('vrot'))
            echo form_hidden('vrot', $this->input->get('vrot'));
    ?>
</div>

<?php 
    $items = array(
        'L' => 'Listed',
    );
    
    $values = array();
    if ($this->input->get('ffg'))
        $values = explode(',', $this->input->get('ffg'))
?>
<div class="facet" data-vicflora-facet-name="ffg">
    <h4>Flora and Fauna Guarantee Act (FFG)</h4>
    <ul>
        <?php foreach($items as $val => $label): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => "ffg_$val",
                    'value' => $val,
                    'checked' => in_array($val, $values)
                ));
            ?>
            <?php $num = isset($facets['ffg'][$val]) ? $facets['ffg'][$val] : 0; ?>
            <?=form_label($label . ' (' . $num . ')', "ffg_$val"); ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get('ffg'))
            echo form_hidden('ffg', $this->input->get('ffg'));
    ?>
</div>

<?php 
    $items = $facetIBRA;
    
    $values = array();
    if ($this->input->get('ibra'))
        $values = explode(',', $this->input->get('ibra'))
?>
<div class="facet collapsible" data-vicflora-facet-name="ibra">
    <h4>IBRA 6.1 regions</h4>
    <ul>
        <?php foreach($items as $val => $label): ?>
        <?php if($facets['ibra'][$val]): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => "ibra_$val",
                    'value' => $val,
                    'checked' => in_array($val, $values)
                ));
            ?>
            <?=form_label($label . ' (' . $facets['ibra'][$val] . ')', "ibra_$val"); ?>
        </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get('ibra'))
            echo form_hidden('ibra', $this->input->get('ibra'));
    ?>
</div>

<?php 
    $items = $facetIBRASub;
    
    $values = array();
    if ($this->input->get('ibra_sub'))
        $values = explode(',', $this->input->get('ibra_sub'))
?>
<div class="facet collapsible" data-vicflora-facet-name="ibra_sub">
    <h4>IBRA 6.1 subregions</h4>
    <ul>
        <?php foreach($items as $val => $label): ?>
        <?php if($facets['ibra_sub'][$val]): ?>
        <li>
            <?=form_checkbox(
                array(
                    'id' => "ibra_sub_$val",
                    'value' => $val,
                    'checked' => in_array($val, $values)
                ));
            ?>
            <?=form_label($label . ' (' . $facets['ibra_sub'][$val] . ')', "ibra_sub_$val"); ?>
        </li>
        <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php
        if ($this->input->get('ibra_sub'))
            echo form_hidden('ibra_sub', $this->input->get('ibra_sub'));
    ?>
</div>

