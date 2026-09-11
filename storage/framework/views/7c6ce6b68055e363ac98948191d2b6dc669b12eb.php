		<?php $__env->startSection('styles'); ?>

		<!-- INTERNAL Data table css -->
		<link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- INTERNAL Sweet-Alert css -->
		<link href="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- INTERNAL Multiselect css -->
		<link href="<?php echo e(asset('assets/plugins/multipleselect/multiple-select.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/plugins/multi/multi.min.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<?php $__env->stopSection(); ?>

							<?php $__env->startSection('content'); ?>

							<!--Page header-->
							<div class="page-header d-xl-flex d-block">
								<div class="page-leftheader">
									<h4 class="page-title"><span class="font-weight-normal text-muted ms-2"><?php echo e(lang('Projects', 'menu')); ?></span></h4>
								</div>
							</div>
							<!--End Page header-->

							<!-- Project List-->
							<div class="col-xl-12 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-header border-0 d-md-max-block">
										<h4 class="card-title mb-md-max-2"><?php echo e(lang('Projects', 'menu')); ?></h4>
										<div class="card-options d-md-max-block">
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Project Create')): ?>

											<a href="javascript:void(0)" class="btn btn-success me-3 mb-md-max-2 mw-10" id="create-new-projects"><?php echo e(lang('Add Project')); ?></a>
											<?php endif; ?>
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Project Assign')): ?>

											<a href="javascript:void(0)" class="btn btn-danger me-3  mb-md-max-2 mw-10" id="projectsassign"><?php echo e(lang('Assign Projects')); ?></a>
											<?php endif; ?>
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Project Importlist')): ?>

											<a href="<?php echo e(route('projects.pcsvimports')); ?>" class="btn btn-info me-3  mb-md-max-2 mw-10" id="importfile"><i class="feather feather-download"></i> <?php echo e(lang('Import file')); ?></a>
											<?php endif; ?>

										</div>
									</div>
									<div class="card-body" >
										<div class="table-responsive spruko-delete">
											<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Project Delete')): ?>

											<button id="massdelete" class="btn btn-outline-light btn-sm mb-4 data-table-btn"><i class="fe fe-trash"></i> <?php echo e(lang('Delete')); ?></button>
											<?php endif; ?>

											<table class="table table-bordered border-bottom text-nowrap ticketdeleterow w-100" id="support-articlelists">
												<thead>
													<tr>
														<th  width="10"><?php echo e(lang('Sl.No')); ?></th>
														<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Project Delete')): ?>

														<th width="10" >
															<input type="checkbox"  id="customCheckAll">
															<label  for="customCheckAll"></label>
														</th>
														<?php endif; ?>
														<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('Project Delete')): ?>

														<th width="10" >
															<input type="checkbox"  id="customCheckAll" disabled>
															<label  for="customCheckAll"></label>
														</th>
														<?php endif; ?>

														<th ><?php echo e(lang('Name')); ?></th>
														<th ><?php echo e(lang('Actions')); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $i = 1; ?>
													<?php $__currentLoopData = $projectss; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projectsss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

														<tr>
															<td><?php echo e($i++); ?></td>
															<td>
																<?php if(Auth::user()->can('Project Delete')): ?>
																	<input type="checkbox" name="project_checkbox[]" class="checkall" value="<?php echo e($projectsss->id); ?>" />
																<?php else: ?>
																	<input type="checkbox" name="project_checkbox[]" class="checkall" value="<?php echo e($projectsss->id); ?>" disabled />
																<?php endif; ?>
															</td>
															<td>
																<?php echo e(Str::limit( $projectsss->name, '40')); ?>

															</td>
															<td>
																<div class = "d-flex">
																	<?php if(Auth::user()->can('Project Edit')): ?>

																		<a href="javascript:void(0)" data-id="<?php echo e($projectsss->id); ?>" class="action-btns1 edit-testimonial">
																			<i class="feather feather-edit text-primary" data-id="<?php echo e($projectsss->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Edit')); ?>"></i>
																		</a>
																	<?php else: ?>
																		~
																	<?php endif; ?>
																	<?php if(Auth::user()->can('Project Delete')): ?>

																		<a href="javascript:void(0)" data-id="<?php echo e($projectsss->id); ?>" class="action-btns1" id="delete-testimonial" >
																			<i class="feather feather-trash-2 text-danger" data-id="<?php echo e($projectsss->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Delete')); ?>"></i>
																		</a>

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
							<!-- End Project List-->

							<?php $__env->stopSection(); ?>
	<?php $__env->startSection('modal'); ?>

    <?php echo $__env->make('admin.projects.model', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


      		<!-- Add Projectz Assign-->
            <div class="modal fade"  id="projectsassigned" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" ></h5>
                            <button  class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form method="POST" enctype="multipart/form-data"  action="<?php echo e(url('admin/projectsassigned')); ?>">
                            <input type="hidden" name="projects_id" id="projects_id">
                            <?php echo csrf_field(); ?>
                            <?php echo view('honeypot::honeypotFormFields'); ?>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label"><?php echo e(lang('Select Category')); ?> </label>
                                    <div class="custom-controls-stacked d-md-flex" >
                                        <select multiple="multiple" class="form-control select2_modal"  name="category_id[]" data-placeholder="<?php echo e(lang('Select Category')); ?>" id="category" >
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

											<option value="<?php echo e($category->id); ?>" <?php echo e(in_array($category->id,$check_category) ? 'selected':''); ?>><?php echo e($category->name); ?></option>

											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="form-label"><?php echo e(lang('Projects', 'menu')); ?></label>
                                    <div class="custom-controls-stacked d-md-flex" id="projectdisable">
                                        <select multiple="multiple" class="filter-multi"  id="projects"  name="projected[]" >
                                            <?php $__currentLoopData = $project; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <option value="<?php echo e($item->id); ?>" selected=""><?php echo e(lang($item->name)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-outline-danger" data-bs-dismiss="modal"><?php echo e(lang('Close')); ?></a>
                                <button type="submit" class="btn btn-secondary" id="btnsave"  ><?php echo e(lang('Save')); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End  Add Projectz Assign  -->

			<!-- INTERNAL Multiselect Js -->
            <script src="<?php echo e(asset('assets/plugins/multipleselect/multiple-select.js')); ?>?v=<?php echo time(); ?>"></script>
            <script src="<?php echo e(asset('assets/plugins/multipleselect/multi-select.js')); ?>?v=<?php echo time(); ?>"></script>
	<?php $__env->stopSection(); ?>

		<?php $__env->startSection('scripts'); ?>


		<!-- INTERNAL Vertical-scroll js-->
		<script src="<?php echo e(asset('assets/plugins/vertical-scroll/jquery.bootstrap.newsbox.js')); ?>?v=<?php echo time(); ?>"></script>

		<!-- INTERNAL Data tables -->
		<script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/dataTables.responsive.min.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.min.js')); ?>?v=<?php echo time(); ?>"></script>
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

				// Datatable
				// $('#support-articlelists').DataTable({

				// 	responsive: true,
				// 	language: {
				// 		searchPlaceholder: search,
				// 		scrollX: "100%",
				// 		sSearch: '',
				// 	},
				// 	order:[],
				// 	columnDefs: [
				// 		{ "orderable": false, "targets":[ 0,1,3] }
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
                $('#support-articlelists').DataTable({
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
                        { "orderable": false, "targets":[ 0,1,3] }
                    ],
                });


				/*  When user click add project button */
				$('#create-new-projects').on('click', function () {
					$('#btnsave').val("create-product");
					$('#projects_id').val('');
					$('#projects_form').trigger("reset");
					$('.modal-title').html("<?php echo e(lang('Add New Project')); ?>");
					$('#addtestimonial').modal('show');
				});


				/* When click edit project */
				$('body').on('click', '.edit-testimonial', function () {
					var projects_id = $(this).data('id');
					$.get('projects/' + projects_id , function (data) {
						$('#nameError').html('');
						$('.modal-title').html("<?php echo e(lang('Edit Project')); ?>");
						$('#btnsave').val("edit-project");
						$('#addtestimonial').modal('show');
						$('#projects_id').val(data.id);
						$('#name').val(data.name);
					})
				});

				// Delete Project
				$('body').on('click', '#delete-testimonial', function () {
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
									url: SITEURL + "/admin/projects/delete/"+_id,
									success: function (data) {
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

				//projects assign
				$('#projectsassign').on('click', function () {

					document.getElementById('projectdisable').style.pointerEvents = 'none';
					document.getElementById('projectdisable').style.opacity = '0.6';

					$('.select2_modal').select2({
						minimumResultsForSearch: '',
						placeholder: "Search",
						width: '100%'
					});

					$('#btnsave').val("create-project");
					$('#projects_id').val('');
					$('#projects_form').trigger("reset");
					$('.modal-title').html("<?php echo e(lang('Assign Projects')); ?>");
					$('#projectsassigned').modal('show');
					$('#projects').hide();
					$.get('projects/' , function (data) {

					});

				});

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
									url:"<?php echo e(url('admin/massproject/delete')); ?>",
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

				// Project submit button
				$('body').on('submit', '#projects_form', function (e) {
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
						url: SITEURL + "/admin/projects/create",
						data: formData,
						cache:false,
						contentType: false,
						processData: false,
						success: (data) => {

							if(data.errors){
								$('#nameError').html('');
								$('#nameError').html(data.errors.name);
								$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
							}
							if(data.success){
								$('#nameError').html('');
								$('#projects_form').trigger("reset");
								$('#addtestimonial').modal('hide');
								$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
								toastr.success(data.success);
								location.reload();
							}
						},
						error: function(data){
							$('#nameError').html('');
							toastr.error('<?php echo e(lang('Project Name is Already Exists', 'alerts')); ?>');
							$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
						}
					});


				});

				//Checkbox checkall
				$('#customCheckAll').on('click', function() {
					$('.checkall').prop('checked', this.checked);
				});

				$('.form-select').select2({
					minimumResultsForSearch: Infinity,
					width: '100%'
				});
				$('#customCheckAll').prop('checked', false);
				$('.checkall').on('click', function(){
					if($('.checkall:checked').length == $('.checkall').length){
						$('#customCheckAll').prop('checked', true);
					}else{
						$('#customCheckAll').prop('checked', false);
					}
				});

			})(jQuery);


		</script>

		<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/projects/index.blade.php ENDPATH**/ ?>