<?php require_once 'header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>ALA matched names</h2>
            <?php if ($matched_names): ?>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Scientific name</th>
                        <th>ALA Scientific name</th>
                        <th>ALA Unprocessed name</th>
                        <th># AVH</th>
                        <th># VBA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matched_names as $index => $row): ?>
                    <tr>
                        <?php if ($index == 0 || $row['scientific_name'] != $matched_names[$index-1]['scientific_name']): ?>
                        <td><b><?=anchor(site_url() . 'flora/taxon/' . $row['taxon_id'], $row['scientific_name'])?></b></td>
                        <?php else: ?>
                        <td>&nbsp;</td>
                        <?php endif; ?>
                        <td><?=$row['ala_scientific_name']?></td>
                        <td><?=$row['ala_unprocessed_scientific_name']?></td>
                        <td><?=$row['count_avh']?></td>
                        <td><?=$row['count_vba']?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'footer.php'; ?>