<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        
        <div class="taxon-edit-menu col-md-12 text-right">
            <a href="<?=site_url()?>flora/taxon/<?=$taxondata['GUID']?>">View</a>
        </div>
        <div class="col-md-12">
            <div class="taxon-name">
                <h2>
                    Edit profile:
                    <span class="currentname <?=($taxondata['RankID']>=180) ? ' italic' : '';?>">
                        <span class="namebit"><?=str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                                class="namebit">', '</span> var. <span class="namebit">', 
                                '</span> f. <span class="namebit">'), $taxondata['FullName']);?></span>
                    <?php if($taxondata['Author']): ?>
                        <span class="author"><?=$taxondata['Author']?></span>
                    <?php endif; ?>
                    </span>
                </h2>
            </div>

        <?=form_open(); ?>
        <?php if (isset($profiles)): ?>
            <?php foreach($profiles as $index => $profile): ?>
            <?php
                if ($profile['AsFullName']) {
                    $as = '<span class="oldname">';
                    $as .= '<span class="namebit">' . str_replace(array(' subsp. ', ' var. ', ' f. '), array('</span> subsp. <span 
                        class="namebit">', '</span> var. <span class="namebit">', 
                        '</span> f. <span class="namebit">'), $profile['AsFullName']) . '</span>';
                    if ($profile['AsAuthor'])
                        $as .= ' <span class="author">' . $profile['AsAuthor'] . '</span>';
                    $as .= ' (' . $profile['TaxonomicStatus'] . ')';
                    $as .= '</span>';
                }
                else ($as = '');

            ?>
            <div class="profile">
                <?php if ($profile['TaxonomicStatus'] == 'accepted' || count($profiles) == 1 || $index == 0): ?>
                <div class="profile-editor">
                    <?php if ($as): ?>
                    <div class="acc-profile-as"><b>As:</b> <?=$as?></div>
                    <?php endif; ?>
                    <?php if (isset($diff)): ?>
                    <div class="diff"><?=$diff?></div>
                    <?php endif; ?>

                    <?=form_hidden('new_accepted_id', $this->input->post('new_accepted_id')); ?>
                    <?=form_hidden('new_accepted_name', $this->input->post('new_accepted_name')); ?>
                    <p id="assign_to_taxon"><a href="#">Assign to new taxon</a><input id="new_taxon"/><span id="new_accepted_name"><?=$this->input->post('new_accepted_name')?></span></p>

                    <?=form_hidden('profile_id', $profile['ProfileID']); ?>
                    <?=form_hidden('stored_profile', $profile['Profile']); ?>
                    <?=form_textarea(array(
                        'name' => 'profile',
                        'id' => 'profile',
                        'value'=> ($this->input->post('compare')) ? $this->input->post('profile') : $profile['Profile']
                    ));?>
                    <div class="editor-menu text-right">
                        <?=form_checkbox(array(
                            'name' => 'minor_edit',
                            'id' => 'minor_edit',
                            'value' => "1"
                        ));?>
                        <?=form_label('This is a minor edit', 'minor_edit'); ?>
                        <?=form_submit(array(
                            'name' => 'compare',
                            'value' => 'Show changes',
                            'class' => 'btn btn-default')); 
                        ?>
                        <?=form_submit(array(
                            'name' => 'save',
                            'value' => 'Save',
                            'class' => 'btn btn-default'
                        )); ?>
                        <?=form_submit(array(
                            'name' => 'cancel',
                            'value' => 'Cancel',
                            'class' => 'btn btn-default'
                        )); ?>
                    </div>
                </div>

                <?php else: ?>
                <div class="profile-as"><?=$as?></div>
                <div class="profile-text"><?=$profile['Profile'];?></div>
                <?php endif; ?>

                <?php if ($profile['Author']): ?>
                <div class="profile-source">
                    <b>Source: </b><?=$profile['Author'] . ' (' . $profile['PublicationYear'] . '). ' .
                        preg_replace('/~([^~]*)~/', '<i>$1</i>', $profile['Title']) . '. In: ' . $profile['InAuthor'] . ', <i>' . 
                        preg_replace('/(Vol\. [2-4]), /', "</i><b>$1</b>, <i>", $profile['InTitle']) . '</i>. ' . 
                        $profile['Publisher'] . ', ' . $profile['PlaceOfPublication'] . '.'; ?>
                </div>
                <?php if ($profile['IsUpdated']): ?>
                <div class="updated-by">
                    <b>Updated by:</b> <?=$profile['UpdatedBy']?>, <?=$profile['DateUpdated'];?>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="created-by">
                    <b>Created by:</b> <?=$profile['CreatedBy']?>, <?=$profile['DateCreated']?>
                </div>
                <?php if ($profile['IsUpdated']): ?>
                <div class="updated-by">
                    <b>Updated by:</b> <?=$profile['UpdatedBy']?>, <?=$profile['DateUpdated'];?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <h3>References</h3>
                <div id="taxon-references">
                    <?php if ($taxonReferences): ?>
                    <?php foreach ($taxonReferences as $ref): ?>
                    <p>
                        <b><?=$ref->label?></b>
                        <?=anchor(site_url() . 'reference/show/' . $ref->value, '<i class="fa fa-search"></i>'); ?>
                        <?=anchor(site_url() . 'reference/delete_taxon_reference_ajax/', '<i class="fa fa-trash"></i>', array('class' => 'delete-taxon-reference', 'data-vicflora-reference-id' => $ref->value)); ?><br/>
                        <?=$ref->description?>
                    </p>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                         <div class="form-horizontal">
                            <div class="input-group">
                                <?=form_input(array('name' => 'reference', 'id' => 'reference-search', 'class' => 'form-control input-sm')); ?>
                                <?=form_input(array('type' => 'hidden', 'name' => 'reference-id', 'id' => 'reference-id')); ?>
                                <div class="input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <?=anchor(site_url() . 'reference/create/', 'New reference', array('class' => 'btn btn-default btn-sm')); ?>
                    </div>
                </div>
                
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if(isset($taxondata['GUID']) && $taxondata['TaxonomicStatus'] == 'accepted'): ?>
            <div class="new-profile text-center"><a href="<?=site_url()?>admin/newprofile/<?=$taxondata['GUID']?>" class="btn btn-default">Add new profile</a></div>
            <?php endif; ?>
            <?=form_close()?>
        </div>
    </div>
</div> <!-- /.container -->
<?php require_once('footer.php'); ?>

