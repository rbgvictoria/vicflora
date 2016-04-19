<?php require_once 'views/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>VicFlora reference</h2>
            
            <div class="text-right ref_nav">
                <a class="btn btn-default" href="<?=site_url()?>reference/create/">New reference</a>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="input-group">
                                <?=form_input(array('name' => 'reference', 'id' => 'reference-search', 'class' => 'form-control input-sm')); ?>
                                <?=form_input(array('type' => 'hidden', 'name' => 'reference-id', 'id' => 'reference-id')); ?>
                                <div class="input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div id="reference-first-letter">
                        <?php 
                            $alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
                                'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
                                'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
                                'y', 'z');
                            $letters = array();
                            foreach($alpha as $letter) {
                                $letters[] = anchor(site_url() . 'reference/reference_lookup_autocomplete?term=' . $letter, strtoupper($letter), array('class' => 'btn btn-default btn-sm'));
                            }
                            echo implode('', $letters);

                        ?>
                    </div>
                </div>
            </div>
            <div id="reference-list">
                
            </div>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>