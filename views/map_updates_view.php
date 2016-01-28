<?php require_once 'header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="pull-right"><button class="accept-all btn btn-default">Accept all</button></div>
            <h2>Map updates</h2>
            <?php if($updates): ?>
            <?php foreach($updates as $day): ?>
            <h3><?=date('j F Y', strtotime($day['date'])); ?></h3>
            <?php foreach($day['taxa'] as $taxon): ?>
            <h3><?=anchor(site_url() . 'admin/editdistribution/' . $taxon['taxon_id'], $taxon['scientific_name'])?></h3>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>ALA record number</th>
                        <th>Catalogue number</th>
                        <th>Longitude</th>
                        <th>Latitude</th>
                        <th>Bioregion</th>
                        <th>Occurrence status</th>
                        <th>Establishment means</th>
                        <th>New record</th>
                        <th>Updated record</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taxon['occurrences'] as $occ): ?>
                    <tr>
                        <td><?=anchor('http://avh.ala.org.au/occurrences/' . $occ['uuid'], $occ['uuid'], array('target' => '_blank'))?></td>
                        <td><?=$occ['catalog_number']?></td>
                        <td><?=$occ['decimal_longitude']?></td>
                        <td><?=$occ['decimal_latitude']?></td>
                        <td><?=$occ['sub_name_7']?></td>
                        <td><?=$occ['occurrence_status']?></td>
                        <td><?=$occ['establishment_means']?></td>
                        <td><?=$occ['is_new_record']?></td>
                        <td><?=$occ['is_updated_record']?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div> <!-- .col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php require_once 'footer.php'; ?>

