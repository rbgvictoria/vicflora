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
                    <div class="highlight-image"><img class="img-responsive" src="<?=base_url()?>images/home/microwal.jpg" alt=""/></div>
                    <div class="highlight-text">Only this year was the scientific name for the important Aboriginal food plant Murnong,
                    <a href="<?=base_url()?>flora/taxon/83b57182-7112-49e7-923e-c667f89f89bf"><i>Microseris walteri</i></a>, clarified.</div>
                </div>
                <div class="highlight">
                    <div class="highlight-image"><img class="img-responsive" src="<?=base_url()?>images/home/olericur.jpg" alt=""/></div>
                    <div class="highlight-text"><a href="<?=base_url()?>flora/taxon/fca8bec1-a85e-436d-8285-6609a2ed700d"><i>Olearia curticoma</i></a>, 
                        the Billygoat Daisy Bush occurs only above Billygoat Bend on the Mitchell River in East Gippsland. It was named in 2014.</div>
                </div>
                <div class="highlight">
                    <div class="highlight-image"><img class="img-responsive" src="<?=base_url()?>images/home/olerirot.jpg" alt=""/></div>
                    <div class="highlight-text"><a href="<?=base_url()?>flora/taxon/d91c65d3-5a69-44b3-9656-cd67d264cfc3"><i>Olearia asterotricha</i> subsp. 
                        <i>rotundifolia</i></a>, described in 2014 is known only from the summit area of Mt Langi Ghiran.</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="carousel-home-page" class="carousel" data-ride="carousel">
                <div class="carousel-inner" role="listbox">
                    <?php foreach ($carousel as $index => $slide): ?>
                    <?php $caption = 'Illustration: ' . $slide->Creator . '. &copy;' . date('Y') . ' ' . $slide->RightsHolder . '. ' . $slide->License . '.'; ?>
                    <div class="item<?=(!$index) ? ' active' : ''; ?>">
                        <img src="<?=$this->config->item('preview_baseurl')?>Library/<?=$slide->CumulusRecordID?>?b=600" alt="<?=$caption?>" />
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
