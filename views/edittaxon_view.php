<?php 
    require_once('header.php'); 
    
    function check($value, $type, $taxondata) {
        return ($type == 'edit') ? $taxondata[$value] : '';
    }

?>
<div class="container">
    <div class="row">
<?php if ($type == 'edit'): ?>
        
<div class="taxon-edit-menu col-md-12 text-right">
    <a href="<?=site_url()?>flora/taxon/<?=$taxondata['GUID']?>">View</a>
</div>
<?php endif; ?>
<div class="col-lg-12">
    <div class="taxon-name">
        <h2>
            <?php if ($type == 'edit'): ?>
            <span class="h1">Edit taxon:</span>
            <?php if ($taxondata['TaxonomicStatus'] == 'accepted'): ?>
            <span class="currentname <?=($taxondata['RankID']>=180) ? ' italic' : '';?>">
            <?php else: ?>
            <span class="oldname <?=($taxondata['RankID']>=180) ? ' italic' : '';?>">
            <?php endif; ?>
                <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                        class="namebit">', '</span> var. <span class="namebit">', 
                        '</span> f. <span class="namebit">'), $taxondata['FullName']);?></span>
            <?php if($taxondata['Author']): ?>
                <span class="author"><?=$taxondata['Author']?></span>
            <?php endif; ?>
            </span>
            <?php elseif ($type == 'add child'): ?>
            Add child:
            <span class="currentname <?=($taxondata['ParentRankID']>=180) ? ' italic' : '';?>">
            <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                    class="namebit">', '</span> var. <span class="namebit">', 
                    '</span> f. <span class="namebit">'), $taxondata['ParentName']);?></span>
            <?php if($taxondata['ParentNameAuthor']): ?>
                <span class="author"><?=$taxondata['ParentNameAuthor']?></span>
            <?php endif; ?>
            </span>
            <?php endif; ?>
        </h2>
    </div>
</div>

<?=form_open(FALSE, array('class' => 'edit-taxon-form form-horizontal')); ?>
<div id="tab-taxon-detail">
    <?=form_hidden('taxon_id', check('TaxonID', $type, $taxondata)); ?>
    
    <!-- Parent -->
    <div class="col-lg-12">
        <div class="form-group">
            <label for="parent_display" class="col-md-2 control-label text-left">Parent</label>
            <div class="currentname col-md-3">
                    <?php
                        /*$name = '<span class="namebit">';
                        $name .= str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                            class="namebit">', '</span> var. <span class="namebit">', 
                            '</span> f. <span class="namebit">'), $taxondata['ParentName']);
                        $name .= '</span>';*/
                        $name = $taxondata['ParentName'];
                        $name .= ($taxondata['ParentNameAuthor']) ? ' ' . $taxondata['ParentNameAuthor'] : '';
                    ?>
                <?=form_input(array(
                    'id' => 'parent_display',
                    'value' => $name,
                    'disabled' => 'disabled',
                    'class' => 'form-control'
                )); ?>
            </div>
            <?php if ($type == 'edit' && $taxondata['RankID'] <= 180 && $taxondata['TaxonomicStatus'] == 'accepted'): ?>
            <div class="col-md-4" id="change-parent">
                <a href="#" class="btn btn-primary">Change parent</a>
                <div class="col-md-10">
                <input id="new_parent" class="form-control" value="<?=$name?>"/>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>


    <?=form_hidden('parent_name', $taxondata['ParentName']); ?>
    <?=form_hidden('parent_name_old', $taxondata['ParentName']); ?>
    <?=form_hidden('parent_id', $taxondata['ParentID']); ?>
    <?=form_hidden('parent_id_old', $taxondata['ParentID']); ?>

    <?php
    switch ($taxondata['ParentRankID']) {
        case '100':
            $options = array(
                '11' => 'family'
            );
            break;

        case '140':
            $options = array(
                '12' => 'genus'
            );
            break;

        case '180':
            $options = array(
                '13' => 'species'
            );
            break;

        case '220':
            $options = array(
                '14' => 'subspecies',
                '22' => 'nothosubspecies',
                '15' => 'variety',
                '21' => 'nothovariety',
                '17' => 'forma'
            );
            break;

        default:
            $options = array();
            break;
    }
    ?>
    
    
    <!-- Taxon rank -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Taxon rank', 'taxon_tree_def_item_id', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-3">
                <?=form_dropdown(
                    'taxon_tree_def_item_id',
                        $options,
                    check('TaxonTreeDefItemID', $type, $taxondata),
                    'id="taxon_tree_def_item_id" class="form-control"'
                ); ?>
                </div>
            <?=form_hidden('taxon_tree_def_item_id_old', check('TaxonTreeDefItemID', $type, $taxondata)); ?>
        </div>
    </div>

    <div class="edit-form-section clearfix">
    <h3 class="col-md-12">Taxon name</h3>

    <?=form_hidden('name_id', check('NameID', $type, $taxondata)); ?>
    
    <!-- Name -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Name', 'name', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-3">
                <?=form_input(array(
                    'name' => 'name',
                    'id' => 'name',
                    'value' => check('Name', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('name_old', check('Name', $type, $taxondata)); ?>
        </div>
    </div>

    <!-- Full name -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Full name', 'full_name', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
                <?=form_input(array(
                    'id' => 'full_name_display',
                    'value' => check('FullName', $type, $taxondata),
                    'disabled' => 'disabled',
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('full_name_old', check('FullName', $type, $taxondata)); ?>
            <?=form_hidden('full_name', check('FullName', $type, $taxondata)); ?>
        </div>
    </div>

    <!-- Author -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Author', 'author', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
            <?=form_input(array(
                'name' => 'author',
                'id' => 'author',
                'value' => check('Author', $type, $taxondata),
                'class' => 'form-control'
            )); ?>
            </div>
        <?=form_hidden('author_old', check('Author', $type, $taxondata)); ?>
        </div>
    </div>

    <!-- Nomenclatural status -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Nomenclatural status', 'nomenclatural_status', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-3">
                <?=form_input(array(
                    'name' => 'nomenclatural_status',
                    'id' => 'nomenclatural_status',
                    'value' => check('NomenclaturalNote', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
        </div>
        <?=form_hidden('nomenclatural_status_old', check('NomenclaturalNote', $type, $taxondata)); ?>
    </div>

    <!-- Name usage -->
    <div class="col-md-12">
        <div class="form-group">
        <?=form_label('Sensu', 'sensu', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
                <?=form_input(array(
                    'name' => 'sensu',
                    'id' => 'sensu',
                    'value' => check('Sensu', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
        <?=form_hidden('sensu_old', check('Sensu', $type, $taxondata)); ?>
        </div>
    </div>
    </div> <!-- /.edit-form-section clearfix -->

    <!-- Etymology -->
    <div class="col-md-12">
        <div class="form-group">
        <?=form_label('Etymology', 'etymology', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
                <?=form_textarea(array(
                    'name' => 'etymology',
                    'id' => 'etymology',
                    'value' => check('Etymology', $type, $taxondata),
                    'class' => 'form-control',
                    'rows' => 2
                )); ?>
            </div>
        <?=form_hidden('etymology_old', check('Etymology', $type, $taxondata)); ?>
        </div>
    </div>
    </div> <!-- /.edit-form-section clearfix -->

    <div class="edit-form-section clearfix"> <!-- Protologue -->
        <?=form_hidden('protologue_id', check('ReferenceID', $type, $taxondata)); ?>
        
        <h3 class="col-md-12">Protologue</h3>
    
    <!-- In-author -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('In author', 'in_author', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
            <?=form_input(array(
                'name' => 'in_author',
                'id' => 'in_author',
                'value' => check('InAuthor', $type, $taxondata),
                'class' => 'form-control'
            )); ?>
            <?=form_hidden('in_author_old', check('InAuthor', $type, $taxondata)); ?>
            </div>
        </div>
    </div>

    <!-- Journal/book -->
    <div class="col-md-12">
        <div class="form-group">
            <?=form_label('Journal/book', 'journal_or_book', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-6">
                <?=form_input(array(
                    'name' => 'journal_or_book',
                    'id' => 'journal_or_book',
                    'value' => check('JournalOrBook', $type, $taxondata),
                'class' => 'form-control'
                )); ?>
            <?=form_hidden('journal_or_book_old', check('JournalOrBook', $type, $taxondata)); ?>
            </div>
        </div>
    </div>
        

    <div class="col-md-12">
        <!-- Series -->
        <div class="form-group">
            <?=form_label('Series', 'series', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'series',
                    'id' => 'series',
                    'value' => check('Series', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
        <?=form_hidden('series_old', check('Series', $type, $taxondata)); ?>

        <!-- Edition -->
           <?=form_label('Edition', 'edition', array('class' => 'col-md-2 control-label')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'edition',
                    'id' => 'edition',
                    'value' => check('Edition', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
                </div>
        </div>
        <?=form_hidden('edition_old', check('Edition', $type, $taxondata)); ?>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <!-- Volume -->
            <?=form_label('Volume', 'volume', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'volume',
                    'id' => 'volume',
                    'value' => check('Volume', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('volume_old', check('Volume', $type, $taxondata)); ?>

            <!-- Part -->
            <?=form_label('Part', 'part', array('class' => 'col-md-2 control-label')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'part',
                    'id' => 'part',
                    'value' => check('Part', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('part_old', check('Part', $type, $taxondata)); ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <!-- Page -->
            <?=form_label('Page', 'page', array('class' => 'col-md-2 control-label text-left')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'page',
                    'id' => 'page',
                    'value' => check('Page', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('page_old', check('Page', $type, $taxondata)); ?>

            <!-- Publication year -->
            <?=form_label('Year', 'publication_year', array('class' => 'col-md-2 control-label')); ?>
            <div class="col-md-2">
                <?=form_input(array(
                    'name' => 'publication_year',
                    'id' => 'publication_year',
                    'value' => check('PublicationYear', $type, $taxondata),
                    'class' => 'form-control'
                )); ?>
            </div>
            <?=form_hidden('publication_year_old', check('PublicationYear', $type, $taxondata)); ?>
        </div>
    </div>
    </div> <!-- /.edit-form-section clearfix -->
    
    <!-- Status -->
    <div class="edit-form-section clearfix" id="status">
        <h3 class="col-md-12">Status</h3>
        <?=form_hidden('accepted_name_id_old', check('AcceptedID', $type, $taxondata)); ?>
        <?=form_hidden('accepted_name_id', check('AcceptedID', $type, $taxondata)); ?>
        
        
        <div class="col-md-12" id="accepted_name">
        <?php if (check('AcceptedName', $type, $taxondata) && in_array($taxondata['TaxonomicStatus'], array('homotypic synonym',
            'heterotypic synonym', 'synonym', 'misapplication'))): ?>
            <span class="eleven columns">Currently accepted name: 
        <?php
            $name = '<span class="currentname"><span class="namebit">';
            $name .= str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                class="namebit">', '</span> var. <span class="namebit">', 
                '</span> f. <span class="namebit">'), check('AcceptedName', $type, $taxondata));
            $name .= '</span></span>';
            $name .= (check('AcceptedNameAuthor', $type, $taxondata)) ? ' ' . $taxondata['AcceptedNameAuthor'] : '';
            echo $name;
            
            $source = (check('AcceptedNameSource', $type, $taxondata)) ? ' ' . $taxondata['AcceptedNameSource'] : '';
            if ($source)
                echo ', <i>cf.</i> ' . $taxondata['AcceptedNameSource'];
            
        ?>
            </span>
        <?php endif; ?>
        </div>

        <!-- Taxonomic status -->
        <div class="col-md-12">
            <?=form_hidden('taxonomic_status', ($type == 'add child') ? 'accepted' : $taxondata['TaxonomicStatus']); ?>
            <?=form_hidden('taxonomic_status_old', check('TaxonomicStatus', $type, $taxondata)); ?>
            
            <div class="form-group">
                <?=form_label('Taxonomic status', 'taxonomic_status', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-3">
                    <?=form_dropdown(
                        FALSE,
                        array(
                            FALSE => '',
                            'accepted' => 'accepted',
                            'homotypic synonym' => 'homotypic synonym',
                            'heterotypic synonym' => 'heterotypic synonym',
                            'synonym' => 'synonym',
                            'misapplication' => 'misapplication'
                        ),
                        ($type == 'add child') ? 'accepted' : check('TaxonomicStatus', $type, $taxondata),
                        'id="taxonomic_status_display" disabled="disabled" class="form-control"'
                    ); ?>
                </div>
                <div class="col-md-2">
                    <button type="button" id="new_accepted_name" class="btn btn-primary" data-toggle="modal" data-target="#acceptedNameModal">
                        Add new accepted name
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Occurrence status -->
        <div class="col-md-12">
            <div class="form-group">
            <?=form_label('Occurrence status', 'occurrence_status', array('class' => 'col-md-2 control-label text-left')); ?>
            <?php 
                if ($type =='edit' && $taxondata['TaxonomicStatus'] != 'accepted') {
                    $options = array(
                        FALSE => '',
                       'excluded' => 'excluded'
                    );
                }
                else
                    $options = array(
                        FALSE => '',
                        'present' => 'present',
                        'endemic' => 'endemic',
                        'extinct' => 'extinct',
                        'excluded' => 'excluded'
                    );
                    
            ?>
                <div class="col-md-3">
                    <?=form_dropdown(
                        'occurrence_status',
                        $options,
                        ($type == 'add child') ? 'present' : check('OccurrenceStatus', $type, $taxondata),
                        'id="occurrence_status" class="form-control"'
                    ); ?>
                </div>
                <?=form_hidden('occurrence_status_old', check('OccurrenceStatus', $type, $taxondata)); ?>
            </div>
        </div>

        <!-- Establishment means -->
        <?php if (check('TaxonomicStatus', $type, $taxondata) == 'accepted' || $type == 'add child'): ?>
        <div class="col-md-12">
            <div class="form-group">
                <?=form_label('Establishment means', 'establishment_means', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-3">
                    <?=form_dropdown(
                        'establishment_means',
                        array(
                            FALSE => '',
                            'native' => 'native',
                            'native (naturalised in part(s) of state)' => 'native and naturalised',
                            'introduced' => 'introduced',
                            'naturalised' => 'naturalised',
                            'adventive' => 'sparingly established',
                            'uncertain' => 'uncertain'
                        ),
                        ($type == 'add child') ? 'native' : check('EstablishmentMeans', $type, $taxondata),
                        'id="establishment_means" class="form-control"'
                    ); ?>
                </div>
                <?=form_hidden('establishment_means_old', check('EstablishmentMeans', $type, $taxondata)); ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div> <!-- /#status.edit-form-section clearfix -->

    <!-- Threat status -->
    <?php if (check('TaxonomicStatus', $type, $taxondata) == 'accepted' || $type == 'add child'): ?>
    <div class="edit-form-section clearfix">
        <h3 class="col-md-12">Threat status</h3>
        <?php 
            $vrot = FALSE;
            $naturalised = FALSE;
            $ffg = FALSE;
            if ($type != 'add child' && $attributes) {
                foreach ($attributes as $attr) {
                    if ($attr['Attribute'] == 'VROT') {
                        $vrot = $attr['StrValue'];
                        echo form_hidden('vrot_attribute_id', $attr['TaxonAttributeID']);
                    }
                    elseif ($attr['Attribute'] == 'FFG') {
                        $ffg = $attr['StrValue'];
                        echo form_hidden('ffg_attribute_id', $attr['TaxonAttributeID']);
                    }
                    elseif ($attr['Attribute'] == 'Naturalised status') {
                        $naturalised = $attr['StrValue'];
                        echo form_hidden('naturalised_attribute_id', $attr['TaxonAttributeID']);
                    }
                }
            }
        ?>

        <div class="col-md-12">
            <div class="attribute form-group">
                <?=form_hidden('vrot_old', $vrot); ?>
                <?=form_label('VROT', 'vrot', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-2">
                    <?=form_dropdown('vrot',
                            array(
                                '',
                                'x' => 'x – extinct',
                                'e' => 'e – endangered',
                                'v' => 'v – vulnerable',
                                'r' => 'r – rare',
                                'k' => 'k – unknown',
                            ),
                            $vrot,
                            'id="vrot" class="form-control"'
                        ); 
                    ?>
                </div>
                <?=form_hidden('ffg_old', $ffg); ?>
                <?=form_label('Flora and Fauna Guarantee Act', 'ffg', array('class' => 'col-md-4 control-label text-right')); ?>
                <div class="col-md-2">
                    <?=form_dropdown('ffg',
                            array(
                                '',
                                'L' => 'Incipiently extinct',
                            ),
                            $ffg,
                            'id="ffg" class="form-control"'
                        ); 
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- /.edit-form-section clearfix -->
    <?php endif;?>

    <!-- APNI -->
    <div class="edit-form-section clearfix">
        <h3 class="col-md-12">APNI name match</h3>
        
        <div class="col-md-12">
            <table class="apni-name-match table table-bordered">
                <thead>
                    <tr>
                        <th>APNI No.</th>
                        <th>APNI full name with authors</th>
                        <th>Match type</th>
                        <th>Verified</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($apni) && $apni):?>
                    <?php foreach ($apni as $index => $apniname): ?>
                    <tr>
                        <td><?=anchor('https://www.anbg.gov.au/cgi-bin/apni?taxon_id=' . $apniname['ApniNo'], $apniname['ApniNo'],
                                array('target' => '_blank')); ?></td>
                        <td><?=$apniname['APNIFullNameWithAuthor']?><?=form_hidden("apni_id[$index]", $apniname['ApniID']);?></td>
                        <td><?=$apniname['MatchType']?></td>
                        <td><?=form_checkbox(array('name' => "apni_match_verified[$index]", 'value' => '1', 'checked' => $apniname['IsVerified']))?></td>
                        <td><?=form_checkbox(array('name' => "apni_delete[$index]", 'value' => '1'))?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
        <div class="new-apni-row col-md-12"><button class="apni-manual btn btn-primary btn-sm">Add APNI number (manually)</button></div>
    </div> <!-- /.edit-form-section clearfix -->
    
    <!-- Common names -->
    <?php if (isset($commonnames)): ?>
    <div class="edit-form-section clearfix">
        <h3 class="col-md-12">Vernacular names</h3>
        <div class="col-md-12">
            <table class="common-names table table-bordered">
                <thead>
                    <tr>
                        <th>Vernacular name</th>
                        <th>Preferred name</th>
                        <th>Usage</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($commonnames): ?>
                    <?php foreach ($commonnames as $index => $commonname):?>
                    <tr>
                        <td><?=form_input(array('name' => "common_name[$index]", 'value' => $commonname['CommonName']))?></td>
                        <td><?=form_checkbox(array('name' => "preferred[$index]", 'value' => 1, 'checked' => $commonname['IsPreferred']))?></td>
                        <td><?=form_input(array('name' => "usage[$index]", 'value' => $commonname['NameUsage']))?></td>
                        <td><?=form_checkbox(array('name' => "delete[$index]", 'value' => 1, 'checked' => FALSE))?>
                        <?=form_hidden(array(
                            "common_name_id[$index]" => $commonname['CommonNameID'],
                            "common_name_old[$index]" => $commonname['CommonName'],
                            "preferred_old[$index]" => $commonname['IsPreferred'],
                            "usage_old[$index]" => $commonname['NameUsage']
                        )); ?>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="new-common-name-row"><a href="#" class="btn btn-primary btn-sm">Add row</a></div>
        </div>
    </div> <!-- /.edit-form-section clearfix -->
    <?php endif; ?>

    <!-- Notes -->
    <div class="edit-form-section clearfix">
        <h3 class="col-md-12">Notes</h3>
        <!-- Taxon remarks -->
        <div class="col-md-12">
            <div class="form-group">
                <?=form_label('Taxon remarks', 'taxon_remarks', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-10">
                <?=form_textarea(array(
                    'name' => 'taxon_remarks',
                    'id' => 'taxon_remarks',
                    'value' => check('Remarks', $type, $taxondata),
                    'rows' => 3,
                    'class' => 'ckeditor form-control'
                )); ?>
                </div>
                <?=form_hidden('taxon_remarks_old', check('Remarks', $type, $taxondata)); ?>
            </div>
        </div>
        
        <!-- Taxon remarks -->
        <div class="col-md-12">
            <div class="form-group">
                <?=form_label('Editor notes', 'editor_notes', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-10">
                <?=form_textarea(array(
                    'name' => 'editor_notes',
                    'id' => 'editor_notes',
                    'value' => check('EditorNotes', $type, $taxondata),
                    'rows' => 3,
                    'class' => 'ckeditor form-control'
                )); ?>
                </div>
                <?=form_hidden('editor_notes_old', check('EditorNotes', $type, $taxondata)); ?>
            </div>
        </div>
    </div>

    <?php if ($type == 'edit'): ?>
    <!-- Remove from index -->    
    <div class="edit-form-section clearfix">
        <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <div class="checkbox">
                        <label>
                        <?=form_checkbox(array(
                            'name' => 'do_not_index',
                            'id' => 'do_not_index',
                            'value' => '1',
                            'checked' => ($taxondata['DoNotIndex']) ? 'checked' : FALSE
                        )); ?>Do not index
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Submit -->
    <div class="edit-form-section clearfix">
        <div class="col-md-12 text-center">
            <?=form_submit(array('name' => 'submit', 'value' => 'Save', 'class' => 'btn btn-primary')); ?>
        </div>
    </div>
    
    <div class="edit-form-section clearfix record-update">

        
<?php if ($type == 'edit'): ?>
        
    <!-- Change history -->
    <?php if (isset($changes) && $changes): ?>
        <h3 class="col-md-12">Change history</h3>
        <div class="col-md-12">
            <table class="formatted-table table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>New name</th>
                        <th>Change type</th>
                        <th>Source</th>
                        <th>Changed by</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($changes as $index => $ch): ?>
                    <tr>
                        <td>
                            <?=form_hidden("change_id[$index]", $ch['ChangeID']); ?>
                            <?=$ch['ChangeDate']?>
                        </td>
                        <td>
                            <b><?=$ch['AcceptedName']?></b><?=($ch['AcceptedNameAuthor']) ? ' ' . $ch['AcceptedNameAuthor'] : '';?>
                        </td>
                        <td>
                            <?=$ch['ChangeType']; ?>
                        </td>
                        <td>
                            <?=$ch['Source']?>
                        </td>
                        <td>
                            <?=$ch['ChangedBy']?>
                        </td>
                    </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 text-left">Created by</label>
                <span class="form-control-static col-md-8"><?=$taxondata['CreatedBy']?></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 text-left">Created</label>
                <span class="form-control-static col-md-8"><?=$taxondata['TimestampCreated']?></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 text-left">Modified by</label>
                <span class="form-control-static col-md-8"><?=$taxondata['ModifiedBy']?></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4 text-left">Modified</label>
                <span class="form-control-static col-md-8"><?=$taxondata['TimestampModified']?></span>
            </div>
        </div>
    </div>
    
<?php endif; ?>    

    
</div> <!-- /#tab-taxon-detail -->

<?=form_close(); ?>


    </div> <!-- /.row -->
</div> <!-- /.container -->



<!-- HTML element for the New accepted name dialog -->

<!-- Modal: New accepted name -->
<div class="modal" id="acceptedNameModal" tabindex="-1" role="dialog" aria-labelledby="acceptedNameModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="acceptedNameModalLabel">New accepted name</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 form-horizontal">
                    <div class="form-group">
                        <label for="nn_name" class="col-md-3 control-label">Name</label>
                        <div class="col-md-9">
                            <input id="nn_name" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nn_type" class="col-md-3 control-label">Taxonomic status</label>
                        <div class="col-md-9">
                            <select id="nn_type"class="form-control">
                                <option></option>
                                <option value="accepted">accepted</option>
                                <option value="homotypic synonym">homotypic synonym</option>
                                <option value="heterotypic synonym">heterotypic synonym</option>
                                <option value="synonym">synonym</option>
                                <option value="misapplication">misapplication</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nn_source" class="col-md-3 control-label">Source</label>
                        <div class="col-md-9">
                            <select id="nn_source" class="form-control">
                                <option></option>
                                <option value="Flora of Victoria">Flora of Victoria</option>
                                <option value="Census edn 6">Census edn 6</option>
                                <option value="Census edn 7">Census edn 7</option>
                                <option value="Census edn 8">Census edn 8</option>
                                <option value="Census 2013">Census 2013</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="save-new-accepted-name">Save</button>
      </div>
    </div>
  </div>
</div>

<!--
<div id="find-in-apni-dialog" title="APNI match(es)">
    <div class="loading"><img src="http://data.rbg.vic.gov.au/vicflora/css/images/ajax-loader.gif" alt="Loading..." 
                              height="16" width="16" /></div>
</div>
-->


<?php require_once('footer.php'); ?>



