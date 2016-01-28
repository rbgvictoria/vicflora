<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        
        <div id="loginform" class="col-lg-12">
                <h2>Log in to VicFlora</h2>
                <?=form_open("admin/authenticate");?>
            <?=form_hidden('referer', $referer); ?>
                <p>
                  <?=form_label('Username', 'username'); ?>
                  <?=form_input(array('name'=>'username', 'id'=>'username', 'size'=>'25')); ?>
                </p>
                <p>
                  <?=form_label('Password', 'passwd'); ?>
                  <?=form_password(array('name'=>'passwd', 'id'=>'passwd', 'size'=>'25')); ?>
                </p>
                <p><?=form_submit('submit', 'Log in', array('class' => 'btn btn-default')); ?></p>
                <?=form_close();?>
        </div>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>