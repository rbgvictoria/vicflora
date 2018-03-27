<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Flora of Victoria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="shortcut icon" href="https://www.rbg.vic.gov.au/common/img/favicon.ico">
    <link rel="stylesheet" type="text/css" href="https://www.rbg.vic.gov.au/common/fonts/451576/645A29A9775E15EA2.css" />
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>third_party/openlayers/3.18.1/ol.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/styles.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jqueryui.autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/vicflora.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>third_party/bower_components/photoswipe/dist/photoswipe.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>third_party/bower_components/photoswipe/dist/default-skin/default-skin.css" />

    <?php if (isset($css)): ?>
        <?php foreach ($css as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?=$link?>" />
        <?php endforeach; ?>
    <?php endif; ?>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <!--script type="text/javascript" src="<?=base_url()?>third_party/bower_components/jquery/dist/jquery.min.js"></script-->
    <script type="text/javascript" src="<?=base_url()?>third_party/bower_components/bootstrap-sass/assets/javascripts/bootstrap.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <!--script type="text/javascript" src="<?=base_url()?>third_party/jquery-ui/js/jquery-ui.min.js"></script-->
    <script type="text/javascript" src="<?=base_url()?>third_party/openlayers/3.18.1/ol.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/jquery.vicflora.ol3.js')?>"></script>
    <script type="text/javascript" src="<?=base_url()?>third_party/bower_components/jspath/jspath.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>third_party/keybase/js/jquery.keybase.key.js"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/vicflora.js')?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/vicflora-keybase.js')?>"></script>
    <script type="text/javascript" src="<?=base_url()?>third_party/bower_components/photoswipe/dist/photoswipe.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>third_party/bower_components/photoswipe/dist/photoswipe-ui-default.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/vicflora.photoswipe.js')?>"></script>
    
    <?php if(isset($this->session->userdata['name'])):?>
    <script src="<?=base_url()?>third_party/contextMenu/jquery.contextMenu.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jquery.contextMenu.css" />
    <script src="<?=base_url()?><?=autoVersion('js/jquery.vicflora.edit-taxon.js')?>"></script>
    <script src="<?=base_url()?>third_party/ckeditor_4.4.0/ckeditor.js"></script>
    <script src="<?=base_url()?>third_party/ckeditor_4.4.0/adapters/jquery.js"></script>
    <?php endif; ?>

    <?php if (isset($js)): ?>
        <?php foreach ($js as $file): ?>
    <script type="text/javascript" src="<?=$file?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body class="vicflora">
    <div id="banner">

    <div class="container">

      
        <div class="row">
            <div class="col-lg-12 clearfix">
              <ul class="social-media">
                  <li><a href="https://twitter.com/RBG_Victoria" target="_blank"><span class="icon icon-twitter-solid"></span></a></li>
                  <li><a href="https://www.facebook.com/BotanicGardensVictoria" target="_blank"><span class="icon icon-facebook-solid"></span></a></li>
                  <li><a href="https://instagram.com/royalbotanicgardensvic/" target="_blank"><span class="icon icon-instagram-solid"></span></a></li>
              </ul>
            </div> <!-- /.col -->

            <nav class="navbar navbar-default">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-brand">
                        <a class="brand-rbg" href="http://www.rbg.vic.gov.au"><img src="<?=base_url()?>images/rbg-logo-with-text.png" alt=""/></a>
                        <a class="brand-vicflora" href="<?=base_url()?>">VicFlora</a>
                    </div>
                </div>
              
                <div id="navbar" class="navbar-collapse collapse">
                  <ul class="nav navbar-nav">
                      <li class="home-link"><a href="<?=site_url()?>"><span class="glyphicon glyphicon-home"></span></a></li>
                    <li><a href="<?=site_url()?>flora/search">Search</a></li>
                    <li><a href="<?=site_url()?>flora/classification">Browse classification</a></li>
                    <li><a href="<?=site_url()?>flora/key/1903" class="colorbox_mainkey">Keys</a></li>
                    <li><a href="<?=site_url()?>flora/checklist">Checklists</a></li>
                    <li><a href="<?=site_url()?>flora/glossary">Glossary</a></li>
                    <li><a href="<?=site_url()?>flora/bioregions">Bioregions & Vegetation</a></li>
                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Help <span class="caret"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="<?=site_url()?>flora/help">Help</a></li>
                        <li><a href="<?=site_url()?>flora/about">About</a></li>
                        <li><a href="<?=site_url()?>flora/acknowledgements">Acknowledgements</a></li>
                      </ul>
                    </li>
                  </ul>
                  <?=form_open('flora/search', array('method' => 'get', 'class' => 'navbar-form navbar-right')); ?>
                    <div class="form-group">
                        <div class="input-group">
                      <?=form_input(array('name' => 'q', 'class' => 'form-control input-sm', 'placeholder' => 'Enter taxon name...')); ?>
                            <div class="submit input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                        </div>
                    </div>
                    
                  <?=form_close(); ?>
                </div><!--/.navbar-collapse -->
            </nav>

            <div class="col-lg-12">
                <!-- Place for alert -->
                <div id="header">
                    <div class="login">
                        <?php if ($this->session->userdata('name')): ?>
                            <?=$this->session->userdata['name']?> | <?=anchor('admin/logout', 'Log out'); ?>
                        <?php else: ?>
                            <?=anchor('admin/login', 'Log in', array('id' => 'hidden-login-link')); ?>
                        <?php endif; ?>
                    </div>
                    <div id="logo">
                        <a href='http://www.rbg.vic.gov.au'>
                            <img class="img-responsive" src="<?=base_url()?>images/rbg-logo-with-text" alt="" />
                        </a>
                    </div>
                    <div id="site-name">
                        <a href="<?=base_url()?>">VicFlora</a>
                    </div>
                    <div id="subtitle">Flora of Victoria</div>
                </div>
            </div>
        </div><!--/.row -->
    </div><!--/.container -->
</div> <!-- /#banner -->