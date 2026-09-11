                                <div class="page-rightheader d-flex ms-md-auto">

									<!-- <?php if($ticket->status == 'Closed'): ?>
										<button type="buttom" class="btn btn-sm btn-light me-2 d-none" id="ticket_to_article" value="">
											<i class="feather feather-book me-3 fs-18 my-auto text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Create Article')); ?>"></i>
											<span><?php echo e(lang('Ticket To Article')); ?> </span>
										</button>
										<a href="<?php echo e(route('admin.article.ticket', $ticket->ticket_id)); ?>" class="btn btn-sm btn-light me-2"  id="ticket_to_article">
											<i class="feather feather-book me-3 fs-18 my-auto text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Create Article')); ?>"></i>
											<span><?php echo e(lang('Ticket To Article')); ?> </span>
										</a>
									<?php endif; ?> -->

									<div class="btn-list">

										<div class="dropdown">
											<button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
												<i class="fe fe-more-vertical"></i>
											</button>
											<div class="dropdown-menu">

												<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" class="dropdown-item" id="show-delete">
													<i class="fa fa-trash me-3 fs-18 my-auto text-muted" data-id="<?php echo e($ticket->id); ?>"></i>
													<?php echo e(lang('Delete Ticket')); ?>

												</a>

												<?php if($ticket->cust->voilated != 'on'): ?>
													<a href="<?php echo e(route('voilating.customer',$ticket->cust->id)); ?>" class="dropdown-item">
														<i class="fa fa-exclamation-triangle me-3 fs-18 my-auto text-muted" data-id="<?php echo e($ticket->id); ?>"></i>
														<?php echo e(lang('Voilation')); ?>

													</a>
												<?php else: ?>
													<a href="<?php echo e(route('unvoilating.customer',$ticket->cust->id)); ?>" class="dropdown-item">
														<i class="fa fa-exclamation-triangle me-3 fs-18 my-auto text-muted" data-id="<?php echo e($ticket->id); ?>"></i>
														<?php echo e(lang('Un-Voilation')); ?>

													</a>
												<?php endif; ?>


												<?php if($ticket->status == 'Closed'): ?>

												<?php else: ?>

												<?php if($ticket->status == 'Suspend'): ?>
													<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" class="dropdown-item" id="unsuspend">
														<i class="fa fa-pause-circle me-3 fs-18 my-auto text-muted" data-id="<?php echo e($ticket->id); ?>"></i>
														<?php echo e(lang('Unsuspend Ticket')); ?>

													</a>
												<?php else: ?>
													<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" class="dropdown-item" id="suspend">
														<i class="fa fa-pause-circle me-3 fs-18 my-auto text-muted" data-id="<?php echo e($ticket->id); ?>"></i>
														<?php echo e(lang('Suspend Ticket')); ?>

													</a>
												<?php endif; ?>

												<?php endif; ?>

											</div>
										</div>
									</div>
								</div>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/viewticket/showticketdata/ticketpageheader.blade.php ENDPATH**/ ?>