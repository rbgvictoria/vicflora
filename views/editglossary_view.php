<?php require_once 'header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <?php if (isset($term) && $term): ?>
            <h1>Edit glossary term: <span class="glossary-term-name"><?=$term->Name?></span></h1>
            <?php else: ?>
            <h1>New glossary term</h1>
            <?php endif; ?>
        </div>
        <div class="col-lg-12">
            <?=form_open('', array('class' => 'form form-horizontal')); ?>
            
            <div class="form-group">
                <label for="term_name" class="col-md-2 control-label text-left">Name</label>
                <div class="col-md-4">
                <?=form_input(array(
                    'id' => 'term_name',
                    'name' => 'term_name',
                    'value' => (isset($term) && $term) ? $term->Name : FALSE,
                    'class' => 'form-control'
                )); ?><?=form_hidden('term_name_old', (isset($term) && $term) ? $term->Name : FALSE);?>
                </div>
                <?php if (isset($term) && $term): ?>
                    <div class="col-md-6 text-right"><button class="term-delete btn btn-default"><i class="fa fa-trash-o fa-lg"></i></button></div>
                <?php endif;?>
            </div>
            
            <div class="form-group">
                <?=form_label('Definition', 'term_definition', array('class' => 'col-md-2 control-label text-left')); ?>
                <div class="col-md-10">
                <?=form_textarea(array(
                    'name' => 'term_definition',
                    'id' => 'term_definition',
                    'value' => (isset($term) && $term) ? $term->Definition : FALSE,
                    'rows' => 3,
                    'class' => 'ckeditor form-control'
                )); ?><?=form_hidden('term_definition_old', (isset($term) && $term) ? $term->Definition : FALSE);?>
                </div>
            </div>
            
            <?php 
                $options = array(
                    '' => '',
                    1 => 'hasSynonym',
                    2 => 'hasExactSynonym',
                    3 => 'hasMoreInclusiveSynonym',
                    4 => 'hasLessInclusiveSynonym',
                    5 => 'hasPartiallyOverlappingSynonym',
                    6 => 'hasApproximatelyEqualSynonym',
                    9 => 'hasVariation',
                    12 => 'isVariationOf',
                    7 => 'hasPlural',
                    8 => 'isPluralOf',
                    10 => 'hasAdjective',
                    13 => 'isAdjectiveOf',
                    14 => 'hasAbbreviation',
                    15 => 'isAbbreviationOf',
                    16 => 'hasSingular',
                    17 => 'isSingularOf',
                    20 => 'hasTranslation',
                    21 => 'isTranslationOf',
                    19 => 'isOpposedTo',
                    11 => 'isRelatedTo(cf.)',
                    18 => 'isRelatedTo(see)'                    
                );
            ?>
            
            <div class="form-group" id="glossary_relationships">
                <label class="col-md-2 control-label text-left">Relationships</label>
                <div class="col-md-10">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Relationship type</th>  
                                <th>Related term</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $numrel = 0;?>
                            <?php if (isset($relationships) && $relationships):?>
                            <?php $numrel = count($relationships); ?>
                            <?php foreach ($relationships as $index => $rel): ?>
                            <tr>
                                <td><?=form_hidden("rel_id[$index]", $rel->RelationshipID)?><?=form_dropdown("rel_type[$index]", $options, $rel->relationshipTypeID, 'class="form-control" disabled="disabled"')?></td>
                                <td><?=form_input(array(
                                    'name' => "related_term[$index]",
                                    'value' => $rel->relatedTerm,
                                    'class' => 'form-control',
                                    'disabled' => 'disabled'
                                ))?></td>
                                <td><button class="rel-delete btn btn-default"><i class="fa fa-trash-o fa-lg"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif;?>
                            <tr>
                                <td><?=form_dropdown('rel_type[' . $numrel .']', $options, FALSE, 'class="form-control"')?></td>
                                <td><?=form_input(array(
                                    'name' => 'related_term[' . $numrel .']',
                                    'class' => 'form-control'
                                ))?></td>
                                <td><button class="rel-add btn btn-default">Add row</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-right"><input type="submit" name="submit" value="Save" class="btn btn-default" />
                        <input type="submit" name="cancel" value="Cancel" class="btn btn-default"/> </p>
                </div>
            </div>
            
            
            <?=form_close()?>
        </div>
    </div>
</div>

<?php if (isset($term) && $term): ?>
<!-- Modal: New accepted name -->
<div class="modal" id="deleteGlossaryTermModal" tabindex="-1" role="dialog" aria-labelledby="deleteGlossaryTermModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteGlossaryTermModalLabel">Delete term</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p>You are about to delete: <b><?=$term->Name?></b>.</p>
                    <p>Are you sure?</p>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a href="<?=site_url()?>admin/delete_glossary_term/<?=$term->TermID?>" class="btn btn-primary">OK</a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>