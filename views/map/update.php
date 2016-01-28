<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Update distribution maps</h2>
            
            <p>Maps where last updated on <?=$lastUpdated?>. <a class="btn btn-default" href="<?=site_url()?>map/update/<?=$lastUpdated?>">Update now</a></p>
            
            <?php if (isset($result)): ?>
            <pre><?php print_r($result)?></pre>
            <?php endif; ?>
            
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>