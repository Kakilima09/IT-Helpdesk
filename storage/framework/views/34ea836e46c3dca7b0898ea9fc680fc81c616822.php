		<?php $__env->startSection('styles'); ?>

		<!-- INTERNAL Data table css -->
		<link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- INTERNAL Sweet-Alert css -->
		<link href="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<?php $__env->stopSection(); ?>

							<?php $__env->startSection('content'); ?>

							<!--Page header-->
							<div class="page-header d-xl-flex d-block">
								<div class="page-leftheader">
									<h4 class="page-title"><span class="font-weight-normal text-muted ms-2"><?php echo e(lang('Self Assigned Tickets', 'menu')); ?></span></h4>
								</div>
							</div>
							<!--End Page header-->

							<div class="col-xl-12 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-header border-0">
										<h4 class="card-title"><?php echo e(lang('Self Assigned Tickets', 'menu')); ?></h4>
									</div>
									<div class="card-body overflow-scroll" >
										<div class="spruko-delete">
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Delete')): ?>

											<button id="massdelete" class="btn btn-outline-light btn-sm mb-4 ticketdeleterow data-table-btn"><i class="fe fe-trash"></i> <?php echo e(lang('Delete')); ?></button>
											<?php endif; ?>
											<div class=" ">

												<table class="table table-bordered border-bottom text-nowrap w-100" id="overduetickets">
													<thead >
														<tr >
															<th ><?php echo e(lang('Sl.No')); ?></th>
															<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Delete')): ?>

															<th width="10" >
																<input type="checkbox"  id="customCheckAll">
																<label  for="customCheckAll"></label>
															</th>
															<?php endif; ?>
															<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('Ticket Delete')): ?>

															<th width="10" >
																<input type="checkbox"  id="customCheckAll" disabled>
																<label  for="customCheckAll"></label>
															</th>
															<?php endif; ?>

															<th ><?php echo e(lang('Ticket Details')); ?></th>
															<th><?php echo e(lang('User')); ?></th>
															<th><?php echo e(lang('Status')); ?></th>
															<th ><?php echo e(lang('Assign To')); ?></th>
															<th ><?php echo e(lang('Actions')); ?></th>
														</tr>
													</thead>
													<tbody id="refresh">
														<?php $i = 1; ?>
														<?php $__currentLoopData = $selfassignedtickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tickets): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

														<tr <?php echo e($tickets->replystatus == 'Replied'? 'class=bg-success-transparent': ''); ?>>
															<td class="wpx-40 text-center">
																<?php echo e($i++); ?>

															</td>
															<td class="wpx-40 text-center">
																<?php if(Auth::user()->can('Ticket Delete')): ?>
																	<input type="checkbox" name="student_checkbox[]" class="checkall" value="<?php echo e($tickets->id); ?>" />
																<?php else: ?>
																	<input type="checkbox" name="student_checkbox[]" class="checkall" value="<?php echo e($tickets->id); ?>" disabled />
																<?php endif; ?>
															</td>
															<td class="overflow-hidden ticket-details">
																<div class="d-flex align-items-center">
																	<div class="">
																		<?php if($tickets->ticketnote->isEmpty()): ?>
																			<?php if($tickets->overduestatus != null): ?>

																			<div class="ribbon ribbon-top-right1 text-danger">
																				<span class="bg-danger text-white"><?php echo e($tickets->overduestatus); ?></span>
																			</div>

																			<?php endif; ?>
																		<?php else: ?>

																			<div class="ribbon ribbon-top-right text-warning">
																				<span class="bg-warning text-white"><?php echo e(lang('Note')); ?></span>
																			</div>

																			<?php if($tickets->overduestatus != null): ?>
																			<div class="ribbon ribbon-top-right1 text-danger">
																				<span class="bg-danger text-white"><?php echo e($tickets->overduestatus); ?></span>
																			</div>
																			<?php endif; ?>

																		<?php endif; ?>

																		<a href="<?php echo e(url('admin/ticket-view/' . $tickets->ticket_id)); ?>" class="fs-14 d-block font-weight-semibold"><?php echo e($tickets->subject); ?></a>

																		<ul class="fs-13 font-weight-normal d-flex custom-ul">
																			<li class="pe-2 text-muted">#<?php echo e($tickets->ticket_id); ?></span>
																			<li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Date')); ?>"><i class="fe fe-calendar me-1 fs-14"></i> <?php echo e($tickets->created_at->timezone(Auth::user()->timezone)->format(setting('date_format'))); ?></li>

																			<?php if($tickets->priority != null): ?>
																				<?php if($tickets->priority == "Low"): ?>
																					<li class="ps-5 pe-2 preference preference-low" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

																				<?php elseif($tickets->priority == "High"): ?>
																					<li class="ps-5 pe-2 preference preference-high" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

																				<?php elseif($tickets->priority == "Critical"): ?>
																					<li class="ps-5 pe-2 preference preference-critical" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

																				<?php else: ?>
																					<li class="ps-5 pe-2 preference preference-medium" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>
																				<?php endif; ?>
																			<?php else: ?>
																				~
																			<?php endif; ?>

																			<?php if($tickets->category_id != null): ?>
																				<?php if($tickets->category != null): ?>

																				<li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Category')); ?>"><i class="fe fe-grid me-1 fs-14" ></i><?php echo e(Str::limit($tickets->category->name, '40')); ?></li>

																				<?php else: ?>

																				~
																				<?php endif; ?>
																			<?php else: ?>

																				~
																			<?php endif; ?>

																			<?php if($tickets->last_reply == null): ?>
																				<li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Last Replied')); ?>"><i class="fe fe-clock me-1 fs-14"></i><?php echo e($tickets->created_at->diffForHumans()); ?></li>

																			<?php else: ?>
																			<li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Last Replied')); ?>"><i class="fe fe-clock me-1 fs-14"></i><?php echo e($tickets->last_reply->diffForHumans()); ?></li>

																			<?php endif; ?>

																			<?php if($tickets->purchasecodesupport != null): ?>
																			<?php if($tickets->purchasecodesupport == 'Supported'): ?>

																			<li class="px-2 text-success font-weight-semibold"><?php echo e(lang('Support Active')); ?></li>
																			<?php endif; ?>
																			<?php if($tickets->purchasecodesupport == 'Expired'): ?>

																			<li class="px-2 text-danger-dark font-weight-semibold"><?php echo e(lang('Support Expired')); ?></li>
																			<?php endif; ?>
																			<?php endif; ?>

																		</ul>
																	</div>
																</div>
															</td>
															<td>
																<?php echo e($tickets->cust->username); ?>

															</td>
															<td>
																<?php if($tickets->status == "New"): ?>

																<span class="badge badge-burnt-orange"><?php echo e(lang($tickets->status)); ?></span>

																<?php elseif($tickets->status == "Re-Open"): ?>

																<span class="badge badge-teal"><?php echo e(lang($tickets->status)); ?></span>

																<?php elseif($tickets->status == "Inprogress"): ?>

																<span class="badge badge-info"><?php echo e(lang($tickets->status)); ?></span>

																<?php elseif($tickets->status == "On-Hold"): ?>

																<span class="badge badge-warning"><?php echo e(lang($tickets->status)); ?></span>

																<?php else: ?>

																<span class="badge badge-danger"><?php echo e(lang($tickets->status)); ?></span>

																<?php endif; ?>
															</td>
															<td>
																<?php if(Auth::user()->can('Ticket Assign')): ?>
																	<?php if($tickets->status == 'Suspend' || $tickets->status == 'Closed'): ?>
																		<div class="btn-group">
																			<?php if($tickets->ticketassignmutliples->isNotEmpty() && $tickets->selfassignuser_id == null): ?>

																			<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" disabled><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
																			<button data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
																			<?php elseif($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id != null): ?>

																			<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e($tickets->selfassign->name); ?> (self) <span class="caret"></span></button>
																			<button data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
																			<?php else: ?>

																			<button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e(lang('Assign')); ?><span class="caret"></span></button>
																			<?php endif; ?>

																		</div>
																	<?php else: ?>
																		<?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id == null): ?>

																			<div class="btn-group">
																				<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
																				<ul class="dropdown-menu" role="menu">
																					<li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
																					<li>
																						<a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($tickets->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
																					</li>
																					<li>
																						<a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" id="assigned">
																						<?php echo e(lang('Other Assign')); ?>

																						</a>
																					</li>
																				</ul>
																			</div>
																		<?php else: ?>
																			<div class="btn-group">
																				<?php if($tickets->ticketassignmutliples->isNotEmpty() && $tickets->selfassignuser_id == null): ?>
																					<?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassign == null): ?>
																					<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
																					<?php else: ?>
																					<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
																					<a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
																					<?php endif; ?>

																				<?php elseif($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id != null): ?>
																				<?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassign == null): ?>
																				<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
																				<?php else: ?>
																				<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e($tickets->selfassign->name); ?> (self) <span class="caret"></span></button>
																				<a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
																				<?php endif; ?>
																				<?php else: ?>

																				<button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
																				<?php endif; ?>

																				<ul class="dropdown-menu" role="menu">
																					<li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
																					<li>
																						<a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($tickets->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
																					</li>
																					<li>
																						<a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" id="assigned">
																						<?php echo e(lang('Other Assign')); ?>

																						</a>
																					</li>
																				</ul>
																			</div>

																		<?php endif; ?>
																	<?php endif; ?>
																<?php else: ?>
																	~
																<?php endif; ?>
															</td>
															<td>
																<?php if(Auth::user()->can('Ticket Edit')): ?>

																<a href="<?php echo e(url('admin/ticket-view/' . $tickets->ticket_id)); ?>" class="btn btn-sm action-btns edit-testimonial"><i class="feather feather-eye text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Edit')); ?>"></i></a>
																<?php else: ?>
																	~
																<?php endif; ?>
																<?php if(Auth::user()->can('Ticket Delete')): ?>
																<a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-sm action-btns" id="show-delete" ><i class="feather feather-trash-2 text-danger" data-id="<?php echo e($tickets->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Delete')); ?>"></i></a>
																<?php else: ?>
																~
																<?php endif; ?>
															</td>
														</tr>
														<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php $__env->stopSection(); ?>


		<?php $__env->startSection('scripts'); ?>


		<!-- INTERNAL Data tables -->
		<script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/dataTables.responsive.min.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.min.js')); ?>?v=<?php echo time(); ?>"></script>


		<!-- INTERNAL Index js-->
		<script src="<?php echo e(asset('assets/js/support/support-sidemenu.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/js/support/support-admindash.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/js/select2.js')); ?>?v=<?php echo time(); ?>"></script>

		<!-- INTERNAL Sweet-Alert js-->
		<script src="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.min.js')); ?>?v=<?php echo time(); ?>"></script>

		<script type="text/javascript">

			"use strict";

			(function($)  {

				// Variables
				var SITEURL = '<?php echo e(url('')); ?>';

				// Csrf Field
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				// DataTable
				// $('#overduetickets').DataTable({
				// 	language: {
				// 		searchPlaceholder: search,

				// 		sSearch: '',
				// 	},
				// 	order:[],
				// 	columnDefs: [
				// 		{ "orderable": false, "targets":[ 0,1,6] }
				// 	],
				// });

                let prev = <?php echo json_encode(lang("Previous")); ?>;
                let next = <?php echo json_encode(lang("Next")); ?>;
                let nodata = <?php echo json_encode(lang("No data available in table")); ?>;
                let noentries = <?php echo json_encode(lang("No entries to show")); ?>;
                let showing = <?php echo json_encode(lang("showing page")); ?>;
                let ofval = <?php echo json_encode(lang("of")); ?>;
                let maxRecordfilter = <?php echo json_encode(lang("- filtered from ")); ?>;
                let maxRecords = <?php echo json_encode(lang("records")); ?>;
                let entries = <?php echo json_encode(lang("entries")); ?>;
                let show = <?php echo json_encode(lang("Show")); ?>;
                let search = <?php echo json_encode(lang("Search...")); ?>;
                // Datatable
                $('#overduetickets').dataTable({
                    language: {
                        searchPlaceholder: search,
                        scrollX: "100%",
                        sSearch: '',
                        paginate: {
                        previous: prev,
                        next: next
                        },
                        emptyTable : nodata,
                        infoFiltered: `${maxRecordfilter} _MAX_ ${maxRecords}`,
                        info: `${showing} _PAGE_ ${ofval} _PAGES_`,
                        infoEmpty: noentries,
                        lengthMenu: `${show} _MENU_ ${entries} `,
                    },
                    order:[],
                    columnDefs: [
                        { "orderable": false, "targets":[ 0,1,4] }
                    ],
                });

				$('.form-select').select2({
					minimumResultsForSearch: Infinity,
					width: '100%'
				});

				// TICKET DELETE SCRIPT
				$('body').on('click', '#show-delete', function () {
					var _id = $(this).data("id");
					swal({
						title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
						text: "<?php echo e(lang('This might erase your records permanently', 'alerts')); ?>",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							$.ajax({
								type: "get",
								url: SITEURL + "/admin/delete-ticket/"+_id,
								success: function (data) {
									toastr.success(data.success);
									var oTable = $('#overduetickets').dataTable();
									oTable.fnDraw(false);
									location.reload();
								},
								error: function (data) {
									console.log('Error:', data);
								}
							});
						}
					});

				});
				// TICKET DELETE SCRIPT END

				//Mass Delete
				$('body').on('click', '#massdelete', function () {
					var id = [];
					$('.checkall:checked').each(function(){
						id.push($(this).val());
					});
					if(id.length > 0){
						swal({
							title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
							text: "<?php echo e(lang('This might erase your records permanently', 'alerts')); ?>",
							icon: "warning",
							buttons: true,
							dangerMode: true,
						})
						.then((willDelete) => {
							if (willDelete) {
								$.ajax({
									url:"<?php echo e(url('admin/ticket/delete/tickets')); ?>",
									method:"GET",
									data:{id:id},
									success:function(data)
									{
										toastr.success(data.success);
										location.reload();
									},
									error:function(data){

									}
								});
							}
						});
					}else{
						toastr.error('<?php echo e(lang('Please select at least one check box.', 'alerts')); ?>');
					}
				});

				// when user click its get modal popup to assigned the ticket
				$('body').on('click', '#assigned', function () {
					var assigned_id = $(this).data('id');

					$('.select2_modalassign').select2({
						dropdownParent: ".sprukosearch",
						minimumResultsForSearch: '',
						placeholder: "Search",
						width: '100%'
					});

					$.get('assigned/' + assigned_id , function (data) {
						$('#AssignError').html('');
						$('#assigned_id').val(data.assign_data.id);
						$(".modal-title").text('<?php echo e(lang('Assign To Agent')); ?>');
						$('#username').html(data.table_data);
						$('#addassigned').modal('show');
					});
				});

				// Assigned Submit button
				$('body').on('submit', '#assigned_form', function (e) {
					e.preventDefault();
					var actionType = $('#btnsave').val();
					var fewSeconds = 2;
					$('#btnsave').html('Sending..');
					$('#btnsave').prop('disabled', true);
						setTimeout(function(){
							$('#btnsave').prop('disabled', false);
						}, fewSeconds*1000);
					var formData = new FormData(this);

					$.ajax({
						type:'POST',
						url: SITEURL + "/admin/assigned/create",
						data: formData,
						cache:false,
						contentType: false,
						processData: false,
						success: (data) => {
							$('#AssignError').html('');
							$('#assigned_form').trigger("reset");
							$('#addassigned').modal('hide');
							$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
							var oTable = $('#overduetickets').dataTable();
							oTable.fnDraw(false);
							toastr.success(data.success);
							location.reload();
						},
						error: function(data){
							$('#AssignError').html('');
							$('#AssignError').html(data.responseJSON.errors.assigned_user_id);
							$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
						}
					});
				});

				// Remove the assigned from the ticket
				$('body').on('click', '#btnremove', function () {
					var asid = $(this).data("id");
					swal({
						title: `<?php echo e(lang('Are you sure you want to unassign this agent?', 'alerts')); ?>`,
						text: "<?php echo e(lang('This agent may no longer exist for this ticket.', 'alerts')); ?>",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {

							$.ajax({
								type: "get",
								url: SITEURL + "/admin/assigned/update/"+asid,
								success: function (data) {
								var oTable = $('#overduetickets').dataTable();
								oTable.fnDraw(false);
								toastr.success(data.success);
								location.reload();
								},
								error: function (data) {
								console.log('Error:', data);
								}
								});

						}
					});



				});

				// Checkbox checkall
				$('#customCheckAll').on('click', function() {
					$('.checkall').prop('checked', this.checked);
				});
				$('body').on('click','#selfassigid', function(e){

					e.preventDefault();

					let id = $(this).data('id');

					$.ajax({
						method:'POST',
						url: '<?php echo e(route('admin.selfassign')); ?>',
						data: {
							id : id,
						},
						success: (data) => {
							toastr.success(data.success);
							location.reload();
						},
						error: function(data){

						}
					});
				})

			})(jQuery);

		</script>

		<?php $__env->stopSection(); ?>

			<?php $__env->startSection('modal'); ?>

			<?php echo $__env->make('admin.modalpopup.assignmodal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/superadmindashboard/mytickets/selfassignticket.blade.php ENDPATH**/ ?>