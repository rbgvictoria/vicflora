<?php

$start = $solrresult->params->start;
$rows = $solrresult->params->rows;

$request = array();
$request['q'] = urlencode($solrresult->params->q);
if (isset ($solrresult->params->fq)) {
    
    if (is_array($solrresult->params->fq)) {
        $fqs = array();
        foreach ($solrresult->params->fq as $fq) {
            if (substr($fq, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon')
                $fqs[] = 'fq=' . urlencode($fq);
        }
            $request['fq'] = substr(implode('&', $fqs) ,3);
    }
    else {
        if (substr($fq, 0, strlen('end_or_higher_taxon')) != 'end_or_higher_taxon')
            $request['fq'] = $solrresult->params->fq;
    }    
}
$request['rows'] = $rows;
$request['start'] = $start;

    //if ($solrresult->numFound >= $rows) {
        $first = $request;
        $first['start'] = 0;
        $qStringFirst = array();
        foreach ($first as $key => $value)
            $qStringFirst[] = $key . '=' . $value;
        $qStringFirst = implode('&', $qStringFirst);

        $prev = $request;
        $prev['start'] = ($start > $rows) ? 
                $start - $rows : 0;
        $qStringPrev = array();
        foreach ($prev as $key => $value)
            $qStringPrev[] = $key . '=' . $value;
        $qStringPrev = implode('&', $qStringPrev);

        $next = $request;
        $next['start'] = $start + $rows;
        $qStringNext = array();
        foreach ($next as $key => $value)
            $qStringNext[] = $key . '=' . $value;
        $qStringNext = implode('&', $qStringNext);

        $last = $request;
        $last['start'] = floor($solrresult->numFound/$rows) * $rows;
        $qStringLast = array();
        foreach ($last as $key => $value)
            $qStringLast[] = $key . '=' . $value;
        $qStringLast = implode('&', $qStringLast);

        $hrefFirst = site_url() . 'flora/search?' . $qStringFirst;
        $hrefPrev = site_url() . 'flora/search?' . $qStringPrev;
        $hrefNext = site_url() . 'flora/search?' . $qStringNext;
        $hrefLast = site_url() . 'flora/search?' . $qStringLast;


        $nav = '<div class="query-result-nav text-center">';
        if ($start == 0) {
            $nav .= '<i class="fa fa-fast-backward"></i>';
            $nav .= '<i class="fa fa-backward"></i>';
        }
        else {
            $nav .= '<a href="' . $hrefFirst . '" title="First page"><i class="fa fa-fast-backward"></i></a>';
            $nav .= '<a href="' . $hrefPrev . '" title="Previous page"><i class="fa fa-backward"></i></a>';
        }

        $from = $start + 1;
        $to = ($start + $rows < $solrresult->numFound) ? 
                $start + $rows : $solrresult->numFound;
        $nav .= "<span class=\"query-result-rows\">{$from}–{$to} of $solrresult->numFound</span>";

        if ($start + $rows < $solrresult->numFound) {
            $nav .= '<a href="' . $hrefNext . '" title="Next page"><i class="fa fa-forward"></i></a>';
            $nav .= '<a href="' . $hrefLast . '" title="Last page"><i class="fa fa-fast-forward"></i></a>';
        }
        else {
            $nav .= '<i class="fa fa-forward"></i>';
            $nav .= '<i class="fa fa-fast-forward"></i>';
        }

        $nav .= '</div>';
    //}
    //else $nav = "<span class=\"query-result-rows\">1–{$solrresult->numFound} of $solrresult->numFound</span>"
?>
