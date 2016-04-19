<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="text-right ref_nav">
                <a class="btn btn-default" href="<?=site_url()?>reference/">Find references</a>
                <a class="btn btn-default" href="<?=site_url()?>reference/edit/<?=$referenceData->ReferenceID?>">Edit reference</a>
                <a class="btn btn-default" href="<?=site_url()?>reference/create/">New reference</a>
            </div>
            <?php if ($fullReference): ?>
            <div class="alert alert-vicflora"><b><?=$fullReference->label?>.</b> <?=$fullReference->description?></div>
            <?php endif; ?>
            
            <?php if ($referenceData): ?>
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Author</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Author?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Publication year</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->PublicationYear?></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=preg_replace('/~([^~]*)~/', '<i>$1</i>', $referenceData->Title); ?></div>
                    </div>
                </div>
                <?php if ($referenceData->JournalOrBook): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Journal</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->JournalOrBook?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Series): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Series</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Series?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Volume): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Volume</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Volume?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Part): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Issue</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Part?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Page): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Page(s)</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Page?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Edition): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Edition</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Edition?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->Publisher): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Publisher</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->Publisher?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->PlaceOfPublication): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Place of publication</label>
                    <div class="col-sm-10">
                        <div class="form-control-static"><?=$referenceData->PlaceOfPublication?></div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($referenceData->InPublicationID): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">In</label>
                    <div class="col-sm-10">
                        <div class="form-control-static">
                            <b><?=$referenceData->in->label?>.</b> <?=$referenceData->in->description?>
                            <?=anchor(site_url() . 'reference/show/' . $referenceData->InPublicationID, '<i class="fa fa-search"></i>');?>
                            <?php if ($this->session->userdata('id')): ?>
                            <?=anchor(site_url() . 'reference/edit/' . $referenceData->InPublicationID, '<i class="fa fa-edit"></i>');?>
                            <?php endif; ?>
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
                
            </div>
            <?php endif; ?>

        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>