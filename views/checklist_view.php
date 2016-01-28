<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Checklist<?php if (isset($park_name)): ?>:
            <?=$park_name?>
            <?php endif; ?>
            </h1>
            <p>This page allows the user to generate a list of vascular plants for any gazetted reserve in Victoria. 
            Click on a point on the map below and a list of reserves will appear. Select a reserve and a checklist 
            of plants will be generated below the map.</p>
        </div>

        <div class="col-md-7">
            <div id="capad_map" class=""></div>
        </div>
        <div class="col-md-5"> 
            <div id="nodelist">&nbsp;</div>
        </div>

        <div class="col-md-12">
            <div class="well well-sm" id="vicflora-checklist-source">
                <h4>Source</h4>
                <ul>
                    <li><b>Protected areas:</b> <i>Collaborative Australian Protected Areas Database</i> (CAPAD) 2012, Commonwealth of Australia 2012</li>
                    <li><b>Occurrence data:</b>
                        <ul>
                            <li>AVH (2014). <i>Australia’s Virtual Herbarium</i>, Council of Heads of Australasian Herbaria, 
                                &lt;<a href="http://avh.chah.org.au">http://avh.chah.org.au</a>&gt;</li>
                            <li><i>Victorian Biodiversity Atlas</i>, © The State of Victoria, Department of Environment and Primary Industries (published Dec. 2014).</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-12">
            <?php if (isset($checklist) && $checklist): ?>
            <div class ="checklist">
                <?php foreach ($checklist as $item): ?>
                <?php if ($item['taxonRank'] == 'family'):?>
                <h3><?=$item['scientificName']?></h3>
                <?php elseif ($item['taxonRank'] == 'genus'): ?>
                <h4><?=$item['scientificName']?></h4>
                <?php else: ?>
                <?php
                    $name = '<span class="namebit">';
                    $name .= str_replace(array(' subsp. ', ' var. ', ' f. ', ' nothosubsp. ', ' nothovar. '), array(
                        '</span> subsp. <span class="namebit">', 
                        '</span> var. <span class="namebit">', '</span> f. <span class="namebit">',
                        '</span> nothosubsp. <span class="namebit">', 
                        '</span> nothovar. <span class="namebit">'
                        ), $item['scientificName']);
                    $name .= '</span>'
                ?>
                <div class="currentname">
                <?php if ($item['taxonRank'] != 'species'):?>
                &emsp;
                <?php endif; ?>
                <?=anchor(site_url() . 'flora/taxon/' . $item['taxonID'], $name); ?>
                <?php if ($item['scientificNameAuthor']): ?><?=$item['scientificNameAuthor']?><?php endif;?>
                </div>
                <?php endif; ?>

                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php require_once('footer.php'); ?>