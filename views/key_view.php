<?php require_once 'header.php';?>
<div class="container">
    <div class="keybase-container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb"></ol>
                <h1 class="key-title"></h1>
                <div class="vicflora-tab clearfix">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab_player" aria-controls="player" role="tab" data-toggle="tab">Interactive key</a></li>
                        <li role="presentation"><a href="#tab_bracketed" aria-controls="bracketed" role="tab" data-toggle="tab">Bracketed key</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab_player">
                            <div id="keybase-player" class="keybase-panel"></div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab_bracketed">
                            <div id="keybase-bracketed" class="keybase-panel"></div>
                        </div>
                    </div> <!-- /.tab-content -->

                </div> <!-- /role:tabpanel -->
                <div class="keybase-key-source"></div>
                <div class="keybase-link text-right"><a href="" target="_blank">Open key in KeyBase <i class="fa fa-external-link"></i></a></div>
            </div>

        </div>
    </div>
</div>
<?php require_once 'footer.php';?>