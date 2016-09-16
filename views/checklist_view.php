<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Checklist<?php if (isset($park_name)): ?>:
            <?=$park_name?>
            <?php endif; ?>
            </h1>
            <p>
                Create your own checklist for any Victorian park or reserve in the <a href="https://www.environment.gov.au/land/nrs/science/capad"
                target="_blank">Collaborative Australian Protected Area Database (CAPAD) <i class="fa fa-external-link"></i></a>, based on occurrence data from <a 
                    href="http:avh.chah.org.au" target="_blank">Australia's Virtual Herbarium (AVH) <i class="fa fa-external-link"></i></a> and the <a 
                href="http://www.depi.vic.gov.au/environment-and-wildlife/biodiversity/victorian-biodiversity-atlas" 
                target="_blank">Victorian Biodiversity Atlas (VBA) <i class="fa fa-external-link"></i></a> 
                and using the taxonomy of VicFlora.
            </p>
            <p>
                Click on a point on the map below and a list of reserves will appear. Select a reserve and a checklist 
                of vascular plant taxa will be generated below the map.
            </p>
            <p>&nbsp;</p>
        </div>

        <div class="col-md-7">
            <div id="capad_map" class=""></div>
        </div>
        <div class="col-md-5"> 
            <div id="nodelist">&nbsp;</div>
        </div>

        <div class="col-md-12 clearfix">
            <div class="row">
                <div class="col-md-4">
                    <div id="facets">
                        <div class="facets">
                            <h3>Filters</h3>
                            <div class="content form-horizontal"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="checklist-result"></div>
                </div>
            </div>
        </div>
        
        <div id="vicflora-checklist-source" class="col-md-12">
            <h4>Source</h4>
            <ul>
                <li><b>Protected areas:</b> <i>Collaborative Australian Protected Areas Database</i> (CAPAD) 2014, Commonwealth of Australia 2014</li>
                <li><b>Occurrence data:</b>
                    <ul>
                        <li>AVH (<?=date('Y')?>). <i>Australia’s Virtual Herbarium</i>, Council of Heads of Australasian Herbaria, 
                            &lt;<a href="http://avh.chah.org.au">http://avh.chah.org.au</a>&gt;</li>
                        <li><i>Victorian Biodiversity Atlas</i>, © The State of Victoria, Department of Environment and Primary Industries (published Dec. 2014).</li>
                    </ul>
                </li>
            </ul>
        </div>

    </div>

</div>
<?php require_once('footer.php'); ?>