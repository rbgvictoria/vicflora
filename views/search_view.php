<?php require_once('header.php'); ?>

	<?=form_open('flora/search', array('method' => 'get')); ?>
<div id="full-width-panel">
    <div class="search">
        <?=form_input(array('name' => 'term', 'id' => 'term', 'value' => 
                (!empty($request['term'])) ? $request['term'] : FALSE)); ?>
        <?=form_hidden('startIndex', 0); ?>
        <button class="submit">Find</button>
    </div>
    <!--pre>
<?php print_r($facets); ?>
    </pre-->
</div>

<div id="left-side-panel">
    <div class="query">
        <div class="query-term"><b>Query term:</b> <?=$this->input->get('term');?></div>
        <?php
            if ($this->input->get()) {
                $fq = array();
                foreach ($facets as $facet) {
                    if (in_array($facet['name'], array_keys($this->input->get()))) {
                        $values = explode(',', $this->input->get($facet['name']));

                        $labels = array();
                        foreach ($facet['items'] as $item) {
                            if (in_array($item['name'], $values))
                                $labels[] = $item['label'];
                        }
                        $fq[] = '<li data-vicflora-facet-name="' . $facet['name']. '"><b>' . $facet['label'] . ':</b> ' . implode (', ', $labels) . ' <img class="clear" src="' . 
                                base_url() .'css/images/icon_delete.png" alt="clear" width="10" height="12" /></li>';
                    }
                }
                if ($fq) {
                    echo '<div><b>Filters</b></div>';
                    echo '<ul>' . implode('', $fq) . '</ul>';
                    echo '<div class="clear-all"><a href="#">Clear all</a></div>';
                }
            }
        ?>
    </div>
    <div class="facets">
        <?php require_once('facets.php') ?>
    </div>
</div>

<div id="right-main-panel">
    <div class="loading"><img src="<?=base_url()?>css/images/ajax-loader.gif" alt="Loading..." 
                              height="16" width="16" /></div>
<?php if(!empty($result)): ?>
    <div class="num-matches"><?=$numMatches?> matches</div>
    <pre><?php print_r($request);?></pre>
<?php

    //if ($numMatches >= $request['pageSize']) {
        $first = $request;
        $first['startIndex'] = 0;
        $qStringFirst = array();
        foreach ($first as $key => $value)
            $qStringFirst[] = $key . '=' . urlencode($value);
        $qStringFirst = implode('&', $qStringFirst);

        $prev = $request;
        $prev['startIndex'] = ($request['startIndex'] > $request['pageSize']) ? 
                $request['startIndex'] - $request['pageSize'] : 0;
        $qStringPrev = array();
        foreach ($prev as $key => $value)
            $qStringPrev[] = $key . '=' . urlencode($value);
        $qStringPrev = implode('&', $qStringPrev);

        $next = $request;
        $next['startIndex'] = $request['startIndex'] + $request['pageSize'];
        $qStringNext = array();
        foreach ($next as $key => $value)
            $qStringNext[] = $key . '=' . urlencode($value);
        $qStringNext = implode('&', $qStringNext);

        $last = $request;
        $last['startIndex'] = floor($numMatches/$request['pageSize']) * $request['pageSize'];
        $qStringLast = array();
        foreach ($last as $key => $value)
            $qStringLast[] = $key . '=' . urlencode($value);
        $qStringLast = implode('&', $qStringLast);

        $hrefFirst = site_url() . 'flora/search?' . $qStringFirst;
        $hrefPrev = site_url() . 'flora/search?' . $qStringPrev;
        $hrefNext = site_url() . 'flora/search?' . $qStringNext;
        $hrefLast = site_url() . 'flora/search?' . $qStringLast;


        $nav = '<div class="query-result-nav">';
        if ($request['startIndex'] == 0) {
            $nav .= '<span class="ui-state-default"><span class="ui-icon ui-icon-seek-first"></span></span>';
            $nav .= '<span class="ui-state-default"><span class="ui-icon ui-icon-seek-prev"></span></span>';
        }
        else {
            $nav .= '<a href="' . $hrefFirst . '" class="ui-state-default" title="First page"><span 
                class="ui-icon ui-icon-seek-first"></a>';
            $nav .= '<a href="' . $hrefPrev . '" class="ui-state-default" title=" page"><span 
                class="ui-icon ui-icon-seek-prev"></a>';
        }

        $from = $request['startIndex'] + 1;
        $to = ($request['startIndex'] + $request['pageSize'] < $numMatches) ? 
                $request['startIndex'] + $request['pageSize'] : $numMatches;
        $nav .= "<span class=\"query-result-rows\">{$from}–{$to} of $numMatches</span>";

        if ($request['startIndex'] + $request['pageSize'] < $numMatches) {
            $nav .= '<a href="' . $hrefNext . '" class="ui-state-default" title="Next page"><span 
                class="ui-icon ui-icon-seek-next"></span></a>';
            $nav .= '<a href="' . $hrefLast . '" class="ui-state-default" title="Last page"><span 
                class="ui-icon ui-icon-seek-end"></span></a>';
        }
        else {
            $nav .= '<span class="ui-state-default"><span class="ui-icon ui-icon-seek-next"></span></span>';
            $nav .= '<span class="ui-state-default"><span class="ui-icon ui-icon-seek-end"></span></span>';
        }

        $nav .= '</div>';
    //}
    //else $nav = "<span class=\"query-result-rows\">1–{$numMatches} of $numMatches</span>"
?>

<?=(isset($nav)) ? $nav : FALSE?>
<div class="query-result">
    <table style="width: 100%">
        <!--tr>
            <th>Family</th>
            <th>&nbsp;</th>
            <th>Taxon name</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr-->
        <?php 
            foreach($result as $row): 
                if ($row['TaxonomicStatus'] == 'Accepted')
                    $nameclass = 'currentname';
                else
                    $nameclass = 'oldname';
                
                $name = '<span class="name ' . strtolower($row['TaxonRank']) . '">';
                $name .= str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                    class="infraepithet">', '</span> var. <span class="infraepithet">', 
                    '</span> f. <span class="infraepithet">'), $row['FullName']);
                $name .= '</span>'
        ?>
        <tr class="name">
            <td><?=($row['Family']) ? $row['Family'] : '&nbsp;'; ?></td>
            <td><?=($row['EstablishmentMeans']) ? '*' : '&nbsp;'; ?></td>
            <td>
                <a href="<?=base_url() . 'flora/taxon/' . $row['GUID']; ?>">
                    <span class="<?=$nameclass?>">
                        <?=$name?>
                        <span class="author"><?=$row['Author']?>
                    </span>
                </a>
            </td>
            <td><?=($row['EPBC']) ? $row['EPBC'] : '&nbsp;'; ?></td>
            <td><?=($row['VROT']) ? $row['VROT'] : '&nbsp;'; ?></td>
            <td><?=($row['FFG']) ? $row['FFG'] : '&nbsp;'; ?></td>
        </tr>
        
        <?php endforeach; ?>
    </table>
</div>
<?=(isset($nav)) ? $nav : FALSE?>

<?php endif; ?>
</div>

<?=form_close(); ?>


<?php require_once('footer.php'); ?>
