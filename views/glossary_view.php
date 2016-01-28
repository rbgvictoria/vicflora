<?php require_once('header.php'); ?>

<div class="container">
<?php if (isset($this->session->userdata['id'])): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="taxon-edit-menu text-right">
                <a href="<?=site_url()?>admin/editglossaryterm/" class="edit-glossary-term">Edit</a>
                <a href="<?=site_url()?>admin/newglossaryterm/">New</a>
            </div>
        </div>
    </div> <!-- /.row -->
<?php endif; ?>
    <div class="row">
        
        <div class="col-lg-12">
            <h1>Glossary</h1>
        </div>


        <div class="col-sm-4 col-md-3" id="glossary-terms">
            <div id="glossary-first-letter">
                <?php 
                    $letters = array();
                    foreach($alph_dropdown as $letter) {
                        $letters[] = anchor(site_url() . 'flora/glossary#' . $letter, '<span class="letter">' . strtoupper($letter) . '</span>');
                    }
                    echo implode('', $letters);
                    
                ?>
            </div>
            
            <div id="term-list">
            </div>

        </div>

        <div class="col-sm-8 col-md-9">
            <div id="definition"></div>
        </div>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>