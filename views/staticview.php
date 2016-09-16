<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12 clearfix">
            <?php if (isset($pages)): ?>
            <div class="pull-right"><a href="<?=site_url()?>admin/add_static_page" class="btn btn-primary btn-sm">Add page</a></div>
            <h1>Static pages</h1>
            <ul>
                <?php foreach ($pages as $page): ?>
                <li><?=anchor('admin/st/' . $page['Uri'], $page['PageTitle'])?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            
            <?php if (($this->session->userdata('id') && strpos($_SERVER['PATH_INFO'], 'admin/st') !== FALSE) || substr($staticcontent['Uri'], 0, strlen('bioregions/')) === 'bioregions/'): ?>
            <div class="pull-right">
            <?php if (substr($staticcontent['Uri'], 0, strlen('bioregions/')) === 'bioregions/'): ?>
                <a class="btn btn-primary btn-sm" href="<?=site_url()?>flora/bioregions">All regions</a>
            <?php endif; ?>
            <?php if ($this->session->userdata('id') && strpos($_SERVER['PATH_INFO'], 'admin/st') !== FALSE): ?>
                <a class="btn btn-primary btn-sm" href="<?=site_url()?>admin/st">Index</a>
                <a class="btn btn-primary btn-sm" href="<?=site_url()?>admin/st/<?=$staticcontent['Uri']?>/_edit">Edit</a>
            <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div> <!-- /.col -->
        <div class="col-lg-12">
            <h1><?=$staticcontent['PageTitle']?></h1>
            <div id="staticcontent"><?=$staticcontent['PageContent']?></div>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
