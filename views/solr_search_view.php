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
<div class="query-result">
    <div class="row">
        <div class="query-result-header">
            <div class="num-matches col-md-2"><?=$solrresult->numFound?> matches</div>
                <div class="ten columns col-md-8"><?=(isset($nav)) ? $nav : FALSE?></div>
            <?php 
                $downlstr = 'q=' .$solrresult->params->q;
                if (isset($solrresult->params->fq)) {
                    if (is_array($solrresult->params->fq)) {
                        foreach ($solrresult->params->fq as $fq) {
                            $downlstr .= '&fq=' . urlencode($fq);
                        }
                    }
                    else
                        $downlstr .= '&fq=' . urlencode($solrresult->params->fq);
                }
            ?>
            <div class="download col-md-2 text-right"><?=anchor(site_url() . 'flora/download?' . $downlstr, 'Download', array('class' => 'btn btn-default', 'role' => 'button'));?></div>
        </div>
    </div>
    
    
    
    <!--div>
        <?php /*
            $options = array(
                    '',
                    'extinct' => 'extinct',
                    'endemic' => 'endemic',
                    'introduced' => 'introduced',
                    'vrot' => 'VROT',
                    'epbc' => 'EPBC',
                    'ffg' => 'FFG',
                );
            if (isset($this->session->userdata['id']))
                $options['profile'] = 'profile';
        */ ?>
        <?/*=form_dropdown('symbol',
                $options,
                $this->input->get('show_symbol'),
                'id="symbol" class="three columns alpha"'
            ); 
        */?>
    </div-->

        <?php 
            foreach($solrresult->docs as $row): 
                if ($row->taxonomic_status == 'accepted') {
                    $nameclass = 'currentname';
                    $colspan = 'col-lg-9 col-md-8';
                }
                else {
                    $nameclass = 'oldname';
                    $colspan = 'col-lg-12';
                }
                
                $edit = '';
                $addchild = '';
                if (isset($this->session->userdata['id'])) {
                    $edit = ' edit';
                    if ($row->taxonomic_status == 'accepted' && in_array($row->taxon_rank, array(
                        'order',
                        'family',
                        'genus',
                        'species',
                    ))) {
                        $addchild = ' add-child';
                    }
                }
                
                $italic = '';
                if (in_array($row->taxon_rank, array(
                    'genus',
                    'species',
                    'subspecies',
                    'variety',
                    'subvariety',
                    'forma',
                    'subforma',
                    'cultivar',
                ))) {
                    $italic = ' italic';
                }
                
                $name = '<span class="namebit">';
                $name .= str_replace(array(' subsp. ', ' var. ', ' f. ', 'nothosubsp.', 'nothovar.'), array(
                    '</span> subsp. <span class="namebit">', 
                    '</span> var. <span class="namebit">', '</span> f. <span class="namebit">',
                    '</span> nothosubsp. <span class="namebit">', 
                    '</span> nothovar. <span class="namebit">'
                    ), $row->scientific_name);
                $name .= '</span>'
        ?>
    
    
        <div class="search-name-entry">
            <div class="row">

            <!--div class="symbols one column alpha">
                <?php if ($row->occurrence_status && in_array('endemic', $row->occurrence_status)): ?>
                <span class="endemic" title="endemic">&dtrif;</span>
                <?php endif; ?>
                <?php if ($row->occurrence_status && in_array('extinct', $row->occurrence_status)): ?>
                <span class="extinct" title="endemic">&dagger;</span>
                <?php endif; ?>
                <?php if ($row->establishment_means == 'introduced'): ?>
                <span class="introduced" title="introduced">*</span>
                <?php endif; ?>
                <?php if ($row->threat_status):?>
                <?php foreach ($row->threat_status as $stat): ?>
                <?php if (substr($stat, 0, strlen('VROT')) == 'VROT' ): ?>
                <span class="vrot" title="EPBC"><?=substr($stat, 5)?></span>
                <?php endif;?>
                <?php endforeach; ?>
                
                <?php foreach ($row->threat_status as $stat): ?>
                <?php if (substr($stat, 0, strlen('EPBC')) == 'EPBC' ): ?>
                <span class="epbc" title="EPBC"><?=substr($stat, 5)?></span>
                <?php endif;?>
                <?php endforeach; ?>

                <?php foreach ($row->threat_status as $stat): ?>
                <?php if ($stat == 'FFG listed'): ?>
                <span class="ffg" title="FFG">F</span>
                <?php endif;?>
                <?php endforeach; ?>
                <?php endif;?>
                <?php if ($row->profile): ?>
                <span class="profile" title="<?=$row->profile?>"><?=substr($row->profile, 0, 3);?></span>
                <?php endif;?>
            </div-->
            
            <div class="<?=$colspan?>">
                <a href="<?=base_url() . 'flora/taxon/' . $row->id; ?>">
                    <span class="<?=$nameclass . $italic . $edit . $addchild?>">
                        <?=$name?>
                        <span class="author"><?=$row->scientific_name_authorship?><?php if ($row->sensu): ?><span 
                                class="sensu">, sensu <?=$row->sensu?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </div>
            
            <?php if ($row->taxonomic_status == 'accepted'): ?>
            <div class="fam col-lg-3 col-md-4 text-right"><?=($row->family) ? $row->family : '&nbsp;'; ?></div>
            <?php elseif($row->taxonomic_status && $row->taxonomic_status != 'accepted'): ?>
            <div class="accepted-name col-lg-12">
                <?php
                    switch ($row->taxonomic_status) {
                        case 'synonym':
                            $syn = '=';
                            break;

                        case 'misapplication':
                            $syn = 'misapplied for:';
                            break;

                        default:
                            $syn = '';
                            break;
                    }
                    
                    $italic = '';
                    if (in_array($row->accepted_name_usage_taxon_rank, array(
                        'genus',
                        'species',
                        'subspecies',
                        'variety',
                        'subvariety',
                        'forma',
                        'subforma',
                        'cultivar',
                    ))) {
                        $italic = ' italic';
                    }
                
                    $name = '<span class="namebit">';
                    $name .= str_replace(array(' subsp. ', ' var. ', ' f. ', 'nothosubsp.', 'nothovar.'), array(
                    '</span> subsp. <span class="namebit">', 
                    '</span> var. <span class="namebit">', '</span> f. <span class="namebit">',
                    '</span> nothosubsp. <span class="namebit">', 
                    '</span> nothovar. <span class="namebit">'
                    ), $row->accepted_name_usage);
                    $name .= '</span>';
                    $name .= ' <span class="author">' . $row->accepted_name_usage_authorship . '</span>';

                ?>
                <?=$syn?> <span class="currentname <?=$italic?>">
                    <?=anchor(site_url() . 'flora/taxon/' . $row->accepted_name_usage_id, $name)?>
                </span>
            </div>
            <?php endif; ?>
            
        </div>
        </div> <!-- /.name-entry -->
        
        <?php endforeach; ?>
        <div class="query-result-footer">
    <?=(isset($nav)) ? $nav : FALSE; ?>
        </div>
</div> <!-- /.query-result -->

<?php endif; ?>
</div> <!-- /#right-main-panel -->

<?=form_close(); ?>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
