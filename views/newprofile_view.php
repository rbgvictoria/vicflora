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

            <?=form_open('admin/newprofile/' . $taxondata['GUID']); ?>
            <div class="profile-editor">
                    <?=form_hidden('taxon_id', $taxondata['TaxonID']); ?>
                    <?=form_textarea(array(
                        'name' => 'profile',
                        'id' => 'profile',
                        'value'=> ''
                    ));?>
            </div>

            <h3>Source</h3>
            <div class="profile-source">
                <div id="source-desc"></div>
                <div class="row">
                    <div class="col-lg-4">
                         <div class="form-horizontal">
                            <div class="input-group">
                                <?=form_input(array('name' => 'source', 'id' => 'source-search', 'class' => 'form-control input-sm')); ?>
                                <?=form_input(array('type' => 'hidden', 'name' => 'source-id', 'id' => 'source-id')); ?>
                                <div class="input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <?=anchor(site_url() . 'reference/create/', 'New reference', array('class' => 'btn btn-default btn-sm')); ?>
                    </div>
                </div>
            </div>
            
            <h3>References</h3>
            <div id="taxon-references">
            </div>
            <div class="row">
                <div class="col-lg-4">
                     <div class="form-horizontal">
                        <div class="input-group">
                            <?=form_input(array('name' => 'reference', 'id' => 'reference-search', 'class' => 'form-control input-sm')); ?>
                            <?=form_input(array('type' => 'hidden', 'name' => 'reference-id', 'id' => 'reference-id')); ?>
                            <div class="input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <?=anchor(site_url() . 'reference/create/', 'New reference', array('class' => 'btn btn-default btn-sm')); ?>
                </div>
            </div>
            <div class="editor-menu text-right">
                <?=form_submit('save', 'Save'); ?>
                <?=form_submit('cancel', 'Cancel'); ?>
            </div>
            <?=form_close()?>
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>
