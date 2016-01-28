<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit: <?=$staticcontent['PageTitle']?></h1>

            <?=form_open()?>
            <?php 
                echo form_open();
                    $hidden = array(
                        'id' => $staticcontent['StaticID'],
                        'title' => $staticcontent['PageTitle'],
                    );
                    echo form_hidden($hidden);

                    $data = array(
                      'name'        => 'pagecontent',
                      'id'          => 'ckeditor1',
                      'class'       => 'ckeditor',
                      'value'       => $staticcontent['PageContent'],
                    );
                    echo form_textarea($data);
            ?>
            <div id="submit_div"><?=form_submit('submit', 'Submit')?></div>

            <div>&nbsp;</div>
            <!-- <div><a class="colorbox_load_image" href="<?=site_url()?>key/loadimage">Upload image file</a></div> -->


            <?=form_close()?>
        </div> <!-- /.col- -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
