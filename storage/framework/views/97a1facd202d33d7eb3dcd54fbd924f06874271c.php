<?php if($allowreply): ?>
<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend' ): ?>
    <div class="card" >
        <?php if($comments->isNotEmpty()): ?>
            <div class="panel-group1" id="accordion1">
                <div class="panel panel-default overflow-hidden br-7">
                    <div class="panel-heading1 panel-arrows">
                        <h4 class="panel-title1">
                        <a class="accordion-toggle collapsed bg-secondary" data-bs-toggle="collapse"
                            data-parent="#accordion" href="#collapseFour" aria-expanded="false">
                        <i class="feather feather-edit-2"></i>
                        <?php echo e(lang('Reply Ticket')); ?></a>
                        </h4>

                    </div>
                    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-expanded="false">
                        <div class="panel-body p-0">
                        <?php else: ?>
                            <div class="card-header  border-0 justify-content-between">
                                <h4 class="card-title"><?php echo e(lang('Reply Ticket')); ?></h4>
                                <?php if(setting('enable_gpt') == 'on' && $comments->isNotEmpty()): ?>
                                    <button class="btn btn-primary ms-auto" type="button" id="gptmodal" data-target="#exampleModal234"><?php echo e(lang('Ask Chat GPT')); ?></button>
                                <?php endif; ?>
                                <span id="replyStatus"></span>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo e(url('admin/ticket/'. $ticket->ticket_id)); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo view('honeypot::honeypotFormFields'); ?>
                            <input type="hidden" name="ticket_id" value="<?php echo e($ticket->id); ?>">
                            <div class="card-body status">
                                <div class="col-md-12 col-sm-12 ps-0 ps-lg-1 pb-5 pe-0">
                                    <div class="d-lg-flex justify-content-between">
                                        <div class="d-flex flex-fill flex-wrap align-items-center me-sm-5">
                                            <label class="form-label me-2"><?php echo e(lang('Canned Response')); ?></label>
                                            <div class="flex-1 mb-2 mb-lg-0">
                                                <select name="cannedmessage" id="cannedmessagess" class="cannedmessage select2 form-control mw"  data-placeholder="<?php echo e(lang('Select Canned Messages')); ?>">
                                                    <option value="" label="Select  Canned Messages"></option>
                                                    <?php $__currentLoopData = $cannedmessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cannedmessage=>$cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($cannedmessage); ?>"><?php echo e($cm->title); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if(setting('enable_gpt') == 'on'): ?>
                                            <button class="btn btn-primary ms-auto" type="button" id="gptmodal" data-target="#exampleModal234"><?php echo e(lang('Ask Chat GPT')); ?></button>
                                        <?php endif; ?>
                                        <span id="replyStatus"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea class="summernote form-control <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="6" cols="100" name="comment" id="summernoteempty" aria-multiline="true"><?php echo e(old('comment')); ?></textarea>
                                    <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                    <strong><?php echo e(lang($message)); ?></strong>
                                    </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(lang('Upload File', 'filesetting')); ?></label>
                                    <div class="file-browser">
                                    <div class="needsclick dropzone" id="document-dropzone"></div>
                                    </div>
                                    <small class="text-muted"><i><?php echo e(lang('The file size should not be more than', 'filesetting')); ?> <?php echo e(setting('FILE_UPLOAD_MAX')); ?><?php echo e(lang('MB', 'filesetting')); ?></i></small>
                                </div>
                                <div class="form-group">
                                    <div class="custom-controls-stacked d-md-flex" id="text">
                                        <label class="form-label mt-1 me-5"><?php echo e(lang('Status')); ?></label>
                                        <label class="custom-control form-radio success me-4">
                                        <?php if($ticket->status == 'Re-Open'): ?>
                                        <input type="radio" class="custom-control-input hold sprukostatuschange" name="status"  id="Inprogress1" value="Inprogress"
                                        <?php echo e($ticket->status == 'Re-Open' ? 'checked' : ''); ?> >
                                        <span class="custom-control-label"><?php echo e(lang('Inprogress')); ?></span>
                                        <?php elseif($ticket->status == 'Inprogress'): ?>
                                        <input type="radio" class="custom-control-input hold sprukostatuschange" name="status"  id="Inprogress2" value="<?php echo e($ticket->status); ?>"
                                        <?php echo e($ticket->status == 'Inprogress' ? 'checked' : ''); ?> >
                                        <span class="custom-control-label"><?php echo e(lang('Inprogress')); ?></span>
                                        <?php else: ?>
                                        <input type="radio" class="custom-control-input hold sprukostatuschange" name="status" id="Inprogress3" value="Inprogress"
                                        <?php echo e($ticket->status == 'New' ? 'checked' : ''); ?> >
                                        <span class="custom-control-label"><?php echo e(lang('Inprogress')); ?></span>
                                        <?php endif; ?>
                                        </label>
                                        <label class="custom-control form-radio success me-4">
                                        <input type="radio" class="custom-control-input hold sprukostatuschange" name="status" id="closed" value="Solved" >
                                        <span class="custom-control-label"><?php echo e(lang('Solved')); ?></span>
                                        </label>
                                        <label class="custom-control form-radio success me-4">
                                        <input type="radio" class="custom-control-input sprukostatuschange" name="status" id="onhold" value="On-Hold" <?php if(old('status') == 'On-Hold'): ?> checked <?php endif; ?> <?php echo e($ticket->status == 'On-Hold' ? 'checked' : ''); ?>>
                                        <span class="custom-control-label"><?php echo e(lang('On-Hold')); ?></span>
                                        </label>
                                    </div>
                                    <?php if(setting('ticketrating') == 'off'): ?>
                                        <div class="switch_section d-none" id="ratingonoff">
                                            <div class="d-flex d-md-max-block mt-4 ms-0">
                                                <a class="onoffswitch2">
                                                    <input type="checkbox" name="rating_on_off" id="rating_on_off" class=" toggle-class onoffswitch2-checkbox sprukoregister" value="yes" <?php if($ticket->rating_on_off == 'on'): ?> checked="" <?php endif; ?>>
                                                    <label for="rating_on_off" class="toggle-class onoffswitch2-label" ></label>
                                                </a>
                                                <label class="form-label ps-3 ps-md-max-0"><?php echo e(lang('Rating page to customer')); ?></label>
                                                <small class="text-muted ps-2 ps-md-max-0"><i>(<?php echo e(lang('If you Enable this switch, you stop rating page to the customer')); ?>)</i></small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer d-flex">
                                <input type="submit" class="btn btn-secondary deletelocalstorage ms-auto" id="btnsprukodisable" value="<?php echo e(lang('Reply Ticket')); ?>" onclick="this.disabled=true;this.form.submit();">
                            </div>
                        </form>
                        <?php if($comments->isNotEmpty()): ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/viewticket/showticketdata/showticketinclude.blade.php ENDPATH**/ ?>