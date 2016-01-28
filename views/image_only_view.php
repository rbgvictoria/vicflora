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
        else {
            $license = anchor('https://creativecommons.org/licenses/by/4.0', 'CC BY 4.0');
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
    </div>
</div>
<?php endif; ?>
