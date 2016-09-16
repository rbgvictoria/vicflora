<?php if(isset($solrresult->facets)):?>

<?php 
    $q = 'q='.$solrresult->params->q;
    $filters = array();
    if (isset($solrresult->params->fq)) {
        if (is_array($solrresult->params->fq)) {
            foreach ($solrresult->params->fq as $filter) {
                $bits = explode(':', $filter);
                $filters[$bits[0]] = $bits[1];
            }
        }
        else {
                $bits = explode(':', $solrresult->params->fq);
                $filters[$bits[0]] = $bits[1];
        }
    }
?>

<div class="facets">
    <h3>Filters</h3>
    <div class="content form-horizontal">
    <?php foreach($solrresult->facets as $facet):?>
    <?php if (!(count($facet['items']) == 1 && !$facet['items'][0]['name'])):?>
    <div class="facet collapsible" data-vicflora-facet-name="<?=$facet['name']?>">
        <?php 
            $checkeditems = array();
            if (isset($filters[$facet['name']])) {
                $str = trim($filters[$facet['name']], " ()");
                $checkeditems = explode(' OR ', $str);
            }
        ?>
        <h4><?=$facet['label']?></h4>
        <ul class="form-group">
            <?php foreach($facet['items'] as $item): ?>
            <?php
                if (!$item['name']) {
                    if (!$item['count']) {
                        continue;
                    }
                    $item['label'] = '(blanks)';
                } 
                $istr = (strpos($item['name'], ' ')) ? '"' . $item['name'] . '"' : $item['name'];
                $istr = str_replace(array('[', ']'), array('\\[', '\\]'), $istr);
                $fqarray = $filters;
                if ($item['name']) {
                    $fqarray[$facet['name']] = $istr;
                }
                else {
                    $fqarray['-' . $facet['name']] = '*';
                }
                
                $fqstrarr = array();
                foreach ($fqarray as $key => $value) {
                    $fqstrarr[] = 'fq=' . urlencode($key . ':' . $value);
                }
                $fqstring = $q . '&' . implode('&', $fqstrarr);
                
                $ckbox = array(
                    'name' => FALSE,
                    'value' => $item['name'],
                    'checked' => in_array($istr, $checkeditems)
                );
                
                $indent = false;
                if (($facet['name'] == 'establishment_means' && in_array($item['name'], array('also naturalised', 'naturalised', 'adventive'))) || 
                        ($facet['name'] == 'occurrence_status' && in_array($item['name'], array('endemic'))))
                    $indent = ' indent';
            ?>
            <li class="checkbox<?=$indent?>">
                <?=form_checkbox($ckbox)?>
                <label><?=anchor(site_url() . 'flora/search?' . $fqstring, $item['label'] . ' (' . $item['count'] . ')' )?></label></li>
            <?php endforeach; ?>
        </ul>
        <?php
            if (isset($filters[$facet['name']])) {
                echo form_hidden($facet['name'], $filters[$facet['name']]);
            }
        ?>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

