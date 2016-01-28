<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Bioregions of Victoria</h2>
        </div>
        <div class="col-md-8">
            <?php if (isset($map)): ?>
            <img src="<?=$map?>" alt="Bioregions of Victoria" usemap="#vicflora_bioregion" />
            <?php require_once 'includes/bioregions_imagemap_600.php';?>
            <div id="info"></div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <?php 
                $codes = array();
                foreach ($legend as $item) {
                    $codes[] = $item['code'];
                }
            ?>
            <?php foreach ($bioregions as $reg): ?>
            <?php $key = array_search($reg['sub_code_7'], $codes); ?>
            <div class="legend-item">
                <span class="legend-symbol" style="background-color:<?=$legend[$key]['colour'];?>"></span>
                <span class="legend-label"><?=$reg['vic_bioregion']?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="clearfix">&nbsp;</div>
        <div class="col-md-12">
            <div id="staticcontent"><?=$staticcontent['PageContent']?></div>
        </div>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>