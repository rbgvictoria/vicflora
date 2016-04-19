<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Flora of Victoria</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="shortcut icon" href="http://www.rbg.vic.gov.au/common/img/favicon.ico">
    <!--link rel="stylesheet" href="http://openlayers.org/en/v3.3.0/css/ol.css" type="text/css"-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jqueryui.autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>js/colorbox/example1/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>js/OpenLayers/theme/default/style.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>js/OpenLayers/theme/default/google.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/main.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/vicflora.css?v=<?=filemtime('css/vicflora.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/keybase.player.css?v=<?=filemtime('css/keybase.player.css')?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/vicflora.keybase.player.css?v=<?=filemtime('css/vicflora.keybase.player.css')?>" />
    <?php if ($this->session->userdata('name')): ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/vicflora.admin.css?v=<?=filemtime('css/vicflora.admin.css')?>" />
    <?php endif; ?>

    <?php if (isset($css)): ?>
        <?php foreach ($css as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?=$link?>" />
        <?php endforeach; ?>
    <?php endif; ?>


    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?=base_url()?>js/modernizr.custom.13288.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.colorbox-min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jspath.min.js"></script>
    <script src="http://maps.google.com/maps/api/js"></script>
    <script src="<?=base_url()?>js/OpenLayers/OpenLayers.js" type="text/javascript"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="<?=base_url()?>js/jquery.vicflora.openlayers.js?v=<?=filemtime('js/jquery.vicflora.openlayers.js')?>"></script>
    <script src="<?=base_url()?>js/jquery.keybase.key.js?v=<?=filemtime('js/jquery.keybase.key.js')?>"></script>
    <script src="<?=base_url()?>js/vicflora.js?v=<?=filemtime('js/vicflora.js')?>"></script>
    <script src="<?=base_url()?>js/colorbox/jquery.vicflora.colorbox.js?v=<?=filemtime('js/colorbox/jquery.vicflora.colorbox.js')?>"></script>
    <!--script src="<?=base_url()?>js/jquery.vicflora.elastic.js"></script-->
    
    <?php if(isset($this->session->userdata['name'])):?>
    <script src="<?=base_url()?>third_party/contextMenu/jquery.contextMenu.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jquery.contextMenu.css" />
    <script src="<?=base_url()?>js/jquery.vicflora.edit-taxon.js?v=<?=filemtime('js/jquery.vicflora.edit-taxon.js')?>"></script>
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
    <nav class="navbar navbar-default navbar-inverse" role="navigation" id="rbgv-branding">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="http://www.rbg.vic.gov.au">
                <img src="http://data.rbg.vic.gov.au/dev/rbgcensus/css/images/rbg-vic-logo-transparent-48x35.png" 
                     alt="" 
                     class="rbgv-logo-navbar"
                />
            </a>
          <a class="navbar-brand" href="http://www.rbg.vic.gov.au">Royal Botanic Gardens Victoria</a>
        </div>
        <ul class="social-media">
            <li><a href="https://twitter.com/RBG_Victoria" target="_blank"><span class="icon icon-twitter-solid"></span></a></li>
            <li><a href="https://www.facebook.com/BotanicGardensVictoria" target="_blank"><span class="icon icon-facebook-solid"></span></a></li>
            <li><a href="https://instagram.com/royalbotanicgardensvic/" target="_blank"><span class="icon icon-instagram-solid"></span></a></li>
        </ul>

      </div><!-- /.container-fluid -->
    </nav>

    <nav class="navbar navbar-default" id="rbg-census-navigation">
      <div class="container">
          <div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
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
              </ul>
            </li>
          </ul>
          <?=form_open('flora/search', array('method' => 'get', 'class' => 'navbar-form navbar-right')); ?>
            <div class="form-group">
              <?=form_input(array('name' => 'q', 'class' => 'form-control', 'placeholder' => 'Enter taxon name...')); ?>
            </div>
            <input type="submit" class="btn btn-default" value="Find"/>
          <?=form_close(); ?>
        </div><!--/.navbar-collapse -->
          </div><!--/.row -->
      </div><!--/.container -->
    </nav>

    <div class="page-header">
        <div class="container">
            <div class="inner">
                <div class="login">
                    <?php if ($this->session->userdata('name')): ?>
                        <?=$this->session->userdata['name']?> | <?=anchor('admin/logout', 'Log out'); ?>
                    <?php else: ?>
                        <?=anchor('admin/login', 'Log in', array('id' => 'hidden-login-link')); ?>
                    <?php endif; ?>
                </div>
                <div id="name-and-subtitle">
                    <div id="site-name">
                        <a href="<?=base_url()?>">VicFlora</a>
                    </div>
                    <div id="subtitle">Flora of Victoria</div>
                </div>
            </div>
        </div>
    </div>