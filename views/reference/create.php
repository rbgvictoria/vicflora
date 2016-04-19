<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="text-right ref_nav">
                <a class="btn btn-default" href="<?=site_url()?>reference/">Find references</a>
            </div>
            <?=form_open(FALSE, 
                    array('class' => 'form-horizontal')); ?>
            <?php if (isset($_SERVER['HTTP_REFERER'])): ?>
                <?=form_hidden('http_referer', $_SERVER['HTTP_REFERER']); ?>
            <?php endif; ?>
                <div class="form-group">
                    <?=form_label('Author(s)', 'ref_author', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_author',
                            'id' => 'ref_author',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Publication year', 'ref_publication_year', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_publication_year',
                            'id' => 'ref_publication_year',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Title', 'ref_title', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_title',
                            'id' => 'ref_title',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Journal', 'ref_journal_or_book', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_journal_or_book',
                            'id' => 'ref_journal_or_book',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Series', 'ref_series', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_series',
                            'id' => 'ref_series',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Volume', 'ref_volume', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_volume',
                            'id' => 'ref_volume',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Issue', 'ref_part', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_part',
                            'id' => 'ref_part',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Page(s)', 'ref_page', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_page',
                            'id' => 'ref_page',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Edition', 'ref_edition', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_edition',
                            'id' => 'ref_edition',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Publisher', 'ref_publisher', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_publisher',
                            'id' => 'ref_publisher',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Place of publication', 'ref_place_of_publication', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_place_of_publication',
                            'id' => 'ref_place_of_publication',
                            'class' => 'form-control input-sm',
                            'value' => FALSE
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('In', 'ref_in_publication', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <?=form_input(array(
                                'id' => 'reference-search',
                                'class' => 'form-control input-sm',
                                'value' => FALSE
                            )); ?>
                            <?=form_input(array(
                                'type' => 'hidden',
                                'id' => 'reference-id',
                                'name' => 'ref_in_publication_id', 
                                'value' => FALSE
                            )); ?>
                            <span class="input-group-addon"><i class="fa fa-search fa-lg"></i></span>
                        </div>
                    </div>
                </div>
            <div class="text-right">
                <?=anchor(site_url() . 'reference/create/', 'New reference', array('class' => 'btn btn-default btn-sm', 'target' => '_blank')); ?>
            </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <div id="reference-list" class="form-control-static"></div>
                    </div>
                </div>
                <div class="text-right">
                    <button name="save" value="save" type="submit" class="btn btn-default">Save</button>
                </div>
                
            <?=form_close()?>

        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>