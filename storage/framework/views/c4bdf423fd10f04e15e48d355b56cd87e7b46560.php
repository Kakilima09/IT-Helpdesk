


	<?php $__env->startSection('styles'); ?>

	<?php $__env->stopSection(); ?>


							<?php $__env->startSection('content'); ?>

							<!--Page header-->
							<div class="page-header d-xl-flex d-block">
								<div class="page-leftheader">
									<h4 class="page-title"><span class="font-weight-normal text-muted ms-2"><?php echo e(lang('Import Projects')); ?></span></h4>
								</div>
							</div>
							<!--End Page header-->

							<!-- Project Import Csv-->
							<div class="col-xl-12 col-lg-12 col-md-12">
								<div class="card ">
									<?php if(isset($errors) & $errors->any()): ?>

										<div class="alert alert-danger">
											<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

												<?php echo e($item); ?>

												
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
									<?php endif; ?>

									<div class="card-header border-0">
										<h4 class="card-title"><?php echo e(lang('Import Projects')); ?></h4>
									</div>
									<form method="POST" action="" enctype="multipart/form-data">
										<?php echo csrf_field(); ?>
										
										<?php echo view('honeypot::honeypotFormFields'); ?>
										<div class="card-body" >
											<div class="row">
												<div class="form-group">
													<label class="form-label"><?php echo e(lang('Upload File', 'filesetting')); ?> </label>
													<div class="input-group file-browser">
														<input class="form-control" name="file" type="file">
													</div>
													<small class="text-muted"><i><?php echo e(lang('File Format: .xlsx & .csv', 'filesetting')); ?></i></small>
													<p><?php echo e(lang('Download', 'filesetting')); ?> <a href="<?php echo e(asset('download/projectsample.csv')); ?>" class="text-primary" download><?php echo e(lang('Sample File', 'filesetting')); ?></a></p>
													<span id="nameError" class="text-danger alert-message"></span>
												</div>
											</div>
										</div>
										<div class="card-footer">
											<button type="submit" class="btn btn-secondary float-end mb-2"  > <?php echo e(lang('Import Data', 'filesetting')); ?></button>
										</div>
									</form>
								</div>
							</div>
							<!-- Project Import Csv -->
           
							<?php $__env->stopSection(); ?>

		<?php $__env->startSection('scripts'); ?>

		<!-- INTERNAL Vertical-scroll js-->
		<script src="<?php echo e(asset('assets/plugins/vertical-scroll/jquery.bootstrap.newsbox.js')); ?>?v=<?php echo time(); ?>"></script>

		<!-- INTERNAL Index js-->
		<script src="<?php echo e(asset('assets/js/support/support-sidemenu.js')); ?>?v=<?php echo time(); ?>"></script>      
		
		<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/projects/projectimport.blade.php ENDPATH**/ ?>