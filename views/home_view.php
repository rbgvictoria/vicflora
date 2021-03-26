<?php require_once('header.php'); ?>


<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="welcome">
                <span>VicFlora</span> is a comprehensive and current guide to the wild plants of Victoria. With plant profiles, 
                identification tools and richly illustrated, the Royal Botanic Gardens Victoria provides this resource 
                free-of-charge for land managers, scientists, students and indeed anyone with an interest in Victorian plants.
            </div>
            <div class="home-page-button-nav">
                <a href="<?=site_url()?>flora/search" class="btn btn-primary btn-lg btn-block">Search</a>
                <a href="<?=site_url()?>flora/classification" class="btn btn-primary btn-lg btn-block">Browse classification</a>
                <a href="<?=site_url()?>flora/key/1903" class="colorbox_mainkey btn btn-primary btn-lg btn-block">Keys</a>
            </div>
            <div class="highlights">
                <h2>Highlights</h2>
                <div class="highlight">
                    <div class="highlight-image">
                        <img class="img-responsive" src="<?=base_url()?>images/home/lucid-logo-icon-150.png" alt=""/>
                    </div>
                    <div class="highlight-text">
                        <h4><span class="h3" style="color:red">New</span> multi-access keys</h4>
                        New multi-access keys to <a href="<?=site_url() ?>static/keys/fabaceae">Fabaceae</a> (excl. Acacia), 
                        <a href="<?=site_url() ?>static/keys/cyperaceae">Cyperaceae</a> and 
                        <a href="<?=site_url() ?>static/keys/juncaceae">Juncaceae</a> in Victoria.
                    </div>
                </div>
                <div class="highlight">
                    <div class="highlight-image">
                        <a href="<?=site_url() ?>static/keys/eucalypts">
                            <img class="img-responsive" src="<?=base_url()?>images/home/eucalcom.jpg" alt=""/>
                        </a>
                    </div>
                    <div class="highlight-text">
                        <h4>
                            <a href="<?=site_url() ?>static/keys/eucalypts">
                                Multi-access key to the Eucalypts
                            </a>
                        </h4>
                        <p>Check out our new multi-access key to the 159 species and infraspecific taxa of <i>Eucalyptus</i>, <i>Angophora</i> and <i>Corymbia</i> in Victoria.</p>
                    </div>
                </div>
                <div class="highlight">
                    <div class="highlight-image">
                        <a href="<?=site_url() ?>static/keys/asteraceae">
                            <img class="img-responsive" src="<?=base_url()?>images/home/microwal.jpg" alt=""/>
                        </a>
                    </div>
                    <div class="highlight-text">
                        <h4>
                            <a href="<?=site_url() ?>static/keys/asteraceae">
                                Multi-access key to the Asteraceae
                            </a>
                        </h4>
                        <p>Check out our new multi-access key to the 618 species and infraspecific taxa of Asteraceae in Victoria.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="carousel-home-page" class="carousel" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <?php foreach ($carousel as $index => $slide): ?>
                    <?php $caption = 'Illustration: ' . $slide->Creator . '. &copy;' . date('Y') . ' ' . $slide->RightsHolder . '. ' . $slide->License . '.'; ?>
                    <div class="item<?=(!$index) ? ' active' : ''; ?>">
                        <img src="<?=$this->config->item('preview_baseurl')?>public/<?=$slide->CumulusRecordID?>?maxsize=600" alt="<?=$caption?>" />
                        <div class="carousel-caption"><?=$caption?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <a class="left carousel-control" href="#carousel-home-page" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-home-page" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
                
                <ol class="carousel-indicators">
                    <?php foreach ($carousel as $index => $slide): ?>
                    <li data-target="#carousel-home-page" data-slide-to="<?=$index?>"<?=(!$index) ? ' class="active"' : '';?>></li>
                    <?php endforeach; ?>
                </ol>
                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
