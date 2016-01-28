<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Add static page</h1>

            <?=form_open()?>
            <p>
                <?=form_label('Page title', 'title', array('style' => 'width: 120px;')); ?>
                <?=form_input(array('name' => 'title', 'id' => 'title')); ?>
            </p>
            <p>
                <?=form_label('URI', 'uri', array('style' => 'width: 120px;')); ?>
                <?=form_input(array('name' => 'uri', 'id' => 'uri')); ?>
            </p>
            <p>
                <?=form_submit('submit', 'Submit'); ?>
            </p>

            <?=form_close()?>
        </div> <!-- /.col- -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
