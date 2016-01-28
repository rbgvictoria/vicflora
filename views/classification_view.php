<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h1>Classification</h1>
            <?php if ($ancestors): ?>
            <div class="classification ancestors">
                <?php foreach ($ancestors as $ancestor): ?>
                <div class="currentname<?=($ancestor['RankID']>=180) ? ' italic' : '';?>">
                    <span class="taxon-rank"><?=$ancestor['Rank']?></span><?=str_repeat('<span class="indent"></span>', $ancestor['Depth']); ?>
                    <?php $author = ($ancestor['Author']) ? ' <span class="author">' . $ancestor['Author'] . '</span>' : ''; ?>
                    <?=anchor(site_url() . 'flora/classification/' . $ancestor['GUID'], '<span class="namebit ' . strtolower($ancestor['Rank']) . '">' . 
                            $ancestor['FullName'] . '</span>' . $author); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($taxon): ?>
            <div class="cl-separator-higher"><?=anchor(site_url() . 'flora/classification/' . $ancestors[count($ancestors)-1]['GUID'], '<span class="glyphicon glyphicon-triangle-top"></span>Higher taxa');?></div>
            <div class="classification currenttaxon">
                <div class="currentname<?=($taxon['RankID']>=180) ? ' italic' : '';?>">
                    <?php $author = ($taxon['Author']) ? ' <span class="author">' . $taxon['Author'] . '</span>' : ''; ?>
                    <span class="taxon-rank"><?=$taxon['Rank']?></span><?=str_repeat('<span class="indent"></span>', $taxon['Depth']); ?>
                    <?=anchor(site_url() . 'flora/taxon/' . $taxon['GUID'], '<span class="namebit ' . strtolower($taxon['Rank']) . '">' . 
                            $taxon['FullName'] . '</span>' . $author); ?>
                </div>
            </div>
            <div class="cl-separator-subordinate"><span class="glyphicon glyphicon-triangle-bottom"></span>Subordinate taxa</div>
            <?php endif; ?>

            <?php if ($children): ?>
            <div class="classification children">
                <?php foreach ($children as $child): ?>
                <div class="currentname<?=($child['RankID']>=180) ? ' italic' : '';?>">
                    <?php $author = ($child['Author']) ? ' <span class="author">' . $child['Author'] . '</span>' : ''; ?>
                    <span class="taxon-rank"><?=$child['Rank']?></span><?=str_repeat('<span class="indent"></span>', $child['Depth']); ?>
                    <?=anchor(site_url() . 'flora/classification/' . $child['GUID'], '<span class="namebit ' . strtolower($child['Rank']) . '">' . 
                            $child['FullName'] . '</span>' . $author); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
