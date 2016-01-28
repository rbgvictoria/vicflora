<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (isset($pages)): ?>
            <h1>Static pages</h1>
            <ul>
                <?php foreach ($pages as $page): ?>
                <li><?=anchor('admin/st/' . $page['Uri'], $page['PageTitle'])?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if (substr($staticcontent['Uri'], 0, 10) == 'bioregions'): ?>
            <div class="row">
                <div class="col-sm-9 col-md-10">
                    <h1><?=$staticcontent['PageTitle']?></h1>
                </div>
                <div class="col-sm-3 col-md-2 text-right">
                    <span class="btn btn-default"><?=anchor(site_url() . 'flora/bioregions', 'All bioregions')?></span>
                </div>
                
            </div>
            <?php else: ?>
            <h1><?=$staticcontent['PageTitle']?></h1>
            <?php endif;?>
            <div id="staticcontent"><?=$staticcontent['PageContent']?></div>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
