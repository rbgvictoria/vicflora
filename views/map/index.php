<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>VicFlora distribution map</h2>
            <a class="btn btn-default" href="map/update">Update maps</a>
            
            <?php if (isset($qstring) && $qstring): ?>
            <div><?=anchor($qstring, $qstring); ?></div>
            <?php endif;?>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>