<div class="card">
	<div class="card-header  border-0">
		<div class="card-title"><?php echo e(lang('Ticket Information')); ?></div>
	</div>
	<div class="card-body pt-2 ps-0 pe-0 pb-0">
		<div class="table-responsive tr-lastchild">
			<table class="table mb-0 table-information">
				<tbody>

					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Ticket ID')); ?></span>
						</td>
						<td>:</td>
						<td>
							<span class="font-weight-semibold">#<?php echo e($ticket->ticket_id); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Category')); ?></span>
						</td>
						<td>:</td>
						<td>

							<?php if($ticket->category_id != null): ?>
								<?php if($ticket->category != null): ?>

								    <span class="font-weight-semibold"><?php echo e($ticket->category->name); ?></span>
									<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>

										<a href="javascript:void(0)" data-id="<?php echo e($ticket->ticket_id); ?>" class="p-1 sprukocategory border border-primary br-7 text-white bg-primary ms-2"> <i class="feather feather-edit-2" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Change Category')); ?>"></i></a>


									<?php endif; ?>
								<?php else: ?>
									<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
									<a href="javascript:void(0)" data-id="<?php echo e($ticket->ticket_id); ?>" class="p-1 border border-primary br-7 text-white bg-primary" > <i class="feather feather-plus" data-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Add Category')); ?>"></i></a>
									<?php endif; ?>
								<?php endif; ?>
							<?php else: ?>
								<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
									<a href="javascript:void(0)" data-id="<?php echo e($ticket->ticket_id); ?>" class="p-1 border border-primary br-7 text-white bg-primary" > <i class="feather feather-plus" data-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Add Category')); ?>"></i></a>
								<?php endif; ?>
							<?php endif; ?>

						</td>
					</tr>
					<?php if($ticket->subcategory != null): ?>
					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('SubCategory')); ?></span>
						</td>
						<td>:</td>
						<td>
							<span class="font-weight-semibold"><?php echo e($ticket->subcategoriess->subcategoryname); ?></span>

						</td>
					</tr>
					<?php endif; ?>

					<?php if($ticket->project != null): ?>

					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Project')); ?></span>
						</td>
						<td>:</td>
						<td>
							<span class="font-weight-semibold"><?php echo e($ticket->project); ?></span>
						</td>
					</tr>
					<?php endif; ?>
					<?php if($ticket->priority != null): ?>
					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Priority')); ?></span>
						</td>
						<td>:</td>
						<td id="priorityid">
							<?php if($ticket->priority == "Low"): ?>

								<span class="badge badge-success-light" ><?php echo e(lang($ticket->priority)); ?></span>
								<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
									<a href="javascript:void(0)"  id="priority" class="p-1 border border-primary br-7 text-white bg-primary ms-2">
										<i class="feather feather-edit-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Change priority" aria-label="Add priority"></i>
									</a>
								<?php endif; ?>
							<?php elseif($ticket->priority == "High"): ?>

								<span class="badge badge-danger-light"><?php echo e(lang($ticket->priority)); ?></span>
								<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
								<a href="javascript:void(0)"  id="priority" class="p-1 border border-primary br-7 text-white bg-primary ms-2">
									<i class="feather feather-edit-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Change priority" aria-label="Add priority"></i>
								</a>
								<?php endif; ?>
							<?php elseif($ticket->priority == "Critical"): ?>

								<span class="badge badge-danger-dark"><?php echo e(lang($ticket->priority)); ?></span>
								<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
									<a href="javascript:void(0)"  id="priority" class="p-1 border border-primary br-7 text-white bg-primary ms-2">
										<i class="feather feather-edit-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Change priority" aria-label="Add priority"></i>
									</a>
								<?php endif; ?>
							<?php else: ?>

								<span class="badge badge-warning-light"><?php echo e(lang($ticket->priority)); ?></span>
								<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
									<a href="javascript:void(0)"  id="priority" class="p-1 border border-primary br-7 text-white bg-primary ms-2">
										<i class="feather feather-edit-2" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Change priority" aria-label="Add priority"></i>
									</a>
								<?php endif; ?>
							<?php endif; ?>
						</td>
					</tr>
					<?php else: ?>

					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Priority')); ?></span>
						</td>
						<td>:</td>
						<td id="priorityid">
							<?php if($ticket->status != 'Closed' && $ticket->status != 'Suspend'): ?>
							<a href="javascript:void(0)"  id="priority" class="p-1 border border-primary br-7 text-white bg-primary">
								<i class="feather feather-plus" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="Change priority" aria-label="Add priority"></i>
							</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endif; ?>

					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Open Date')); ?></span>
						</td>
						<td>:</td>
						<td>
							<span class="font-weight-semibold"><?php echo e($ticket->created_at->timezone(Auth::user()->timezone)->format(setting('date_format'))); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Status')); ?></span>
						</td>
						<td>:</td>
						<td>
							<?php if($ticket->status == "New"): ?>

							<span class="badge badge-burnt-orange"><?php echo e(lang($ticket->status)); ?></span>
							<?php elseif($ticket->status == "Re-Open"): ?>

							<span class="badge badge-teal"><?php echo e(lang($ticket->status)); ?></span>
							<?php elseif($ticket->status == "Inprogress"): ?>

							<span class="badge badge-info"><?php echo e(lang($ticket->status)); ?></span>
							<?php elseif($ticket->status == "On-Hold"): ?>

							<span class="badge badge-warning"><?php echo e(lang($ticket->status)); ?></span>
							<?php else: ?>

							<span class="badge badge-danger"><?php echo e(lang($ticket->status)); ?></span>
							<?php endif; ?>

						</td>
					</tr>
					<?php if($ticket->replystatus != null && $ticket->replystatus == "Solved" || $ticket->replystatus == "Unanswered" || $ticket->replystatus == "Waiting for response"): ?>

					<tr>
						<td>
							<span class="w-50"><?php echo e(lang('Reply Status')); ?></span>
						</td>
						<td>:</td>
						<td>
							<?php if($ticket->replystatus == "Solved"): ?>

                                <span class="badge badge-success"><?php echo e(lang($ticket->replystatus)); ?></span>

							<?php elseif($ticket->replystatus == "Unanswered"): ?>

                                <span class="badge badge-danger-light"><?php echo e(lang($ticket->replystatus)); ?></span>

							<?php elseif($ticket->replystatus == "Waiting for response"): ?>

                                <span class="badge badge-warning"><?php echo e(lang($ticket->replystatus)); ?></span>

							<?php else: ?>
							<?php endif; ?>

						</td>
					</tr>
					<?php endif; ?>

					<?php $customfields = $ticket->ticket_customfield()->get(); ?>
					<?php if($customfields->isNotEmpty()): ?>
					<?php $__currentLoopData = $customfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customfield): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if($customfield->fieldtypes != 'textarea'): ?>
						<?php if($customfield->privacymode == '1'): ?>
							<?php
								$extrafiels = decrypt($customfield->values);
							?>
							<tr>
								<td><?php echo e($customfield->fieldnames); ?></td>
								<td>: </td>
								<td><?php echo e($extrafiels); ?> </td>
							</tr>
						<?php else: ?>

							<tr>
								<td><?php echo e($customfield->fieldnames); ?></td>
								<td>:</td>
								<td><?php echo e($customfield->values); ?> </td>
							</tr>

						<?php endif; ?>
					<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer ticket-buttons">
		<?php if($ticket->status == 'Closed'): ?>

			<button class="btn btn-secondary my-1" id="reopen" data-id="<?php echo e($ticket->id); ?>"> <i class="feather feather-rotate-ccw"></i> <?php echo e(lang('Re-Open')); ?></button>

		<?php endif; ?>
		<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Assign')): ?>
				<?php if($ticket->status == 'Suspend' || $ticket->status == 'Closed'): ?>
					<div class="btn-group">
						<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>

						<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" disabled><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
						<button data-id="<?php echo e($ticket->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
						<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>

						<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e($ticket->selfassign->name); ?> (self) <span class="caret"></span></button>
						<button data-id="<?php echo e($ticket->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
						<?php else: ?>

						<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e(lang('Assign')); ?><span class="caret"></span></button>
						<?php endif; ?>

					</div>
				<?php else: ?>
					<?php if($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id == null): ?>

						<div class="btn-group">
							<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
							<ul class="dropdown-menu" role="menu">
								<li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
								<li>
									<a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
								</li>
								<li>
									<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" id="assigned">
									<?php echo e(lang('Other Assign')); ?>

									</a>
								</li>
							</ul>
						</div>
					<?php else: ?>
						<div class="btn-group">
							<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>
								<?php if($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassign == null): ?>
								<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
								<?php else: ?>
								<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
								<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
								<?php endif; ?>

							<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>
							<?php if($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassign == null): ?>
							<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
							<?php else: ?>
							<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e($ticket->selfassign->name); ?> (self) <span class="caret"></span></button>
							<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
							<?php endif; ?>
							<?php else: ?>

							<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
							<?php endif; ?>

							<ul class="dropdown-menu" role="menu">
								<li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
								<li>
									<a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
								</li>
								<li>
									<a href="javascript:void(0)" data-id="<?php echo e($ticket->id); ?>" id="assigned">
									<?php echo e(lang('Other Assign')); ?>

									</a>
								</li>
							</ul>
						</div>

					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
	</div>
</div>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/viewticket/showticketdata/ticketinfooter.blade.php ENDPATH**/ ?>