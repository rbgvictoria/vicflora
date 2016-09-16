<div class="query-result">
    <div class="row">
        <div class="query-result-header">
            <div class="num-matches col-sm-3"><?=$solrresult->numFound?> matches</div>
                <div class="ten columns col-sm-6"><?=(isset($nav)) ? $nav : FALSE?></div>
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
            <div class="download col-sm-3 text-right"><?=anchor(site_url() . 'flora/download?' . $downlstr, 'Download', array('class' => 'btn btn-primary', 'role' => 'button'));?></div>
        </div>
    </div>
    
    
    
        <?php 
            foreach($solrresult->docs as $row): 
                if ($row->taxonomic_status == 'accepted') {
                    $nameclass = 'currentname';
                    $colspan = 'col-lg-9 col-sm-8';
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

            <div class="<?=$colspan?>">
                <a href="<?=base_url() . 'flora/taxon/' . $row->id; ?>">
                    <span class="<?=$nameclass . $italic . $edit . $addchild?>">
                        <?=$name?>
                        <span class="author"><?=$row->scientific_name_authorship?><?php if ($row->sensu): ?><span 
                                class="sensu">, sensu <?=$row->sensu?></span>
                        <?php endif; ?><?php if ($row->vernacular_name):?><span class="vernacular-name"><?=$row->vernacular_name?></span><?php endif;?>
                    </span>
                </a>
            </div>
            
            <?php if ($row->taxonomic_status == 'accepted'): ?>
            <div class="fam col-lg-3 col-sm-4"><?=($row->family) ? $row->family : '&nbsp;'; ?></div>
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
