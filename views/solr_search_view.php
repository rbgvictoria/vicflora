<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
    <?=form_open('flora/search', array('method' => 'get')); ?>
    <div id="full-width-panel" class="col-lg-12">
        <div id="search" class="clearfix">
            <?php 
                $initval = $solrresult->params->q;
                if ($initval == '*:*') $initval = FALSE;
                $initval = str_replace('\\', '', $initval);
                if (!strpos($initval, ':')) $initval = trim($initval, ' *');
            ?>
            <div class="form-inline">
                <div class="form-group">
                    <?=form_input(array('name' => 'q', 'id' => 'term', 'value' => $initval, 'class' => 'form-control')); ?>
                    <button class="btn btn-default" type="submit">Find</button>
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group">
                    <?=form_checkbox(array('id' => 'excludeHigherTaxa', 'checked' => strpos($this->input->server('QUERY_STRING'), 'end_or_higher_taxon:end'), 'value'=> '1', 'class'=>'form-control'));?>
                    <?=form_label('Exclude higher taxa', 'excludeHigherTaxa', array('class' => 'control-label')); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div id="left-side-panel" class="col-md-4">
    <div class="query">
        <h3>Query</h3>
        <div class="content">
            <div class="query-term">
                <span class="h4">Query term:</span> <?=$solrresult->params->q;?>
                <?php
                    if (trim($solrresult->params->q) && $solrresult->params->q != '*:*') {
                        $qstr = 'q=*:*';
                        if (isset($solrresult->params->fq)) {
                            if (is_array($solrresult->params->fq)) {
                                $arr = array();
                                foreach($solrresult->params->fq as $fq) {
                                    if (substr($fq, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon')
                                        $arr[] = 'fq=' . urlencode($fq);
                                }
                                $qstr .= '&' . implode('&', $arr);
                            }
                            else {
                                if (substr($solrresult->params->fq, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon') {
                                    $qstr .= '&fq=' . urlencode($solrresult->params->fq);
                                }
                            }
                        }
                        echo '<a href="' . site_url() . 'flora/search?' . $qstr . '"><img src="' . 
                                base_url() . 'css/images/icon_delete.png" alt="clear" width="10" height="12" /></a>';
                    }
                ?>
            </div>
    <?php if(isset($solrresult->params->fq)): ?>
    <?php 
        $q = 'q='.$solrresult->params->q;
        $filters = array();
        if (is_array($solrresult->params->fq)) {
            foreach ($solrresult->params->fq as $filter) {
                if (substr($filter, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon')
                    $filters[] = $filter;
            }
        }
        else {
                if (substr($solrresult->params->fq, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon')
                    $filters[] = $solrresult->params->fq;
        }
    ?>
            <?php if($filters):?>
            <h4 class="fqs">Filter queries</h4>
            <ul>
            <?php foreach($filters as $index => $fq):?>
                <?php 
                    $fqarr = explode(':', $fq);

                    $fcts = array();
                    foreach ($solrresult->facets as $facet)
                        $fcts[] = $facet['name'];

                    $key = array_search($fqarr[0], $fcts);
                    $fq = '<b>' . $solrresult->facets[$key]['label'] . ':</b> ' . $fqarr[1];
                    
                    $endOrHigher = false;
                    if ($fqarr[0] == 'end_or_higher_taxon')
                        $endOrHigher = true;
                ?>
                <?php if (!$endOrHigher): ?>
                <li><?=$fq?> 
                    <?php 
                        $fqarr = array();
                        foreach ($filters as $ind => $filter) {
                            if ($ind != $index)
                                $fqarr[] = 'fq=' . urlencode ($filter);
                        }
                        $qstring = $q;
                        if ($fqarr) {
                            $qstring .= '&' . implode('&', $fqarr);
                        }
                    ?>
                    <a href="<?=site_url() . 'flora/search?' . $qstring;?>"><img src="<?=base_url()?>css/images/icon_delete.png" alt="clear" width="10" height="12" /></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php else: ?>
                <?php
                    $fqarr = explode(':', $solrresult->params->fq);
                    $fcts = array();
                    foreach ($solrresult->facets as $facet)
                        $fcts[] = $facet['name'];

                    $key = array_search($fqarr[0], $fcts);
                    $fq = '<b>' . $solrresult->facets[$key]['label'] . ':</b> ' . $fqarr[1];

                    $endOrHigher = false;
                    if ($fqarr[0] == 'end_or_higher_taxon')
                        $endOrHigher = true;
                ?>
                <?php if(!$endOrHigher):?>
                <li><?=$fq?>
                <a href="<?=site_url() . 'flora/search?' . $q;?>"><img src="<?=base_url()?>css/images/icon_delete.png" alt="clear" width="10" height="12" /></a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
            <?php endif;?>
        </div>
    </div>
    <?php require_once('solr_facets.php'); ?>
</div>

<div id="right-main-panel" class="col-md-8">
    <?php require_once('solr_nav.php'); ?>
    <!--div class="loading"><img src="<?=base_url()?>css/images/ajax-loader.gif" alt="Loading..." 
                              height="16" width="16" /></div-->
    
<?php if(!empty($solrresult)): ?>
<?php require_once 'solr_result.php'; ?>
<?php endif; ?>
</div> <!-- /#right-main-panel -->

<?=form_close(); ?>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
