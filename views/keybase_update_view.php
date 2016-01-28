<?php require_once 'header.php'; ?>;
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Taxa missing in KeyBase</h2>
            <?php if ($missing_taxa): ?>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Taxon ID</th>
                        <th>Taxon name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($missing_taxa as $row): ?>
                    <tr>
                        <td><?=$row['GUID']?></td>
                        <td><?=$row['FullName']?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-right"><?=anchor(site_url() . '/admin/keybase_update/update', 'Update', array('class' => 'btn btn-default'))?></div>
            <?php else: ?>
            <p>All taxa in VicFlora can be linked to from KeyBase.</p>
            <?php endif; ?>

            <?php if ($not_in_keys): ?>
            <h2>Taxa that are not in any keys</h2>
            <p>Number of taxa: <?=count($not_in_keys)?></p>
            <ul>
                <?php foreach ($not_in_keys as $item): ?>
                <li><?=anchor(site_url() . 'flora/taxon/' . $item['GUID'], $item['FullName'])?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->


<?php require_once 'footer.php'; ?>;

