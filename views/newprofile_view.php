<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">

    <div class="taxon-name">
        <h2>New profile:
            <span class="currentname <?=($taxondata['RankID']>=180) ? ' italic' : '';?>">
                <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                    class="namebit">', '</span> var. <span class="namebit">', 
                    '</span> f. <span class="namebit">'), $taxondata['FullName']);?></span>
            <?php if($taxondata['Author']): ?>
                <span class="author"><?=$taxondata['Author']?></span>
            <?php endif; ?>
            </span>
        </h2>
    </div>
    
    <div class="profile-editor">
        <?=form_open('admin/newprofile/' . $taxondata['GUID']); ?>
            <?=form_hidden('taxon_id', $taxondata['TaxonID']); ?>
            <?=form_textarea(array(
                'name' => 'profile',
                'id' => 'profile',
                'value'=> ''
            ));?>
            <div class="editor-menu">
                <?=form_submit('save', 'Save'); ?>
                <?=form_submit('cancel', 'Cancel'); ?>
            </div>
        <?=form_close()?>
    </div>
        
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
