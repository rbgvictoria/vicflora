<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">

        <div class="col-md-10">
            <h1>Download</h1>
        </div>
        <div class="col-md-2 text-right back-to-search"><a href="<?=site_url()?>flora/search?<?=$qstring?>" class="btn btn-default">Back to search result</a></div>

        <div class="col-lg-12">
            <h3>Query string</h3> 
            <p><?=$qstring?></p>
            <h3>Fields</h3>
        </div>

        <div class="download-fields">
            <div class="col-md-6">
                <p>
                    <?=form_checkbox(array('id'=>'taxonID', 'value'=>'taxon_id', 'checked'=>'checked', 'disabled'=>'disabled'));?>
                    <?=form_label('taxonID', 'taxonID');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'scientificName', 'value'=>'scientific_name', 'checked'=>'checked', 'disabled'=>'disabled'));?>
                    <?=form_label('scientificName', 'scientificName');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'scientificNameAuthorship', 'value'=>'scientific_name_authorship', 'checked'=>'checked'));?>
                    <?=form_label('scientificNameAuthorship', 'scientificNameAuthorship');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'namePublishedIn', 'value'=>'name_published_in'));?>
                    <?=form_label('namePublishedIn', 'namePublishedIn');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'namePublishedInYear', 'value'=>'name_published_in_year'));?>
                    <?=form_label('namePublishedInYear', 'namePublishedInYear');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'taxonRank', 'value'=>'taxon_rank', 'checked'=>'checked'));?>
                    <?=form_label('taxonRank', 'taxonRank');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'acceptedNameUsage', 'checked'=>'checked', 'value'=>'accepted_name_usage'));?>
                    <?=form_label('acceptedNameUsage', 'acceptedNameUsage');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'vernacularName', 'value'=>'vernacular_name'));?>
                    <?=form_label('vernacularName', 'vernacularName');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'taxonomicStatus', 'value'=>'taxonomic_status', 'checked'=>'checked'));?>
                    <?=form_label('taxonomicStatus', 'taxonomicStatus');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'occurrenceStatus', 'value'=>'occurrence_status', 'checked'=>'checked'));?>
                    <?=form_label('occurrenceStatus', 'occurrenceStatus');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'establishmentMeans', 'value'=>'establishment_means', 'checked'=>'checked'));?>
                    <?=form_label('establishmentMeans', 'establishmentMeans');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'threatStatus', 'value'=>'threat_status'));?>
                    <?=form_label('threatStatus', 'threatStatus');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'profile', 'value'=>'profile'));?>
                    <?=form_label('Profile', 'profile');?>
                </p>
            </div>

            <div class="col-md-6">
                <h4>Classification</h4>
                <p>
                    <?=form_checkbox(array('id'=>'kingdom', 'value'=>'kingdom'));?>
                    <?=form_label('kingdom', 'kingdom');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'phylum', 'value'=>'phylum'));?>
                    <?=form_label('phylum', 'phylum');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'class', 'value'=>'class'));?>
                    <?=form_label('class', 'class');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'subclass', 'value'=>'subclass'));?>
                    <?=form_label('subclass', 'subclass');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'superorder', 'value'=>'superorder'));?>
                    <?=form_label('superorder', 'superorder');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'order', 'value'=>'order'));?>
                    <?=form_label('order', 'order');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'family', 'value'=>'family', 'checked'=>'checked'));?>
                    <?=form_label('family', 'family');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'genus', 'value'=>'genus'));?>
                    <?=form_label('genus', 'genus');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'specificEpithet', 'value'=>'specific_epithet'));?>
                    <?=form_label('specificEpithet', 'specificEpithet');?>
                </p>
                <p>
                    <?=form_checkbox(array('id'=>'infraspecificEpithet', 'value'=>'infraspecific_epithet'));?>
                    <?=form_label('infraspecificEpithet', 'infraspecificEpithet');?>
                </p>
            </div>
        </div>
    
        <div class="col-lg-12">
            <div class="download-filetype">
                <h3>Delimiter</h3>
                <?=form_radio(array('name'=>'filetype', 'id'=>'filetype_txt', 'value'=>'txt', 'checked'=>'checked'));?>
                <?=form_label('Tab (TXT)', 'filetype_txt');?>
                <?=form_radio(array('name'=>'filetype', 'id'=>'filetype_csv', 'value'=>'csv'));?>
                <?=form_label('Comma (CSV)', 'filetype_csv');?>
            </div>
            <div class="download-filename">
            <h3>Filename</h3>
            <?=form_label('Filename', 'filename')?>
            <?=form_input(array('id'=>'filename', 'value'=>'vicflora_download_' . date('Ymd_Hi')));?>
            </div>
            <div class="download-submit text-center">
                <a href="#" class="btn btn-default">Download</a>
            </div>
        </div>
    </div>
</div> <!-- /.container -->

<?php require_once('footer.php'); ?>