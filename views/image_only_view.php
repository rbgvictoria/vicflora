<?php if($image): ?>
<div class="vicflora-image-preview">
    <?php
        $width = $image->PixelXDimension;
        $height = $image->PixelYDimension;
        if ($image->Subtype == 'Illustration') {
            $width = $width / 2;
            $height = $height / 2;
        }    
            
        if ($width > $height) {
            if ($width > 1024) {
                $height = $height * (1024 / $width);
                $width = 1024;
            }
            $size = $width;
        }
        else {
            if ($height > 1024) {
                $width = $width * (1024 / $height);
                $height = 1024;
            }
            $size = $height;
        }
        
        $scientificName = FALSE;
        if (isset($image->ScientificName)) {
            $scientificName = '<i>' . $image->ScientificName . '</i>';
            if ($image->AsName) $scientificName .= ' (as <i>' . $image->AsName . '</i>)';
            $search = array(' subsp. ', ' var. ', ' f. ');
            $replace = array(
                '</i> subsp. <i>',
                '</i> var. <i>',
                '</i> f. <i>'
            );
            $scientificName = str_replace($search, $replace, $scientificName);
        }
        
        if (substr($image->License, 0, 5) === 'CC BY') {
            $bits = explode(' ', $image->License);
            $url = 'https://creativecommons.org/licenses/';
            $url .= strtolower($bits[1]);
            $url .= (isset($bits[2])) ? '/' . $bits[2] : '/4.0';
            if (isset($bits[3])) $url .= '/' .strtolower ($bits[3]);
            $license = anchor($url, $image->License);
        }
        elseif ($image->License == 'All rights reserved') {
            $license = 'All rights reserved';
        }
        elseif ($image->SubjectCategory == 'Flora of the Otway Plain and Ranges plate') {
            $license = 'Not to be reproduced without prior permission from CSIRO Publishing.';
        }
        else {
            $license = anchor('https://creativecommons.org/licenses/by-nc-sa/4.0', 'CC BY-NC-SA 4.0');
        }
        
        /*switch ($image->License) {
            case 'CC BY 3.0 AU':
                $license = anchor('https://creativecommons.org/licenses/by/3.0/au/', 'CC BY 3.0 AU');
                break;

            default:
                $license = anchor('https://creativecommons.org/licenses/by/4.0/', 'CC BY 4.0');
                break;
        }*/
    ?>
    <img src="http://images.rbg.vic.gov.au/sites/P/Library/<?=$image->CumulusRecordID?>?b=<?=$size?>" width="<?=$width?>" height="<?=$height?>"
         alt="<?=($scientificName) ? $scientificName : $image->Caption?>" />
    <div class="vicflora-preview-caption" style="width: <?=$width?>px;">
        <p><?=($scientificName) ? $scientificName : ''; ?><?=($image->Caption && $scientificName) ? '. ' : ''; ?><?=($image->Caption) ? $image->Caption : '';?>.</p>
        <p><?=($image->Subtype == 'Illustration') ? 'Illustrator' : 'Photographer';?>: 
            <?=$image->Creator?>, &copy; <?=($image->RightsHolder) ? $image->RightsHolder : 'Royal Botanic Gardens Victoria';?>,
            <?=date('Y')?>, <?=$license?>.
        </p>
        <?php if (isset($image->SubjectCategory) && $image->SubjectCategory == 'Flora of the Otway Plain and Ranges plate'): ?>
        <p>Reproduced with permission from <i>Flora of the Otway Plain and Ranges 1: Orchids, Irises, Lilies, Grass -trees, 
            Mat-rushes and Other Petaloid Monocotyledons / Flora of the Otway Plain and Ranges 2: Daisies, Heaths, Peas, 
            Saltbushes, Sundews, Wattles and Other Shrubby and Herbaceous Dicotyledons</i> by Enid Mayfield.
            Published by CSIRO Publishing</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
