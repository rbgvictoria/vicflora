<?php require_once('header.php'); ?>

<?php require_once getcwd() . '/third_party/Encoding/Encoding.php';?>


<?php if($namedata): ?>
<div class="container">
<?php if (isset($this->session->userdata['last_search']) || isset($breadcrumbs)): ?>
    <div class="row">
        <div class="col-lg-12">
        <?php if (isset($this->session->userdata['last_search'])):?>
            <div class="back-link pull-right">
                <span><?=anchor($this->session->userdata['last_search'], 'Back to last search result', array('class' => 'btn btn-primary btn-sm'));?></span>
            </div>
        <?php endif;?>
        <?php if (isset($breadcrumbs) || isset($siblings)  || isset($children)): ?>
            <ol class="breadcrumb pull-left">
                <?php foreach ($breadcrumbs as $crumb): ?>
                <li><?=anchor(site_url() . 'flora/taxon/' . $crumb->GUID, $crumb->FullName);?></li>
                <?php endforeach; ?>
                <?php if($siblings): ?>
                <li><?=form_dropdown('siblings', $siblings, array($namedata['GUID']), 'id="nav-siblings" class="form-control input-sm"');?></li>
                <?php endif; ?>
                <?php if($children): ?>
                <li><?=form_dropdown('children', $children, FALSE, 'id="nav-children" class="form-control input-sm"');?></li>
                <?php endif; ?>
            </ol>
        <?php endif; ?>
        </div> <!-- /.col- -->    
    </div>
<?php endif;?>
    
<?php if (isset($this->session->userdata['id'])): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="taxon-edit-menu text-right">
                <a href="<?=site_url()?>admin/edittaxon/<?=$namedata['GUID']?>">Edit</a>
                <?php if ($namedata['TaxonomicStatus'] == 'accepted' || $namedata['UnmatchedProfile']): ?>
                <a href="<?=site_url()?>admin/editprofile/<?=$namedata['GUID']?>">Edit profile</a>
                <?php endif;?>
                <?php if (isset($svg_map)): ?>
                <a href="<?=site_url()?>admin/editdistribution/<?=$namedata['GUID']?>">Edit distribution</a>
                <?php endif; ?>
                <?php if ($namedata['RankID'] <= 220 && $namedata['RankID'] >=100): ?>
                <a href="<?=site_url()?>admin/addchild/<?=$namedata['GUID']?>">Add child</a>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- /.row -->
<?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="scientific-name pull-left">
            <?php if ($namedata['TaxonomicStatus'] == 'accepted'): ?>
                <span class="currentname <?=($namedata['RankID']>=180) ? ' italic' : '';?>">
            <?php else: ?>
                <span class="oldname <?=($namedata['RankID']>=180) ? ' italic' : '';?>">
            <?php endif; ?>
                    <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                            class="namebit">', '</span> var. <span class="namebit">', 
                            '</span> f. <span class="namebit">'), 
                $namedata['FullName']);?></span><?php if($namedata['Author']): ?> <span 
                class="author"><?=$namedata['Author']?></span><?php endif; ?>
                </span>
            </h2>

            <?php if ($namedata['TaxonomicStatus'] == 'accepted' && $commonname): ?>
            <h2 class="preferred-common-name pull-left"><?=$commonname[0]['CommonName']?></h2>
            <?php endif; ?>
            </div>
    </div> <!-- /.row -->
    
    <div class="row">
        <div class="col-lg-12">
    
            <?php
                if ($namedata['InAuthor'] && substr($namedata['Author'], strlen($namedata['Author']) - 
                        strlen($namedata['InAuthor'])) != $namedata['InAuthor']) {
                    $inauthor = '<i>in</i> ' . $namedata['InAuthor'] . ', ';
                }
                else
                    $inauthor = '';

                $nomnot = '';
                if ($namedata['NomenclaturalNote']) {
                    if (substr($namedata['NomenclaturalNote'], 0, 3) == 'as ')
                        $nomnot = ', as <i>' . substr($namedata['NomenclaturalNote'], 3) . '</i>';
                    else
                        $nomnot = ', ' . $namedata['NomenclaturalNote'];
                }

                $protologue = ($namedata['JournalOrBook']) ? '<i>' . $namedata['JournalOrBook'] . '</i>' : '';
                $protologue .= ($namedata['Series']) ? ', ' . $namedata['Series'] . ',' : '';
                $protologue .= ($namedata['Edition']) ? ', ' . $namedata['Edition'] . ',' : '';
                $protologue .= ($namedata['Volume']) ? ' <b>' . $namedata['Volume'] . '</b>' : '';
                $protologue .= ($namedata['Part']) ? ' (' . $namedata['Part'] . ')' : '';
                $protologue .= ($namedata['Volume'] && $namedata['Page']) ? ':' : '';
                $protologue .= ($namedata['Page']) ? ' ' . $namedata['Page'] : '';
                $protologue .= ($namedata['PublicationYear']) ? ' (' . $namedata['PublicationYear'] . ')' : '';

                $sensu = ($protologue && $namedata['Sensu']) ? ', ' : '';
                $sensu .= ($namedata['Sensu']) ? 'sensu ' . $namedata['Sensu'] : '';
            ?>
            <?php if ($protologue || $nomnot || $sensu): ?>
            <p class="protologue">
            <?=$inauthor?><?=$protologue?><?=$nomnot?><?=$sensu?>

            <?php if ($apni && count($apni) == 1):?>
                <span class="apni">
                    <?=anchor($apni[0]['ApniScientificNameID'] . '/api/apni-format', '<small>APNI <i class="fa fa-external-link"></i></small>', array('target' => '_blank'));?>
                </span>
            <?php endif; ?>
            </p>
            <?php endif; ?>

            <div class="section status">
                <?php if($namedata['TaxonomicStatus'] || $namedata['OccurrenceStatus'] != 'excluded'): ?>
                <p><span class="vicflora-label">Taxonomic status:</span><span class="vicflora-stat-value"><?=($namedata['TaxonomicStatus']) ? ucfirst($namedata['TaxonomicStatus']) : 'Not current'; ?></span></p>
                <?php endif;?>
                <?php if($namedata['TaxonomicStatus'] == 'accepted' || $namedata['OccurrenceStatus'] == 'excluded'): ?> 
                <p><span class="vicflora-label">Occurrence status:</span><span class="vicflora-stat-value"><?=ucfirst($namedata['OccurrenceStatus']); ?></span></p>
                <?php endif; ?>
                <?php if($namedata['TaxonomicStatus'] == 'accepted'): ?> 
                <p><span class="vicflora-label">Establishment means:</span><span class="vicflora-stat-value"><?=($namedata['EstablishmentMeans'] == 
                        'adventive') ? 'Sparingly established' : ucfirst($namedata['EstablishmentMeans'])?></span></p>
                <?php if ($attributes && array_intersect(array('EPBC (Jan. 2014)', 'VROT', 'FFG'), 
                        array_keys($attributes))): ?>
                <?php 
                    $vrot = array(
                        'x' => 'extinct (x)',
                        'e' => 'endangered (e)',
                        'v' => 'vulnerable (v)',
                        'r' => 'rare (r)',
                        'k' => 'data deficient (k)',
                    );

                    $epbc = array(
                        'EX' => 'extinct (EX)',
                        'CR' => 'critically endangered (CR)',
                        'EN' => 'endangered (EN)',
                        'VU' => 'vulnerable (VU)',
                    );

                    $threatStatus = array();
                    if (isset($attributes['EPBC (Jan. 2014)'])) {
                        $threatStatus[] = 'EPBC: ' . $epbc[$attributes['EPBC (Jan. 2014)']];
                    }
                    if (isset($attributes['VROT'])) {
                        $threatStatus[] = 'Victoria: ' . $vrot[$attributes['VROT']];
                    }
                    if (isset($attributes['FFG'])) {
                        $threatStatus[] = 'listed in Flora and Fauna Guarantee Act 1988';
                    }
                    $threatStatus = implode('; ', $threatStatus);

                ?>
                <?php if ($threatStatus): ?>
                <p><span class="vicflora-label">Threat status:</span><span class="stat-value"><?=$threatStatus?></span></p>
                <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ($namedata['TaxonomicStatus'] != 'accepted' && $acceptedname): ?>
                <p><span class="vicflora-label">Accepted name:</span><span class="stat-value"><span 
                    class="currentname <?=($acceptedname['RankID']>=180) ? ' italic' : '';?>"><a href="<?=site_url()?>flora/taxon/<?=$acceptedname['GUID']?>"><span 
                        class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                            class="namebit">', '</span> var. <span class="namebit">', 
                            '</span> f. <span class="namebit">'), $acceptedname['FullName']);?></span>
                        <?php if($acceptedname['Author']): ?>
                        <span class="author"><?=$acceptedname['Author']?></span>
                        <?php endif; ?>
                    </a></span></span>
                </p>
                <?php endif;?>
                <?php if ($namedata['Remarks']): ?>
                <p><span class="label">Remarks:</span><span class="stat-value"><?=$namedata['Remarks']?></span></p>
                <?php endif; ?>
            </div>
        </div>
    </div> <!-- /.row -->

    <?php if ($namedata['TaxonomicStatus'] == 'accepted'): ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div id="detail-page-tab" class="vicflora-tab" role="tabpanel">
            
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                <?php if ($profile): ?>
                    <li role="presentation"><a href="#tab-profile" aria-controls="overview" role="tab" data-toggle="tab">Overview</a></li>
                <?php endif; ?>
                <?php if ($images): ?>
                    <li role="presentation"><a href="#tab-images" aria-controls="images" role="tab" data-toggle="tab">Images</a></li>
                <?php endif; ?>
                <?php if ($distribution): ?>
                    <li role="presentation"><a href="#tab-distribution" aria-controls="distribution" role="tab" data-toggle="tab">Distribution</a></li>
                <?php endif; ?>
                <?php if ($classification || $subordinates): ?>
                    <li role="presentation"><a href="#tab-classification" aria-controls="classification" role="tab" data-toggle="tab">Classification</a></li>
                <?php endif; ?>
                <?php if ($synonyms || $misapplications): ?>
                    <li role="presentation"><a href="#tab-synonyms" aria-controls="synonyms" role="tab" data-toggle="tab">Synonyms</a></li>
                <?php endif; ?>
                <?php if($links): ?>
                    <li role="presentation"><a href="#tab-links" aria-controls="links" role="tab" data-toggle="tab">Other floras</a></li>
                <?php endif; ?>
                </ul>

                <div class="tab-content">
                    <?php if ($profile): ?>
                    <div id="tab-profile" role="tabpanel" class="tab-pane section profile">
                        
                        <div class="row">
                            <?php if ($heroImage || (isset($profileMap) && $profileMap)): ?>
                            <div class="col-md-8">
                            <?php else: ?>
                            <div class="col-md-12">
                            <?php endif; ?>
                            
                        <?=$profileStr;?>
                        <?php if ($profile[0]['Author']): ?>
                        <?php
                            if ($profile[0]['AsFullName']) {
                                $as = 'as <span class="oldname">';
                                $as .= '<i>' . str_replace(array(' subsp. ', ' var. ', ' f. '), array('</i> subsp. <i>', '</i> var. <i>', 
                                    '</i> f. <i>'), $profile[0]['AsFullName']) . '</i>';
                                if ($profile[0]['AsAuthor'])
                                    $as .= ' <span class="author">' . $profile[0]['AsAuthor'] . '</span>';
                                //$as .= ' (' . $profile[0]['TaxonomicStatus'] . ')';
                                $as .= '</span>';
                            }
                            else ($as = '');

                        ?>
                        <div class="profile-source">
                            <b>Source: </b><?=$profile[0]['Author'] . ' (' . $profile[0]['PublicationYear'] . '). ' .
                                preg_replace('/~([^~]*)~/', '<i>$1</i>', $profile[0]['Title']) . '. In: ' . $profile[0]['InAuthor'] . ', <i>' . 
                                preg_replace('/(Vol\. [2-4]), /', "</i><b>$1</b>, <i>", $profile[0]['InTitle']) . '</i>. ' . 
                                $profile[0]['Publisher'] . ', ' . $profile[0]['PlaceOfPublication']; ?><?=($as) ? " ($as)." : '.'; ?>
                        </div>
                        <?php if ($profile[0]['IsUpdated']): ?>
                        <div class="updated-by">
                            <b>Updated by:</b> <?=$profile[0]['UpdatedBy']?>, <?=$profile[0]['DateUpdated'];?>
                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="created-by">
                            <b>Created by:</b> <?=$profile[0]['CreatedBy']?>, <?=$profile[0]['DateCreated']?>
                        </div>
                        <?php if ($profile[0]['IsUpdated']): ?>
                        <div class="updated-by">
                            <b>Updated by:</b> <?=$profile[0]['UpdatedBy']?>, <?=$profile[0]['DateUpdated'];?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                                
                        <?php if ($taxonReferences): ?>
                        <div class="taxon-references">
                            <div>&nbsp;</div>
                            <div><b>References</b></div>
                            <?php foreach ($taxonReferences as $ref): ?>
                            <p><b><?=$ref->label?>. </b> <?=$ref->description?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                                

                        <?php if ($key && $namedata['GUID'] !== 'd3590e10-e8ac-4848-9e33-9fbbaf171f7a'): ?>
                        <div>&nbsp;</div>
                        <div><?=anchor(site_url() . 'flora/key/' . $key['KeysID'], $key['Title'], array('class' => 'btn btn-primary colorbox_key'))?></div>
                        <?php endif; ?>
                        </div>
                                
                        <?php if ($heroImage || (isset($profileMap) && $profileMap)): ?>
                        <div class="col-md-4 profile-rigth-pane">
                            <div class="row">
                            <?php if ($heroImage):?>
                            <?php
                                $width = $heroImage->PixelXDimension;
                                $height = $heroImage->PixelYDimension;
                                if ($heroImage->Subtype == 'Illustration') {
                                    $width = $width / 2;
                                    $height = $height / 2;
                                }    

                                if ($width > $height) {
                                    if ($width > 512) {
                                        $height = $height * (512 / $width);
                                        $width = 512;
                                    }
                                    $size = $width;
                                }
                                else {
                                    if ($height > 512) {
                                        $width = $width * (512 / $height);
                                        $height = 512;
                                    }
                                    $size = $height;
                                }
                            ?>
                            <div class="hero-image col-sm-6 col-md-12">
                                <div class="image-padding">
                                    <div>
                                        <div class="image-box">
                                            <div class="content">
                                                <img class="img-responsive" src="<?=$this->config->item('preview_baseurl')?><?= urlencode($heroImage->CumulusCatalogue)?>/<?=$heroImage->CumulusRecordID?>?b=<?=$size?>"
                                                     alt="Hero image" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($profileMap) && $profileMap): ?>
                            <div class="profile-map col-sm-6 col-md-12">
                                <div class="image-padding">
                                    <div>
                                        <div class="image-box">
                                            <div class="content">
                                                <img class="img-responsive" src="<?=$profileMap?>" alt="Distribution map" width="512" height="310" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            </div> <!-- /.row -->
                        </div> <!-- /.profile-right-pane -->
                        <?php endif; ?>
                                
                        </div> <!-- /.row -->
                    </div>
                    <?php endif; ?>

                    <?php if ($images): ?>
                    <div id="tab-images" role="tabpanel" class="tab-pane section images">
                        <div class="row thumbnail-row">
                        <?php foreach ($images as $image): ?>
                            <figure class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
                                <a href="<?=$this->config->item('preview_baseurl')?><?= urlencode($image->catalog)?>/<?=$image->id?>?maxsize=<?=$image->maxsize?>" 
                                   class="thumbnail thumb" data-size="<?=$image->width?>x<?=$image->height?>" 
                                   data-alt="<?=$image->alt?>" data-caption="<?=$image->caption?>">
                                    <span>
                                        <img alt="" src="<?=$this->config->item('thumbnail_baseurl')?><?= urlencode($image->catalog)?>/<?=$image->id?>" />
                                    </span>
                                </a>
                            </figure>
                        <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($classification || $subordinates): ?>
                    <div id="tab-classification" role="tabpanel" class="tab-pane classification">
                        <?php if ($classification): ?>
                        <div class="section taxon-classification">
                            <?php foreach ($classification as $ancestor): ?>
                            <div class="currentname <?=($ancestor['RankID']>=180) ? ' italic' : ''?>">
                                <span class="taxon-rank"><?=$ancestor['Rank']?></span><?=str_repeat('<span class="indent"></span>', $ancestor['Depth']); ?>
                                <?php $author = ($ancestor['Author']) ? ' <span class="author">' . $ancestor['Author'] . '</span>' : ''; ?>
                                <?=anchor(site_url() . 'flora/taxon/' . $ancestor['GUID'], '<span class="namebit">' . 
                                        str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                                    class="namebit">', '</span> var. <span class="namebit">', 
                                    '</span> f. <span class="namebit">'), $ancestor['FullName']) . '</span>' . $author); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="cl-separator-higher"><span class="glyphicon glyphicon-triangle-top"></span>Higher taxa</div>
                        <?php endif; ?>
                        
                        <div class="section currentname">
                            <div class="currentname<?=($namedata['RankID']>=180) ? ' italic' : '';?>">
                                <?php $author = ($namedata['Author']) ? ' <span class="author">' . $namedata['Author'] . '</span>' : ''; ?>
                                <span class="taxon-rank"><?=$namedata['Rank']?></span><?=str_repeat('<span class="indent"></span>', $namedata['Depth']); ?>
                                <?=anchor(site_url() . 'flora/taxon/' . $namedata['GUID'], '<span class="namebit ' . strtolower($namedata['Rank']) . '">' . 
                                        $namedata['FullName'] . '</span>' . $author); ?>
                            </div>
                        </div>
            
                        <?php if ($subordinates): ?>
                        <div class="cl-separator-subordinate"><span class="glyphicon glyphicon-triangle-bottom"></span>Subordinate taxa</div>
                        <div class="section subordinate-taxa">
                            <?php foreach ($subordinates as $child): ?>
                            <div class="currentname <?=($child['RankID']>=180) ? ' italic' : ''?>">
                                <?php $author = ($child['Author']) ? ' <span class="author">' . $child['Author'] . '</span>' : ''; ?>
                                <span class="taxon-rank"><?=$child['Rank']?></span><?=str_repeat('<span class="indent"></span>', $child['Depth']); ?>
                                <?=anchor(site_url() . 'flora/taxon/' . $child['GUID'], '<span class="namebit ' . strtolower($child['Rank']) . '">' . 
                                        str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                                    class="namebit">', '</span> var. <span class="namebit">', 
                                    '</span> f. <span class="namebit">'), $child['FullName']) . '</span>' . $author); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($synonyms || $misapplications): ?>
                    <div id="tab-synonyms" role="tabpanel" class="tab-pane">
                        <?php if ($synonyms): ?>
                        <div class="synonyms section">
                            <h4>Synonyms</h4>
                            <ul>
                                <?php foreach($synonyms as $syn): ?>
                                <li><span class="oldname italic"><a href="<?=site_url()?>flora/taxon/<?=$syn['GUID']?>"><span 
                                        class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), 
                                        array('</span> subsp. <span 
                                    class="namebit">', '</span> var. <span class="namebit">', 
                                    '</span> f. <span class="namebit">'), $syn['FullName']);?></span>
                                    <?php if($syn['Author']): ?>
                                        <span class="author"><?=$syn['Author']?></span>
                                    <?php endif; ?>
                                    </span></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <?php if ($misapplications): ?>
                        <div class="misapplications section">
                            <h4>Misapplications</h4>
                            <ul>
                                <?php foreach($misapplications as $syn): ?>
                                <li><span class="oldname italic"><a href="<?=site_url()?>flora/taxon/<?=$syn['GUID']?>">
                                        <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                                    class="namebit">', '</span> var. <span class="namebit">', 
                                    '</span> f. <span class="namebit">'), $syn['FullName']);?></span>
                                    <?php if($syn['Author']): ?><span class="author"><?=$syn['Author']?></span><?php endif; ?><?php 
                                        if($syn['Sensu']): ?> (<i>in</i> <?=$syn['Sensu']?>)<?php endif; ?>
                                    </span></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($distribution): ?>
                    <div id="tab-distribution" role="tabpanel" class="tab-pane section distribution">
                        <!--h3>Distribution</h3-->
                        <?php if (isset($svg_map)): ?>
                        <div class="svg-map">
                            <h3>Victoria</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <figure class="distribution-map-frame">
                                        <div id="svg-avhdistribution"></div>
                                        <div class="legend">
                                            <button class="btn btn-primary btn-sm"><i class="fa fa-bars fa-lg"></i></button>
                                            <img src="<?=base_url()?>images/vicflora-map-legend.png" alt="" />
                                            <i class="fa fa-remove"></i>
                                        </div>
                                        
                                    <figcaption><b>Source: </b>AVH (2014). <i>Australia&apos;s Virtual Herbarium</i>, Council of Heads of 
                                        Australasian Herbaria, &lt;<a href="http://avh.chah.org.au">http://avh.chah.org.au</a>&gt;.
                                        <a href="http://avh.ala.org.au/occurrences/search?taxa=<?=str_replace(' ', '+', 
                                                $namedata['FullName']);?>" target="_blank">Find <?=$namedata['FullName']?> in AVH <i class="fa fa-external-link"></i></a>;
                                                <i>Victorian Biodiversity Atlas</i>, © The State of Victoria, Department of Environment and Primary Industries (published Dec. 2014)
                                                <a href="http://biocache.ala.org.au/occurrences/search?taxa=<?=str_replace(' ', '+', 
                                                $namedata['FullName']);?>&fq=data_resource_uid:dr1097" target="_blank">Find <?=$namedata['FullName']?> in Victorian Biodiversity Atlas <i class="fa fa-external-link"></i></a>
                                    </figcaption>
                                    </figure>
                                </div>
                                <div class="col-md-6">
                                    <?php 
                                        $codes = array();
                                        foreach ($bioregion_legend as $item) {
                                            $codes[] = $item['code'];
                                        }
                                    ?>
                                    
                                    <table class="bioregions table table-bordered table-condensed">
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Bioregion</th>
                                            <th>Occurrence status</th>
                                            <th>Establishment means</th>
                                        </tr>
                                        <?php foreach ($distribution as $row): ?>
                                        <?php 
                                            $key = array_search($row['sub_code_7'], $codes);
                                            $colour = $bioregion_legend[$key]['colour'];
                                        ?>
                                        <tr>
                                            <td><span class="legend-symbol" style="background-color:<?=$colour;?>"></span></td>
                                            <td><?=$row['sub_name_7']?></td>
                                            <td><?=$row['occurrence_status']?></td>
                                            <td><?=$row['establishment_means']?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                    <div class="bioregions-link text-right"><?=anchor(site_url() . 'flora/bioregions', 'Bioregions of Victoria')?></div>
                                </div>
                            </div> <!-- /.row -->
                        </div>
                        
                        <?php if ($stateDistribution): ?>
                        <h3>State distribution</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <figure>
                                    <img src="<?=$stateDistributionMap?>" alt="Distribution map" width="242" height="242"/>
                                </figure>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($stateDistribution as $row): ?>
                                        <tr>
                                            <td><?=$row['state_province']?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php /*
                        <h3>Bounding polygon</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Convex hull</h4>
                                <img src="<?=$boundingPolygonMap100?>" alt="Distribution map" width="400" height="242"/>
                            </div>
                            <div class="col-md-6">
                                <h4>Concave hull, 99 per cent</h4>
                                <img src="<?=$boundingPolygonMap99?>" alt="Distribution map" width="400" height="242"/>
                            </div>
                            <div class="col-md-6">
                                <h4>Concave hull, 80 per cent</h4>
                                <img src="<?=$boundingPolygonMap80?>" alt="Distribution map" width="400" height="242"/>
                            </div>
                            <div class="col-md-6">
                                <h4>Concave hull, 99 per cent, curved</h4>
                                <img src="<?=$boundingPolygonMap99C?>" alt="Distribution map" width="400" height="242"/>
                            </div>
                        </div>
                         */?>
                        
                        </div> <!-- /#tab-distribution -->
                        <?php endif; ?>

                        <?php if($links): ?>
                        <div id="tab-links" role="tabpanel" class="tab-pane section flora-links">
                            <ul>
                                <?php foreach($links as $link): ?>
                                <?php 
                                    switch ($link['Flora']) {
                                        case 'FloraBase':
                                            $src = 'florabase.png';
                                            break;

                                        case 'PlantNet':
                                            $src = 'floraNSW.jpg';
                                            break;

                                        case 'eFlora of South Australia':
                                            $src = 'floraSA.gif';
                                            break;

                                        default:
                                            break;
                                    }
                                ?>
                                <li class="flora-link"><?=anchor($link['Url'], '<img src="' . base_url() . 'images/' . $src . '" alt="' . $link['Flora'] . '" />', array('target' => '_blank')); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div> <!-- /.tab-content -->
            </div> <!-- /#detail-page-tab -->
        </div>
    </div> <!-- /.row -->
    <?php endif; // accepted?>
    
</div> <!-- /.container -->
<?php endif; ?>
    
<?php require_once('footer.php'); ?>
