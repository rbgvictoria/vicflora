<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="text-right ref_nav">
                <a class="btn btn-default" href="<?=site_url()?>reference/">Find references</a>
                <a class="btn btn-default" href="<?=site_url()?>reference/show/<?=$referenceData->ReferenceID?>">Show reference</a>
                <a class="btn btn-default" href="<?=site_url()?>reference/create/">New reference</a>
            </div>

            <?php if ($fullReference): ?>
            <div class="alert alert-vicflora"><b><?=$fullReference->label?>.</b> <?=$fullReference->description?></div>
            <?php endif; ?>
            
            <?php if ($referenceData): ?>
            <?=form_open('reference/edit/' . $referenceData->ReferenceID, 
                    array('class' => 'form-horizontal'),
                    array('ref_version' => $referenceData->Version)); ?>
                <div class="form-group">
                    <?=form_label('Author(s)', 'ref_author', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_author',
                            'id' => 'ref_author',
                            'class' => 'form-control input-sm',
                            'value' => $referenceData->Author
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Author role', 'ref_author_role', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_dropdown('ref_author_role', array(
                            '' => '',
                            'author' => 'Author',
                            'editor' => 'Editor'
                        ), $referenceData->AuthorRole); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Publication year', 'ref_publication_year', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_publication_year',
                            'id' => 'ref_publication_year',
                            'class' => 'form-control input-sm',
                            'value' => $referenceData->PublicationYear
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
                            'value' => $referenceData->Title
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
                            'value' => $referenceData->JournalOrBook
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
                            'value' => $referenceData->Series
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
                            'value' => $referenceData->Volume
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
                            'value' => $referenceData->Part
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
                            'value' => $referenceData->Page
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
                            'value' => $referenceData->Edition
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
                            'value' => $referenceData->Publisher
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
                            'value' => $referenceData->PlaceOfPublication
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('In', 'ref_in_publication', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'id' => 'reference-search',
                            'class' => 'form-control input-sm',
                            'value' => ($referenceData->InPublicationID) ? $referenceData->in->label : FALSE
                        )); ?>
                        <?=form_input(array(
                            'type' => 'hidden',
                            'id' => 'reference-id',
                            'name' => 'ref_in_publication_id', 
                            'value' => $referenceData->InPublicationID
                        )); ?>
                    </div>
                </div>
                <?php if ($referenceData->InPublicationID): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-10">
                        <div id="reference-list" class="form-control-static">
                            <b><?=$referenceData->in->label?>.</b>
                            <?=anchor(site_url() . 'reference/show/' . $referenceData->InPublicationID, '<i class="fa fa-search"></i>');?>
                            <?php if ($this->session->userdata('id')): ?>
                            <?=anchor(site_url() . 'reference/edit/' . $referenceData->InPublicationID, '<i class="fa fa-edit"></i>');?>
                            <?php endif; ?>
                            <br/> <?=$referenceData->in->description?>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <?php if ($inPublications):?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Includes</label>
                    <div class="col-sm-10">
                        <div class="form-control-static">
                            <?php foreach ($inPublications as $item): ?>
                            <p>
                                <b><?=$item->label?>.</b> <?=$item->description?>
                                <?=anchor(site_url() . 'reference/show/' . $item->value, '<i class="fa fa-search"></i>')?>
                                <?php if ($this->session->userdata('id')): ?>
                                <?=anchor(site_url() . 'reference/edit/' . $item->value, '<i class="fa fa-edit"></i>')?>
                                <?php endif; ?>
                            </p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <?=form_label('URL', 'ref_url', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_url',
                            'id' => 'ref_url',
                            'class' => 'form-control input-sm',
                            'value' => $referenceData->URL
                        )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?=form_label('Date accessed', 'ref_date_accessed', array('class' => 'col-sm-2 control-label')); ?>
                    <div class="col-sm-10">
                        <?=form_input(array(
                            'name' => 'ref_date_accessed',
                            'id' => 'ref_date_accessed',
                            'class' => 'form-control input-sm',
                            'value' => $referenceData->DateAccessed
                        )); ?>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button name="save" value="save" type="submit" class="btn btn-default">Save</button>
                </div>
                
            <?=form_close()?>
            <?php endif; ?>

        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>