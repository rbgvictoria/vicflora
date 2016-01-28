<?php if ($image): ?>
    <div class="image-innerframe">
        <img alt="" src="<?=base_url()?>images/fov/2/previews/<?=$image['Filename']?>" />
    </div>
    <div class="image-caption">
        <?php
            $fignum = $image['FigureNumber'] . $image['FigureSub'];
            $taxonname = $image['TaxonName'];
            $caption = $image['Caption'];
            if (strpos($image['FigureSub'], '-') === FALSE)
                $caption = substr($caption, 3);
            $caption = preg_replace('/^[a-z]{1}\./', "<b>$0</b>", $caption);
            $caption = preg_replace('/; ([a-z]{1}\.)/', "; <b>$1</b>", $caption);

            $source = $image['Editor'] . ' (' . $image['Year'] . '). <i>'. $image['Title'] .
                    ', ' . $image['Volume'] . ', ' . $image['Subtitle'] . '</i>. ' . 
                    $image['Publisher'] . ', ' . $image['PlaceOfPublication'];
            if ($image['AsTaxonName']) 
                $source .= ' (as <i>' . $image['AsTaxonName'] . '</i>)';
            $source .= '.';
        ?>
        <b>Figure <?=$fignum?>.</b> <i><?=$taxonname?></i>; <?=$caption; ?> 
        <b>Source:</b> <?=$source?>
    </div>


<?php endif;?>
