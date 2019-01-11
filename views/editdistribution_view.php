<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">

        <div class="taxon-edit-menu col-md-12 text-right">
            <a href="<?=site_url()?>flora/taxon/<?=$taxondata['GUID']?>">View</a>
        </div>
        <div class="col-md-12">
            <div class="taxon-name">
                <h2>
                    Edit distribution:
                    <span class="currentname <?=($taxondata['RankID']>=180) ? ' italic' : '';?>">
                        <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                                class="namebit">', '</span> var. <span class="namebit">', 
                                '</span> f. <span class="namebit">'), $taxondata['FullName']);?></span>
                    <?php if($taxondata['Author']): ?>
                        <span class="author"><?=$taxondata['Author']?></span>
                    <?php endif; ?>
                    </span>
                </h2>
            </div>
        </div>
        

        <div class="col-lg-12">
            <div class="map-frame">
                <div id="edit_distribution_map"></div>
                <div class="show-outliers">
                    <label>
                        <input type="checkbox" id="show_outliers"/>
                        Show outliers
                    </label>
                </div>
                <div id="mouse-position"></div>
            </div>
        </div>

        <div class="col-md-12">
            <div id="nodelist" class="sixteen colums clearfix"></div>
        </div>
            
            
        <div class="col-md-12">
            <div id="edit-distribution-tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#bioregions" aria-controls="bioregions" role="tab" toggle="tab">Bioregions</a>
                    </li>
                    <?php if ($overview_by_name): ?>
                    <li role="presentation">
                        <a href="#occurrences_overview" aria-controls="occurrences_overview" role="tab" toggle="tab">Overview by ALA name</a>
                    </li>
                    <?php endif;?>
                    <?php if ($assertions): ?>
                    <li role="presentation">
                        <a href="#assertions" aria-controls="assertions" role="tab" toggle="tab">Assertions</a>
                    </li>
                    <?php endif;?>
                    <?php if ($updates): ?>
                    <li role="presentation">
                        <a href="#updates" aria-controls="updates" role="tab" toggle="tab">Updated occurrences</a>
                    </li>
                    <?php endif;?>
                </ul>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane active" id="bioregions">
                        <?php if ($bioregion_table): ?>
                        <?=form_open(); ?>
                        <?=form_hidden('taxon_id', $taxondata['GUID']); ?>
                        <?=form_hidden('user_id', $this->session->userdata('id')); ?>
                        <div class="bioregion-table">
                            <?php
                                $est_options = array(
                                    'native' => 'native',
                                    'introduced' => 'introduced',
                                    'naturalised' => 'naturalised',
                                    'cultivated' => 'cultivated',
                                    'uncertain' => 'uncertain'
                                );
                                $occ_options = array(
                                    'present' => 'present',
                                    'absent' => 'absent',
                                    'extinct' => 'extinct',
                                    'doubtful' => 'doubtful',
                                    'excluded' => 'excluded'
                                );
                            ?>
                            <table class="bioregions table table-bordered table-condensed">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Bioregion</th>
                                    <th>Occurrence status</th>
                                    <th>Establishment means</th>
                                </tr>
                                <?php foreach($bioregion_table as $index=>$row):?>
                                <?php if ($row['occurrence_status'] == 'absent') $row['colour'] = '#e9e9e9;'; ?>
                                <tr>
                                    <td><span class="legend-symbol" style="background-color:<?=$row['colour']?>"></span></td>
                                    <td><?=form_hidden("sub_code_7[$index]", $row['sub_code_7'])?><?=$row['sub_name_7']?></td>
                                    <td><?=form_hidden("occurrence_status_old[$index]", $row['occurrence_status'])?><?=form_dropdown("occurrence_status[$index]", $occ_options, $row['occurrence_status'], 'class="form-control input-sm"'); ?></td>
                                    <td><?=form_hidden("establishment_means_old[$index]", $row['establishment_means'])?><?=form_dropdown("establishment_means[$index]", $est_options, $row['establishment_means'], 'class="form-control input-sm"'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <div class="text-right"><?=form_submit('editBioregions', 'Submit'); ?></div>
                        <?=form_close(); ?>
                        <?php endif; ?>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="occurrences_overview">
                        <?php if ($overview_by_name): ?>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Provided name</th>
                                    <th>Processed name</th>
                                    <th>ALA Taxon ID</th>
                                    <th># AVH</th>
                                    <th># VBA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overview_by_name as $row): ?>
                                <tr>
                                    <td><?=$row['ala_unprocessed_scientific_name']?></td>
                                    <td><?=$row['ala_scientific_name']?></td>
                                    <td><?=$row['ala_taxon_id']?></td>
                                    <td><?=$row['count_avh']?></td>
                                    <td><?=$row['count_vba']?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif;?>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="assertions">
                        <?php if ($assertions): ?>
                        <table class="table table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th>ALA Record number</th>
                                    <th>ALA Data source</th>
                                    <th>Assertion type</th>
                                    <th>Asserted value</th>
                                    <th>Assertion time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assertions as $assertion): ?>
                                <tr>
                                    <td><?=anchor('http://avh.ala.org.au/occurrences/' . $assertion['uuid'], $assertion['uuid'], array('target' => '_blank'))?></td>
                                    <td><?=$assertion['data_source']?></td>
                                    <td><?=$assertion['term']?></td>
                                    <td><?=$assertion['asserted_value']?><?=($assertion['ala_scientific_name']) ? 
                                        '<br/><span class="assertion-ala-name">(ALA: ' . $assertion['ala_scientific_name'] . 
                                        '|' . $assertion['ala_unprocessed_scientific_name'] . ')</span>' : ''; ?></td>
                                    <td><?=$assertion['timestamp_modified']?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane" id="updates">
                        <?php if ($updates): ?>
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>ALA record number</th>
                                    <th>Catalogue number</th>
                                    <th>Longitude</th>
                                    <th>Latitude</th>
                                    <th>Bioregion</th>
                                    <th>Occurrence status</th>
                                    <th>Establishment means</th>
                                    <th>New record</th>
                                    <th>Updated record</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($updates as $row): ?>
                                <tr>
                                    <td><?=anchor('http://avh.ala.org.au/occurrences/' . $row['uuid'], $row['uuid'], array('target' => '_blank'))?></td>
                                    <td><?=$row['catalog_number']?></td>
                                    <td><?=$row['decimal_longitude']?></td>
                                    <td><?=$row['decimal_latitude']?></td>
                                    <td><?=$row['sub_name_7']?></td>
                                    <td><?=$row['occurrence_status']?></td>
                                    <td><?=$row['establishment_means']?></td>
                                    <td><?=$row['is_new_record']?></td>
                                    <td><?=$row['is_updated_record']?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-right"><button class="accept-all btn btn-default">Accept all</button></div>
                        <?php endif; ?>
                    </div>
                </div> <!-- /.tab-content -->
            </div>
        </div> <!-- /.col -->
        
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('footer.php'); ?>

