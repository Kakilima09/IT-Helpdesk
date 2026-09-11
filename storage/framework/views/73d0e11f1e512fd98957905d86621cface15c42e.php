<?php $__env->startSection('styles'); ?>

<!-- INTERNAl Summernote css -->
<link rel="stylesheet" href="<?php echo e(asset('assets/plugins/summernote/summernote.css')); ?>?v=<?php echo time(); ?>">

<!-- DropZone CSS -->
<link href="<?php echo e(asset('assets/plugins/dropzone/dropzone.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

<!-- galleryopen CSS -->
<link href="<?php echo e(asset('assets/plugins/simplelightbox/simplelightbox.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet">

<!-- INTERNAL Sweet-Alert css -->
<link href="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<!--Page header-->
<div class="page-header d-xl-flex d-block">
	<div class="page-leftheader">
		<h4 class="page-title"><span class="font-weight-normal text-muted ms-2"><?php echo e(lang('Ticket Information')); ?></span></h4>
	</div>

	<!--page header left -->
		<?php $array = $ticket->ticketassignmutliples()->pluck('toassignuser_id')->toArray(); ?>
		<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>
			<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
				<?php echo $__env->make('admin.viewticket.showticketdata.ticketpageheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php elseif(in_array(Auth::id(), $array)): ?>
				<?php echo $__env->make('admin.viewticket.showticketdata.ticketpageheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php else: ?>
			<?php endif; ?>
		<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>
			<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
				<?php echo $__env->make('admin.viewticket.showticketdata.ticketpageheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php elseif($ticket->selfassignuser_id == Auth::id()): ?>
				<?php echo $__env->make('admin.viewticket.showticketdata.ticketpageheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			<?php else: ?>
			<?php endif; ?>
		<?php else: ?>
			<?php echo $__env->make('admin.viewticket.showticketdata.ticketpageheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php endif; ?>
	<!--page header left -->
</div>
<!--End Page header-->

<!--Row-->
<div class="row">
	<div class="col-xl-12 col-md-12 col-lg-12">
		<div class="row">
			<div class="col-xl-9 col-lg-12 col-md-12">

				<?php if($ticket->purchasecode != null  ): ?>
                    <?php
                        $aaa = Str::length($ticket->purchasecode);
                    ?>
                    <?php if($aaa != 36 ): ?>
                        <?php if(decrypt($ticket->purchasecode) != 'undefined'): ?>
                            <!-- Purchase Code Details -->
                            <div class="purchasecodes alert alert-light-warning br-13 ">
                                <div class="ps-0 pe-0 pb-0">
                                    <div class="">
                                        <strong><?php echo e(lang('Puchase Code')); ?> :</strong>
                                        <?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>

                                            <span class=""><?php echo e(decrypt($ticket->purchasecode)); ?></span>
                                        <?php else: ?>
                                            <?php if(setting('purchasecode_on') == 'on'): ?>

                                            <span class=""><?php echo e(decrypt($ticket->purchasecode)); ?></span>
                                            <?php else: ?>

                                            <span class=""><?php echo e(Str::padLeft(Str::substr(decrypt($ticket->purchasecode), -4), Str::length(decrypt($ticket->purchasecode)), Str::padLeft('*', 1))); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-dark leading-tight ms-2" id="purchasecodebtn" data-id="<?php echo e($ticket->purchasecode); ?>"><?php echo e(lang('View Details')); ?></button>
                                        <?php if($ticket->purchasecodesupport == 'Supported'): ?>

                                        <span class="badge btn btn-sm badge-success ms-2"><?php echo e(lang('Support Active')); ?></span>
                                        <?php elseif($ticket->purchasecodesupport == 'Expired'): ?>

                                        <span class="badge btn btn-sm text-white cursor-default badge-danger ms-2"><?php echo e(lang('Support Expired')); ?></span>
                                        <?php else: ?>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <!-- End Purchase Code Details -->
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Purchase Code Details -->
                        <div class="purchasecodes alert alert-light-warning br-13 ">
                            <div class="ps-0 pe-0 pb-0">
                                <div class="">
                                    <strong><?php echo e(lang('Puchase Code')); ?> :</strong>
                                    <?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>

                                        <span class=""><?php echo e($ticket->purchasecode); ?></span>
                                    <?php else: ?>
                                        <?php if(setting('purchasecode_on') == 'on'): ?>

                                        <span class=""><?php echo e($ticket->purchasecode); ?></span>
                                        <?php else: ?>

                                        <span class=""><?php echo e(Str::padLeft(Str::substr($ticket->purchasecode, -4), Str::length($ticket->purchasecode), Str::padLeft('*', 1))); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-dark leading-tight ms-2" id="purchasecodebtn" data-id="<?php echo e(encrypt($ticket->purchasecode)); ?>"><?php echo e(lang('View Details')); ?></button>
                                    <?php if($ticket->purchasecodesupport == 'Supported'): ?>

                                    <span class="badge btn btn-sm badge-success ms-2"><?php echo e(lang('Support Active')); ?></span>
                                    <?php elseif($ticket->purchasecodesupport == 'Expired'): ?>

                                    <span class="badge btn btn-sm text-white cursor-default badge-danger ms-2"><?php echo e(lang('Support Expired')); ?></span>
                                    <?php else: ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <!-- End Purchase Code Details -->
                    <?php endif; ?>
                <?php endif; ?>

                <?php if($ticket->cust->logintype == 'envatosociallogin'): ?>
                    <?php if($ticket->usernameverify != null): ?>
                        <?php if($ticket->usernameverify == 'verified'): ?>
                            <?php if(Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
                                <div class="alert alert-light-success br-13 w-100 fs-14 d-none" id="custmermismatch">
                                    <span class=""><?php echo e(lang('The username in purchase details and the current logged-in username do not match. This customer’s username has been verified and is valid.')); ?></span>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-success" id="reverttoverify" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Unverifiy')); ?></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-light-danger br-13 w-100 fs-14 d-none" id="custmermismatch">
                                <span class=""><?php echo e(lang('The username in purchase details and the current logged-in username do not match. This customer seems invalid, please take appropriate action.')); ?></span>
                                <?php if(Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
                                    <div class="mt-1">
                                        <button class="btn btn-sm btn-success" id="reverttowrong" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Verify')); ?></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-light-danger br-13 w-100 fs-14 d-none" id="custmermismatch">
                            <span class=""><?php echo e(lang('The username in purchase details and the current logged-in username do not match. Verify customer details and proceed to the next step.')); ?></span>
                            <div class="mt-1">
                                <button class="btn btn-sm btn-success " id="purchasverified" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Valid User')); ?></button>
                                <button class="btn btn-sm ms-1 btn-danger" id="wrongcustomer" data-id="<?php echo e($ticket->id); ?>"><?php echo e(lang('Invalid User')); ?></button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

				<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>
					<?php
						$toassignusers = $ticket->ticketassignmutliples;
						$condition = false;
					?>

					<?php $__currentLoopData = $toassignusers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toassignuser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if($toassignuser->toassignuser->id == Auth::id()): ?>
							<?php $condition = true; ?>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php if($condition): ?>
						<div class="alert alert-light-info br-13 ">
							<div class="d-flex align-items-center">
								<span class=""><?php echo e(lang('This ticket is assigned to you please respond.')); ?></span>
								<button class="btn-close ms-auto" data-bs-dismiss="alert" aria-hidden="true">×</button>
							</div>
						</div>
					<?php else: ?>
						<div class="alert alert-light-danger br-13 ">
							<div class="">
								<span class=""><?php echo e(lang('This ticket has already been assigned to another employee.')); ?></span>

							</div>
						</div>
					<?php endif; ?>
				<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>
					<?php if($ticket->selfassignuser_id != Auth::id()): ?>
						<div class="alert alert-light-danger br-13 ">
							<div class="">
								<span class=""><?php echo e(lang('This ticket has already been assigned to another employee.')); ?></span>
							</div>
						</div>
					<?php else: ?>
					<div class="alert alert-light-info br-13 ">
						<div class="d-flex align-items-center">
							<span class=""><?php echo e(lang('This ticket has been selfassigned by you, please respond.')); ?></span>
							<button class="btn-close ms-auto" data-bs-dismiss="alert" aria-hidden="true">×</button>
						</div>
					</div>
					<?php endif; ?>
				<?php else: ?>
				<?php endif; ?>

				<div class="card">
					<div class="card-header border-0 mb-1 d-block">
						<div class="d-sm-flex d-block">
							<div>
								<h4 class="card-title mb-1 fs-22"><?php echo e($ticket->subject); ?> </h4>
							</div>
						</div>
						<small class="fs-13"><i class="feather feather-clock text-muted me-1"></i><?php echo e(lang('Created At')); ?>

							<span class="text-muted">
								<?php if($ticket->created_at->timezone(setting('default_timezone'))->format('Y-m-d') == now()->timezone(setting('default_timezone'))->format('Y-m-d')): ?>
									<?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format('h:i A')); ?> (<?php echo e($ticket->created_at->timezone(setting('default_timezone'))->diffForHumans()); ?>)
								<?php else: ?>
									<?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format('D, d M Y, h:i A')); ?> (<?php echo e($ticket->created_at->timezone(setting('default_timezone'))->diffForHumans()); ?>)
								<?php endif; ?>
							</span>
						</small>
					</div>
					<div class="card-body pt-2 readmores px-6 mx-1">
						<div>
							<span><?php echo $ticket->message; ?></span>

							<div class="row galleryopen mt-4">
								<div class="uhelp-attach-container flex-wrap">

									<?php if($ticket->emailticketfile != null): ?>
										<?php if($ticket->emailticketfile == 'mismatch'): ?>
											<div class="border d-table rounded attach-container-width mb-2" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('File upload failed, Please make sure that the file size is within the allowed limits and that the file format is supported.')); ?>">
												<div class="d-flex align-items-center file-attach-uhelp mt-1">
													<div class="me-2">
														<a href="#" class="uhelp-attach-acion d-flex align-items-center justify-content-center"><i class="fe feather-alert-circle text-danger fs-20"></i></a>
													</div>
													<div class="d-flex align-items-center text-muted fs-12 me-3">
														<p class="file-attach-name text-danger mb-0"><?php echo e(lang('Upload Failed')); ?></p>
													</div>
												</div>
											</div>
										<?php else: ?>
											<?php
												$arraytype = explode(',', $ticket->emailticketfile);
											?>
											<?php $__currentLoopData = $arraytype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arraytypes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php
													$arrayextension = explode('.', $arraytypes);
													$finalextension = $arrayextension[1];
												?>
												<div class="border d-table rounded attach-container-width mb-2">
													<div class="d-flex align-items-center file-attach-uhelp">
														<div class="me-2">
															<?php if($finalextension == 'jpg' || $finalextension == 'jpeg' || $finalextension == 'JPG'): ?>
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-jpg" viewBox="0 0 16 16">
																	<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-4.34 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.507.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.24v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.066-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.347.158.48.275.133.117.238.253.314.407ZM0 14.786c0 .164.027.319.082.465.055.147.136.277.243.39.11.113.245.202.407.267.164.062.354.093.569.093.42 0 .748-.115.984-.345.238-.23.358-.566.358-1.005v-2.725h-.791v2.745c0 .202-.046.357-.138.466-.092.11-.233.164-.422.164a.499.499 0 0 1-.454-.246.577.577 0 0 1-.073-.27H0Zm4.92-2.86H3.322v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475.108-.201.161-.427.161-.677 0-.25-.052-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.546 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H4.11v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Z"/>
																</svg>
															<?php elseif($finalextension == 'pdf'): ?>
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
																<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z"/>
																</svg>
															<?php elseif($finalextension == 'csv'): ?>
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16">
																<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/>
																</svg>
															<?php elseif($finalextension == 'png'): ?>
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-png" viewBox="0 0 16 16">
																	<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3.76 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.506.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.82v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.067-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.348.158.48.275.133.117.238.253.314.407Zm-8.64-.706H0v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H.788v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Zm1.964 2.666V13.25h.032l1.761 2.675h.656v-3.999h-.75v2.66h-.032l-1.752-2.66h-.662v4h.747Z"/>
																</svg>
															<?php else: ?>
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
																	<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
																</svg>
															<?php endif; ?>
														</div>
														<div class="d-flex align-items-center text-muted fs-12 me-3">
															<p class="file-attach-name text-truncate mb-0"><?php echo e($arrayextension[0]); ?></p>.<?php echo e($arrayextension[1]); ?>

														</div>
														<a href="<?php echo e(route('emailtoticketimageurl', array($ticket->id,$arraytypes))); ?>" target="_blank" class="uhelp-attach-acion p-2 rounded border lh-1 me-1 d-flex align-items-center justify-content-center"><i
																			class="fe fe-eye text-muted fs-12"></i></a>
														<a href="<?php echo e(route('emailtoticketdownload', array($ticket->id,$arraytypes))); ?>" class="uhelp-attach-acion p-2 rounded border lh-1 d-flex align-items-center justify-content-center"><i
																class="fe fe-download text-muted fs-12"></i></a>
													</div>
												</div>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

										<?php endif; ?>
									<?php endif; ?>


									<?php $__currentLoopData = $ticket->getMedia('ticket'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php
										$a = explode('.', $ticketss->file_name);
										$aa = $a[1];
									?>

									<div class="border d-table rounded attach-container-width mb-2">
										<div class="d-flex align-items-center file-attach-uhelp">
											<div class="me-2">
												<?php if($aa == 'jpg' || $aa == 'jpeg' || $aa == 'JPG'): ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-jpg" viewBox="0 0 16 16">
														<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-4.34 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.507.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.24v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.066-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.347.158.48.275.133.117.238.253.314.407ZM0 14.786c0 .164.027.319.082.465.055.147.136.277.243.39.11.113.245.202.407.267.164.062.354.093.569.093.42 0 .748-.115.984-.345.238-.23.358-.566.358-1.005v-2.725h-.791v2.745c0 .202-.046.357-.138.466-.092.11-.233.164-.422.164a.499.499 0 0 1-.454-.246.577.577 0 0 1-.073-.27H0Zm4.92-2.86H3.322v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475.108-.201.161-.427.161-.677 0-.25-.052-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.546 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H4.11v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Z"/>
													</svg>
												<?php elseif($aa == 'pdf'): ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
													<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z"/>
													</svg>
												<?php elseif($aa == 'csv'): ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16">
													<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/>
													</svg>
												<?php elseif($aa == 'png'): ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-png" viewBox="0 0 16 16">
														<path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3.76 8.132c.076.153.123.317.14.492h-.776a.797.797 0 0 0-.097-.249.689.689 0 0 0-.17-.19.707.707 0 0 0-.237-.126.96.96 0 0 0-.299-.044c-.285 0-.506.1-.665.302-.156.201-.234.484-.234.85v.498c0 .234.032.439.097.615a.881.881 0 0 0 .304.413.87.87 0 0 0 .519.146.967.967 0 0 0 .457-.096.67.67 0 0 0 .272-.264c.06-.11.091-.23.091-.363v-.255H8.82v-.59h1.576v.798c0 .193-.032.377-.097.55a1.29 1.29 0 0 1-.293.458 1.37 1.37 0 0 1-.495.313c-.197.074-.43.111-.697.111a1.98 1.98 0 0 1-.753-.132 1.447 1.447 0 0 1-.533-.377 1.58 1.58 0 0 1-.32-.58 2.482 2.482 0 0 1-.105-.745v-.506c0-.362.067-.678.2-.95.134-.271.328-.482.582-.633.256-.152.565-.228.926-.228.238 0 .45.033.636.1.187.066.348.158.48.275.133.117.238.253.314.407Zm-8.64-.706H0v4h.791v-1.343h.803c.287 0 .531-.057.732-.172.203-.118.358-.276.463-.475a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.475-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.381.574.574 0 0 1-.238.24.794.794 0 0 1-.375.082H.788v-1.406h.66c.218 0 .389.06.512.182.123.12.185.295.185.521Zm1.964 2.666V13.25h.032l1.761 2.675h.656v-3.999h-.75v2.66h-.032l-1.752-2.66h-.662v4h.747Z"/>
													</svg>
												<?php else: ?>
													<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
														<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
													</svg>
												<?php endif; ?>
											</div>
											<div class="d-flex align-items-center text-muted fs-12 me-3">
												<p class="file-attach-name text-truncate mb-0"><?php echo e($a[0]); ?></p>.<?php echo e($a[1]); ?>

											</div>
											<a href="<?php echo e(route('imageurl', array($ticketss->id,$ticketss->file_name))); ?>" target="_blank" class="uhelp-attach-acion p-2 rounded border lh-1 me-1 d-flex align-items-center justify-content-center"><i
																class="fe fe-eye text-muted fs-12"></i></a>
											<a href="<?php echo e(route('imagedownload', array($ticketss->id,$ticketss->file_name))); ?>" class="uhelp-attach-acion p-2 rounded border lh-1 d-flex align-items-center justify-content-center"><i
													class="fe fe-download text-muted fs-12"></i></a>
										</div>
									</div>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</div>
							</div>
						</div>
					</div>

				</div>

				<?php $customfields = $ticket->ticket_customfield()->get(); ?>
				<?php if($customfields->isNotEmpty()): ?>
					<?php $__currentLoopData = $customfields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customfield): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if($customfield->fieldtypes == 'textarea'): ?>
							<?php if($customfield->privacymode == '1'): ?>
								<?php
									$extrafieldds = decrypt($customfield->values);
								?>
								<div class="card">
									<div class="card-header border-0">
										<h4 class="card-title"><?php echo e($customfield->fieldnames); ?></h4>
									</div>
									<div class="card-body">
										<span><?php echo e($extrafieldds); ?></span>
									</div>
								</div>
							<?php else: ?>
								<div class="card">
									<div class="card-header border-0">
										<h4 class="card-title"><?php echo e($customfield->fieldnames); ?></h4>
									</div>
									<div class="card-body">
										<span><?php echo e($customfield->values); ?></span>
									</div>
								</div>

							<?php endif; ?>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>


				

				<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>
					<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.showticketinclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php elseif(in_array(Auth::id(), $array)): ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.showticketinclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php else: ?>
					<?php endif; ?>
				<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>
					<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.showticketinclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php elseif($ticket->selfassignuser_id == Auth::id()): ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.showticketinclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php else: ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo $__env->make('admin.viewticket.showticketdata.showticketinclude', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				<?php endif; ?>

				



				
				<?php if($comments->isNOtEmpty()): ?>

				<div class="card">
					<div class="card-header border-botom-0 py-3">
						<h4 class="card-title"><?php echo e(lang('Conversations')); ?></h4>
                        <?php if($ticket->status == 'Closed'): ?>
                            <button type="buttom" class="btn btn-primary ms-auto disabled" id="ticket_to_article" value="">
                                <i class="feather feather-book me-3 fs-18 my-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Create Article')); ?>"></i>
                                <span><?php echo e(lang('Ticket To Article')); ?> </span>
                            </button>
                        <?php endif; ?>
					</div>
					<div class="suuport-convercontentbody" >
						<?php echo e(csrf_field()); ?>

						<div id="spruko_loaddata">
							<?php echo $__env->make('admin.viewticket.showticketdata', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						</div>
					</div>
				</div>
				<?php endif; ?>
				

			</div>

			<div class="col-xl-3 col-lg-12 col-md-12">

				<!-- Ticket Information -->

					<?php if($ticket->ticketassignmutliples->isNotEmpty() && $ticket->selfassignuser_id == null): ?>
						<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
							<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php elseif(in_array(Auth::id(), $array)): ?>
							<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php else: ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php endif; ?>
					<?php elseif($ticket->ticketassignmutliples->isEmpty() && $ticket->selfassignuser_id != null): ?>
						<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
							<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php elseif($ticket->selfassignuser_id == Auth::id()): ?>
							<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php else: ?>
							<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php endif; ?>
					<?php else: ?>
						<?php echo $__env->make('admin.viewticket.showticketdata.ticketinfooter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php endif; ?>

				<!-- Ticket Information -->

				<!-- Ticket Activity Details -->
				<div class="card">
					<div class="card-header d-sm-max-flex border-bottom-0 d-flex">
						<h3 class="card-title"><?php echo e(lang('Assign Activity')); ?></h3>
						<div class="card-options">
							<a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.tickethistory', $ticket->ticket_id)); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(lang('View History')); ?></a>
						</div>
					</div>
					<div class="card-body">
						<ul class="list-unstyled mb-0 ticket-activity">
							<?php if($ticket->user_id == null): ?>
							<?php if($ticket->cust != null): ?>

							<li class="list-item border-bottom-0">
								<div class="d-flex">
									<div class="me-3">
										<span class="avatar brround mt-1">
											<?php if($ticket->cust->image == null): ?>

												<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
											<?php else: ?>

												<img src="<?php echo e(asset('uploads/profile/'.$ticket->cust->image)); ?>" class="brround" alt="<?php echo e($ticket->cust->image); ?>">
											<?php endif; ?>
										</span>
									</div>
									<div>
										<p class="mb-0 text-muted fs-12"><?php echo e(lang('Created By')); ?></p>
										<p class="fs-13 mb-0">
											<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($ticket->cust->username); ?></a>
											<span class="fs-12 text-muted">(<?php echo e(lang($ticket->cust->userType)); ?>)</span>
										</p>

									</div>
									<div class="datatime fs-11 mb-0 ms-auto text-end">
										<div class="w-100 mb-1"><?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format(setting('date_format'))); ?></div>
										<div class="w-100"><?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format(setting('time_format'))); ?></div>
									</div>
								</div>
							</li>

							<?php endif; ?>
							<?php endif; ?>
							<?php if($ticket->user_id != null): ?>
							<?php if($ticket->users != null): ?>

							<li class="list-item border-bottom-0">
								<div class="d-flex">
									<div class="me-3">
										<span class="avatar brround mt-1">

											<?php if($ticket->users->image == null): ?>

												<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
											<?php else: ?>

												<img src="<?php echo e(asset('uploads/profile/'.$ticket->users->image)); ?>" class="brround" alt="<?php echo e($ticket->users->image); ?>">
											<?php endif; ?>
										</span>
									</div>
									<div>
										<p class="mb-0 text-muted fs-12"><?php echo e(lang('Created By')); ?></p>
										<p class="fs-13 mb-0">
											<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($ticket->users->name); ?></a>
											<?php if(!empty($ticket->users->getRoleNames()[0])): ?>
											<span class="fs-12 text-muted">(<?php echo e($ticket->users->getRoleNames()[0]); ?>)</span>
											<?php endif; ?>

										</p>

									</div>
									<div class="datatime fs-11 mb-0 ms-auto text-end">
										<div class="w-100 mb-1"><?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format(setting('date_format'))); ?></div>
										<div class="w-100"><?php echo e($ticket->created_at->timezone(setting('default_timezone'))->format(setting('time_format'))); ?></div>
									</div>
								</div>
							</li>

							<?php endif; ?>
							<?php endif; ?>
							<?php if($ticket->selfassignuser_id != null): ?>
								<?php if($ticket->selfassign != null): ?>
								<li class="list-item border-bottom-0">
									<div class="d-flex">
										<div class="me-3">
											<span class="avatar brround mt-1">
												<?php if($ticket->selfassign->image == null): ?>

													<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
												<?php else: ?>

													<img src="<?php echo e(asset('uploads/profile/'.$ticket->selfassign->image)); ?>" class="brround" alt="<?php echo e($ticket->selfassign->image); ?>">
												<?php endif; ?>
											</span>
										</div>
										<div>
											<p class="fs-13 mb-0">
												<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($ticket->selfassign->name); ?></a>
												<?php if(!empty($ticket->selfassign->getRoleNames()[0])): ?>
												<span class="fs-12 text-muted">(<?php echo e($ticket->selfassign->getRoleNames()[0]); ?>)</span>
												<?php endif; ?>

											</p>
											<p class="mb-0 fs-12 text-success"><?php echo e(lang('Self Assigned')); ?></p>
										</div>
									</div>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($ticket->selfassignuser_id == null): ?>
							<?php if($ticket->myassignuser != null): ?>
							<li class="list-item border-bottom-0">
								<div class="d-flex">
									<div class="me-3">
										<span class="avatar brround mt-1">
											<?php if($ticket->myassignuser->image == null): ?>

												<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
											<?php else: ?>

												<img src="<?php echo e(asset('uploads/profile/'.$ticket->myassignuser->image)); ?>" class="brround" alt="<?php echo e($ticket->myassignuser->image); ?>">
											<?php endif; ?>
										</span>
									</div>
									<div>
										<p class="fs-13 mb-0">
											<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($ticket->myassignuser->name); ?></a>
											<?php if(!empty($ticket->myassignuser->getRoleNames()[0])): ?>
											<span class="fs-12 text-muted">(<?php echo e($ticket->myassignuser->getRoleNames()[0]); ?>)</span>
											<?php endif; ?>
										</p>
										<p class="mb-0 fs-12 text-success font-weight-semibold"><?php echo e(lang('Assigner')); ?></p>
									</div>
								</div>
							</li>
							<?php endif; ?>

							<?php $toassignusers = $ticket->ticketassignmutliples; ?>
							<?php if($toassignusers->isNOtEmpty()): ?>
								<?php $__currentLoopData = $toassignusers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toassignuser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if($toassignuser->toassignuser != null): ?>
									<li class="list-item border-bottom-0">
										<div class="d-flex">
											<div class="me-3">
												<span class="avatar brround mt-1">
													<?php if($toassignuser->toassignuser->image == null): ?>

														<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
													<?php else: ?>

														<img src="<?php echo e(asset('uploads/profile/'.$toassignuser->toassignuser->image)); ?>" class="brround" alt="<?php echo e($toassignuser->toassignuser->image); ?>">
													<?php endif; ?>
												</span>
											</div>
											<div>
												<p class="fs-13 mb-0">
													<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($toassignuser->toassignuser->name); ?></a>
													<?php if(!empty($toassignuser->toassignuser->getRoleNames()[0])): ?>
													<span class="fs-12 text-muted">(<?php echo e($toassignuser->toassignuser->getRoleNames()[0]); ?>)</span>
													<?php endif; ?>
												</p>
												<p class="mb-0 fs-12 text-secondary"><?php echo e(lang('Assignee')); ?></p>

											</div>
										</div>
									</li>
									<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>

							<?php endif; ?>

							<?php if($ticket->closedby_user != null): ?>
							<li class="list-item border-bottom-0">
								<div class="d-flex">
									<div class="me-3">
										<span class="avatar brround mt-1">
											<?php if($ticket->closedusers->image == null): ?>

												<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="brround" alt="default">
											<?php else: ?>

												<img src="<?php echo e(asset('uploads/profile/'.$ticket->closedusers->image)); ?>" class="brround" alt="<?php echo e($ticket->closedusers->image); ?>">
											<?php endif; ?>
										</span>
									</div>
									<div>
										<p class="fs-13 mb-0">
											<a href="javascript:void(0);" class="text-dark font-weight-semibold"><?php echo e($ticket->closedusers->name); ?></a>
											<?php if(!empty($ticket->closedusers->getRoleNames()[0])): ?>
											<span class="fs-12 text-muted">(<?php echo e($ticket->closedusers->getRoleNames()[0]); ?>)</span>
											<?php endif; ?>
											<svg xmlns="http://www.w3.org/2000/svg" class="svg-success"data-bs-toggle="tooltip" data-bs-placement="top" title="Solved" viewBox="0 0 24 24"><path opacity="0.2" d="M10.3125,16.09375a.99676.99676,0,0,1-.707-.293L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328l-6.1875,6.1875A.99676.99676,0,0,1,10.3125,16.09375Z" opacity=".99"/><path d="M12,2A10,10,0,1,0,22,12,10.01146,10.01146,0,0,0,12,2Zm5.207,7.61328-6.1875,6.1875a.99963.99963,0,0,1-1.41406,0L6.793,12.98828A.99989.99989,0,0,1,8.207,11.57422l2.10547,2.10547L15.793,8.19922A.99989.99989,0,0,1,17.207,9.61328Z"/></svg>
										</p>
										<p class="mb-0 fs-12 text-secondary">Closed</p>
									</div>
									<div class="datatime fs-11 mb-0 ms-auto text-end">
										<div class="w-100 mb-1"><?php echo e($ticket->updated_at->timezone(setting('default_timezone'))->format(setting('date_format'))); ?></div>
										<div class="w-100"><?php echo e($ticket->updated_at->timezone(setting('default_timezone'))->format(setting('time_format'))); ?></div>
									</div>
								</div>
							</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>
				<!-- End Ticket Activity Details -->
				<!-- Customer Details -->
				<div class="card">
					<div class="card-header d-sm-max-flex border-0">
						<div class="card-title"><?php echo e(lang('Customer Details')); ?></div>
						<?php if($custsimillarticket > 1): ?>
							<div class="card-options">
								<a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.customer.tickethistory', $ticket->cust->id)); ?>" target="_blank" rel="noopener noreferrer"><?php echo e(lang('Previous Tickets')); ?></a>
							</div>
						<?php endif; ?>
					</div>
					<div class="card-body text-center pt-2 px-0 pb-0 py-0">
						<div class="profile-pic">
							<div class="profile-pic-img mb-2">
								<span class="bg-success dots" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Online')); ?>"></span>
								<?php if($ticket->cust->image == null): ?>

									<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>"  class="brround avatar-xxl" alt="default">
								<?php else: ?>

									<img class="brround avatar-xxl" alt="<?php echo e($ticket->cust->image); ?>" src="<?php echo e(asset('uploads/profile/'. $ticket->cust->image)); ?>">
								<?php endif; ?>

							</div>
							<div class="text-dark">
								<?php if($ticket->cust->voilated == 'on'): ?>
									<span class="badge badge-danger-light"><i class="fa fa-exclamation-triangle text-danger"></i> <?php echo e(lang('Voilation')); ?></span>
								<?php endif; ?>
								<h5 class="mb-1 font-weight-semibold2"><?php echo e($ticket->cust->username); ?></h5>

								<h6 class="mb-1 mt-2 text-muted fw-normal"><?php echo e($ticket->cust->firstname); ?> <?php echo e($ticket->cust->lastname); ?></h6>
								<small class="text-muted "><?php echo e($ticket->cust->email); ?>

								</small>
							</div>
						</div>
						<div class="table-responsive text-start tr-lastchild">
							<table class="table mb-0 table-information">
								<tbody>
									<tr>
										<td>
											<span class="w-50"><?php echo e(lang('IP')); ?></span>
										</td>
										<td>:</td>
										<td>
											<span class="font-weight-semibold"><?php echo e($ticket->cust->last_login_ip); ?></span>
										</td>
									</tr>
									<tr>
										<td>
											<span class="w-50"><?php echo e(lang('Mobile Number')); ?></span>
										</td>
										<td>:</td>
										<td>
											<span class="font-weight-semibold"><?php echo e($ticket->cust->phone); ?></span>
										</td>
									</tr>
									<tr>
										<td>
											<span class="w-50"><?php echo e(lang('Country')); ?></span>
										</td>
										<td>:</td>
										<td>
											<span class="font-weight-semibold"><?php if($ticket->cust->country != null): ?><?php echo e(lang($ticket->cust->country)); ?><?php else: ?><?php echo e($ticket->cust->country); ?><?php endif; ?></span>
										</td>
									</tr>
									<tr>
										<td>
											<span class="w-50"><?php echo e(lang('Timezone')); ?></span>
										</td>
										<td>:</td>
										<td>
											<span class="font-weight-semibold"><?php if($ticket->cust->timezone != null): ?><?php echo e(lang($ticket->cust->timezone)); ?><?php else: ?><?php echo e($ticket->cust->timezone); ?><?php endif; ?></span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!-- End Customer Details -->
				<!--ticke note  -->
				<div class="card">
					<div class="card-header d-sm-max-flex border-0">
						<div class="card-title"><?php echo e(lang('Ticket Note')); ?></div>
						<div class="card-options">
							<?php if($ticket->status != 'Closed'): ?>

							<a href="javascript:void(0)" class="btn btn-secondary " id="create-new-note"><i class="feather feather-plus"  ></i></a>
							<?php endif; ?>

						</div>
					</div>
					<?php $emptynote = $ticket->ticketnote()->get() ?>
					<?php if($emptynote->isNOtEmpty()): ?>
					<div class="card-body  item-user">
						<div id="refresh">

							<?php $__currentLoopData = $ticket->ticketnote()->latest()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<div class="alert alert-light-warning ticketnote" id="ticketnote_<?php echo e($note->id); ?>" role="alert">
								<?php if($note->user_id == Auth::id() || Auth::user()->getRoleNames()[0] == 'superadmin'): ?>

								<a href="javascript:" class="ticketnotedelete" data-id="<?php echo e($note->id); ?>" onclick="deletePost(event.target)">
									<i class="feather feather-x" data-id="<?php echo e($note->id); ?>" ></i>
								</a>
								<?php endif; ?>

								<p class="m-0"><?php echo e($note->ticketnotes); ?></p>
								<p class="text-end mb-0"><small><i><b><?php echo e($note->users->name); ?></b> <?php if(!empty($note->users->getRoleNames()[0])): ?> (<?php echo e($note->users->getRoleNames()[0]); ?>) <?php endif; ?></i></small></p>
							</div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						</div>
					</div>
					<?php else: ?>
					<div class="card-body">
						<div class="text-center">
							<div class="avatar avatar-xxl empty-block mb-4">
								<svg xmlns="http://www.w3.org/2000/svg" height="50" width="50" viewBox="0 0 48 48"><path fill="#CDD6E0" d="M12.8 4.6H38c1.1 0 2 .9 2 2V46c0 1.1-.9 2-2 2H6.7c-1.1 0-2-.9-2-2V12.7l8.1-8.1z"/><path fill="#ffffff" d="M.1 41.4V10.9L11 0h22.4c1.1 0 2 .9 2 2v39.4c0 1.1-.9 2-2 2H2.1c-1.1 0-2-.9-2-2z"/><path fill="#CDD6E0" d="M11 8.9c0 1.1-.9 2-2 2H.1L11 0v8.9z"/><path fill="#FFD05C" d="M15.5 8.6h13.8v2.5H15.5z"/><path fill="#dbe0ef" d="M6.3 31.4h9.8v2.5H6.3zM6.3 23.8h22.9v2.5H6.3zM6.3 16.2h22.9v2.5H6.3z"/><path fill="#FFD15C" d="M22.8 35.7l-2.6 6.4 6.4-2.6z"/><path fill="#334A5E" d="M21.4 39l-1.2 3.1 3.1-1.2z"/><path fill="#FF7058" d="M30.1 18h5.5v23h-5.5z" transform="rotate(-134.999 32.833 29.482)"/><path fill="#40596B" d="M46.2 15l1 1c.8.8.8 2 0 2.8l-2.7 2.7-3.9-3.9 2.7-2.7c.9-.6 2.2-.6 2.9.1z"/><path fill="#F2F2F2" d="M39.1 19.3h5.4v2.4h-5.4z" transform="rotate(-134.999 41.778 20.536)"/></svg>
							</div>
							<h4 class="mb-2"><?php echo e(lang('Don’t have any notes yet')); ?></h4>
							<span class="text-muted"><?php echo e(lang('Add your notes here')); ?></span>
						</div>
					</div>
					<?php endif; ?>
				</div>
				<!-- End ticket note  -->
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<!-- INTERNAL Summernote js  -->
<script src="<?php echo e(asset('assets/plugins/summernote/summernote.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Index js-->
<script src="<?php echo e(asset('assets/js/support/support-ticketview.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- DropZone JS -->
<script src="<?php echo e(asset('assets/plugins/dropzone/dropzone.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- galleryopen JS -->
<script src="<?php echo e(asset('assets/plugins/simplelightbox/simplelightbox.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/simplelightbox/light-box.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Sweet-Alert js-->
<script src="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.min.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/js/select2.js')); ?>?v=<?php echo time(); ?>"></script>

<!--Showmore Js-->
<script src="<?php echo e(asset('assets/js/jquery.showmore.js')); ?>?v=<?php echo time(); ?>"></script>


<script type="text/javascript">


"use strict";

var ticrat = <?php echo json_encode(setting('ticketrating') == 'off'); ?>


if(ticrat){
    if(document.getElementById('closed')){
        if(document.getElementById('closed').checked){
            document.getElementById('ratingonoff').classList.add('d-block');
            document.getElementById('ratingonoff').classList.remove('d-none');
        }
        document.getElementById('closed').addEventListener("click", function(){
            document.getElementById('ratingonoff').classList.add('d-block');
            document.getElementById('ratingonoff').classList.remove('d-none');
        });
    }

    if(document.getElementById('onhold')){
        document.getElementById('onhold').addEventListener("click", function(){
            document.getElementById('ratingonoff').classList.add('d-none');
            document.getElementById('ratingonoff').classList.remove('d-block');
        });
    }

    if(document.getElementById('Inprogress1')){
        document.getElementById('Inprogress1').addEventListener("click", function(){
            document.getElementById('ratingonoff').classList.add('d-none');
            document.getElementById('ratingonoff').classList.remove('d-block');
        });
    }

    if(document.getElementById('Inprogress2')){
        document.getElementById('Inprogress2').addEventListener("click", function(){
            document.getElementById('ratingonoff').classList.add('d-none');
            document.getElementById('ratingonoff').classList.remove('d-block');
        });
    }

    if(document.getElementById('Inprogress3')){
        document.getElementById('Inprogress3').addEventListener("click", function(){
            document.getElementById('ratingonoff').classList.add('d-none');
            document.getElementById('ratingonoff').classList.remove('d-block');
        });
    }
}

// Image Upload
var uploadedDocumentMap = {}
Dropzone.options.documentDropzone = {
	url: '<?php echo e(url('/admin/ticket/imageupload/' .$ticket->ticket_id)); ?>',
	maxFilesize: '<?php echo e(setting('FILE_UPLOAD_MAX')); ?>', // MB
	addRemoveLinks: true,
	acceptedFiles: '<?php echo e(setting('FILE_UPLOAD_TYPES')); ?>',
	headers: {
	'X-CSRF-TOKEN': "<?php echo e(csrf_token()); ?>"
	},
	success: function (file, response) {
	$('form').append('<input type="hidden" name="comments[]" value="' + response.name + '">')
	uploadedDocumentMap[file.name] = response.name
	},
	removedfile: function (file) {
	file.previewElement.remove()
	var name = ''
	if (typeof file.file_name !== 'undefined') {
		name = file.file_name
	} else {
		name = uploadedDocumentMap[file.name]
	}
	$('form').find('input[name="comments[]"][value="' + name + '"]').remove()
	},
	init: function () {
	<?php if(isset($project) && $project->document): ?>
		var files =
		<?php echo json_encode($project->document); ?>

		for (var i in files) {
		var file = files[i]
		this.options.addedfile.call(this, file)
		file.previewElement.classList.add('dz-complete')
		$('form').append('<input type="hidden" name="comments[]" value="' + file.file_name + '">')
		}
	<?php endif; ?>
	this.on('error', function(file, errorMessage) {
		if (errorMessage.message) {
			var errorDisplay = document.querySelectorAll('[data-dz-errormessage]');
			errorDisplay[errorDisplay.length - 1].innerHTML = errorMessage.message;
		}
	});
	}
}



// Edit Form
function showEditForm(id) {
	var x = document.querySelector(`#supportnote-icon-${id}`);

	if (x.style.display == "block") {
		x.style.display = "none";
	}
	else {

		x.style.display = "block";
	}
}

// Delete Media
function deleteticket(event) {
	var id  = $(event).data("id");
	let _url = `<?php echo e(url('/admin/image/delete/${id}')); ?>`;

	let _token   = $('meta[name="csrf-token"]').attr('content');

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
					url: _url,
					type: 'DELETE',
					data: {
					_token: _token
					},
					success: function(response) {
						$("#imageremove"+id).remove();
						$('#imageremove'+ id).remove();
					},
					error: function (data) {
					console.log('Error:', data);
					}
				});
			}
		});
}

<?php if($ticket->status != "Closed"): ?>

// onhold ticket status
let hold = document.getElementById('onhold');
let text = document.querySelector('.status');
let hold1 = document.querySelectorAll('.hold');
let  status = false;


if(hold != null)
{
	hold.addEventListener('click',(e)=>{
	if( status == false)
		statusDiv();
		status = true;
}, false)

if(document.getElementById('onhold').hasAttribute("checked") == true){
	statusDiv();
	status = true;
}

}



function statusDiv(){
	let Div = document.createElement('div')
	Div.setAttribute('class','d-block pt-4');
	Div.setAttribute('id','holdremove');

	let newField = document.createElement('textarea');
	newField.setAttribute('type','text');
	newField.setAttribute('name','note');
	newField.setAttribute('class',`form-control <?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>`);
	newField.setAttribute('rows',3);
	newField.setAttribute('placeholder','<?php echo e(lang("Leave a message for On-Hold")); ?>');
	newField.innerText = `<?php echo e(old('note',$ticket->note)); ?>`;
	Div.append(newField);
	text.append(Div);
}


hold1.forEach((element,index)=>{
	element.addEventListener('click',()=>{
		let myobj = document.getElementById("holdremove");
		myobj?.remove();

		status = false
	}, false)
})

<?php endif; ?>


// Variables
var SITEURL = '<?php echo e(url('')); ?>';

// Csrf field
$.ajaxSetup({
	headers: {
	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

/*  When user click add note button */
$('#create-new-note').on('click', function () {
	$('#btnsave').val("create-product");
	$('#ticket_id').val(`<?php echo e($ticket->id); ?>`);
	$('#note_form').trigger("reset");
	$('.modal-title').html(`<?php echo e(lang('Add Note', 'menu')); ?>`);
	$('#addnote').modal('show');

});

// Note Submit button
$('body').on('submit', '#note_form', function (e) {
	e.preventDefault();
	var actionType = $('#btnsave').val();
	var fewSeconds = 2;
	$('#btnsave').html(`<?php echo e(lang('Sending..', 'menu')); ?>`);
	$('#btnsave').prop('disabled', true);
		setTimeout(function(){
			$('#btnsave').prop('disabled', false);
		}, fewSeconds*1000);
	var formData = new FormData(this);
	$.ajax({
		type:'POST',
		url: SITEURL + "/admin/note/create",
		data: formData,
		cache:false,
		contentType: false,
		processData: false,

		success: (data) => {
			$('#note_form').trigger("reset");
			$('#addnote').modal('hide');
			$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
			location.reload();
			toastr.success(data.success);

		},
		error: function(data){
			console.log('Error:', data);
			$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
		}
	});
});

let ticket_status = <?php echo json_encode($ticket->status); ?>;
if(ticket_status == 'Closed'){
    let status= false;
    let tickettoarticleList = document.querySelectorAll('.tickettoarticle');
    tickettoarticleList.forEach(ele =>{
        ele.addEventListener('click', ()=>{
            for (let index = 0; index < tickettoarticleList.length; index++) {
                if(tickettoarticleList[index].checked){
                    $('#ticket_to_article').removeClass("disabled");
                    status = false;
                    break;
                }else{
                    $('#ticket_to_article').addClass("disabled");
                    console.log('else');
                }
            }
            if(status){
                console.log(status);
                $('#ticket_to_article').addClass("disabled");
            }
        })
    })

    $('body').on('click', '#ticket_to_article', function () {
        let ticket_Id = <?php echo json_encode($ticket->ticket_id); ?>;
        var ticket_to_article_Id = [];
        let tickettoarticle = document.querySelectorAll('.tickettoarticle');

        if(tickettoarticle.length){
            tickettoarticle.forEach(e => {
                if(e.checked){
                    ticket_to_article_Id.push(e.getAttribute('value'))
                }
            });
        }

        console.log(ticket_to_article_Id,ticket_Id);

        if(ticket_to_article_Id.length){
            var per = <?php echo json_encode(Auth::user()->can('Article Create')); ?>

            if(per){
                window.location.href = `${SITEURL}/admin/ticketarticle/${ticket_Id}/${ticket_to_article_Id}`;
            }else{
                toastr.error('You do not have permission to create an article.');
            }
        }else{
            toastr.error('Please select the field');
        }

    });
}

// when user click its get modal popup to assigned the ticket
$('body').on('click', '#assigned', function () {
	var assigned_id = $(this).data('id');
	$('.select2_modalassign').select2({
		dropdownParent: ".sprukosearch",
		minimumResultsForSearch: '',
		placeholder: "Search",
		width: '100%'
	});

	$.get('ticketassigneds/' + assigned_id , function (data) {
		$('#AssignError').html('');
		$('#assigned_id').val(data.assign_data.id);
		$(".modal-title").text('Assign To Agent');
		$('#username').html(data.table_data);
		$('#addassigned').modal('show');
	});

});

// Assigned Button submit
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


// ticket comment delete
$('body').on('click', '#deletecomment', function () {
	var id = $(this).data("id");
	console.log(id);
	swal({
			title: `<?php echo e(lang('Are you sure you want to delete this comment?', 'alerts')); ?>`,
			text: "<?php echo e(lang('This might erase your records permanently.', 'alerts')); ?>",
			icon: "warning",
			buttons: true,
			dangerMode: true,
		})
		.then((willDelete) => {
		if (willDelete) {

			$.ajax({
				type: "get",
				url: SITEURL + "/admin/ticket/deletecomment/"+id,
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

// Reopen the ticket
$('body').on('click', '#reopen', function(){
	var reopenid = $(this).data('id');
	$.ajax({
		type:'POST',
		url: SITEURL + "/admin/ticket/reopen/" + reopenid,
		data: {
			reopenid:reopenid
		},
		success:function(data){
			console.log(data);
			toastr.success(data.success);
			location.reload();

		},
		error:function(data){
			toastr.error(data);
		}
	});

});

// change priority
$('#priority').on('click', function () {

	$('#PriorityError').html('');
	$('#btnsave').val("save");
	$('#priority_form').trigger("reset");
	$('.modal-title').html(`<?php echo e(lang('Priority')); ?>`);
	$('#addpriority').modal('show');
	$('.select2_modalpriority').select2({
		dropdownParent: ".sprukopriority",
		minimumResultsForSearch: '',
		placeholder: "Search",
		width: '100%'
	});


});

$('body').on('submit', '#priority_form', function (e) {
	e.preventDefault();
	var actionType = $('#pribtnsave').val();
	var fewSeconds = 2;
	$('#btnsave').html('Sending..');
	var formData = new FormData(this);
	$.ajax({
	type:'POST',
	url: SITEURL + "/admin/priority/change",
	data: formData,
	cache:false,
	contentType: false,
	processData: false,

	success: (data) => {
	$('#PriorityError').html('');
	$('#priority_form').trigger("reset");
	$('#addpriority').modal('hide');
	$('#pribtnsave').html('<?php echo e(lang('Save Changes')); ?>');
	location.reload();
	toastr.success(data.success);


	},
	error: function(data){
		$('#PriorityError').html('');
		$('#PriorityError').html(data.responseJSON.errors.priority_user_id);
		$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
	}
	});
});
// end priority

// category list
$('body').on('click', '.sprukocategory', function(){

	var category_id = $(this).data('id');
    $('.modal-title').html(`<?php echo e(lang('Category', 'menu')); ?>`);
	$('#CategoryError').html('');
	$('#addcategory').modal('show');
	$.ajax({
		type: "get",
		url: SITEURL + "/admin/category/list/" + category_id,
		success: function(data){
			$('.select4-show-search').select2({
				dropdownParent: ".sprukosearchcategory",
			});
			$('.subcategoryselect').select2({
					dropdownParent: ".sprukosearchcategory",
				});
			$('#sprukocategorylist').html(data.table_data);
			$('.ticket_id').val(data.ticket.id);

			if(data.ticket.project != null){
				$('#subcategory')?.empty();
				$('#selectSubCategory .removecategory')?.remove();
				let selectDiv = document.querySelector('#selectSubCategory');
				let Divrow = document.createElement('div');
				Divrow.setAttribute('class','removecategory');
				let selectlabel =  document.createElement('label');
				selectlabel.setAttribute('class','form-label')
				selectlabel.innerText = "Projects";
				let selecthSelectTag =  document.createElement('select');
				selecthSelectTag.setAttribute('class','form-control select2-shows-search');
				selecthSelectTag.setAttribute('id', 'subcategory');
				selecthSelectTag.setAttribute('name', 'project');
				selecthSelectTag.setAttribute('data-placeholder','Select Projects');
				let selectoption = document.createElement('option');
				selectoption.setAttribute('label','Select Projects')
				selectDiv.append(Divrow);
				// Divrow.append(Divcol3);
				Divrow.append(selectlabel);
				Divrow.append(selecthSelectTag);
				selecthSelectTag.append(selectoption);
				$('.select2-shows-search').select2({
					dropdownParent: ".sprukosearchcategory",
				});
				$('#subcategory').append(data.projectop);

			}
			<?php if(setting('ENVATO_ON') == 'on'): ?>
			if(data.ticket.purchasecode != null){
				$('#envato_id')?.empty();
				$('#envatopurchase .row')?.remove();
				let selectDiv = document.querySelector('#envatopurchase');
				let Divrow = document.createElement('div');
				Divrow.setAttribute('class','row');
				let Divcol3 = document.createElement('div');
				let selectlabel =  document.createElement('label');
				selectlabel.setAttribute('class','form-label')
				selectlabel.innerHTML = "<?php echo e(lang('Envato Purchase Code')); ?>";
				let divcol9 = document.createElement('div');
				let selecthSelectTag =  document.createElement('input');
				selecthSelectTag.setAttribute('class','form-control');
				selecthSelectTag.setAttribute('type','search');
				selecthSelectTag.setAttribute('id', 'envato_id');
				selecthSelectTag.setAttribute('name', 'envato_id');
				// selecthSelectTag.setAttribute('value', data.ticket.purchasecode);
				selecthSelectTag.setAttribute('placeholder', '<?php echo e(lang("Update Your Purchase Code")); ?>');
				let selecthSelectInput =  document.createElement('input');
				selecthSelectInput.setAttribute('type','hidden');
				selecthSelectInput.setAttribute('id', 'envato_support');
				selecthSelectInput.setAttribute('name', 'envato_support');
				// selecthSelectInput.setAttribute('value', data.ticket.purchasecodesupport);
				selectDiv.append(Divrow);
				Divrow.append(Divcol3);
				Divcol3.append(selectlabel);
				divcol9.append(selecthSelectTag);
				divcol9.append(selecthSelectInput);
				Divrow.append(divcol9);
			}
			<?php endif; ?>
			if(data.ticket.subcategory != null){

				$('#selectssSubCategory').show()
				$('#subscategory').html(data.subcategoryt);

			}else{

				if(!data.subcategoryt){
					$('#selectssSubCategory').hide();
				}else{
					$('#selectssSubCategory').show()
					$('#subscategory').html(data.subcategoryt);
				}
			}



		},
		error: function(data){

		}
	});
});



// when category change its get the subcat list
$('body').on('change', '#sprukocategorylist', function(e) {
	var cat_id = e.target.value;
	$('#selectssSubCategory').hide();
	$.ajax({
		url:"<?php echo e(route('guest.subcategorylist')); ?>",
		type:"POST",
			data: {
			cat_id: cat_id
			},
		success:function (data) {

			if(data.subcategoriess){
				$('#selectssSubCategory').show()
				$('#subscategory').html(data.subcategoriess)
			}
			else{
				$('#selectssSubCategory').hide();
				$('#subscategory').html('')
			}

			<?php if(setting('ENVATO_ON') == 'on'): ?>
			// Envato access
			if(data.envatosuccess.length >= 1){
				$('.sprukoapiblock').attr('disabled', true);
				$('#envato_id')?.empty();
				$('#envatopurchase .row')?.remove();
				let selectDiv = document.querySelector('#envatopurchase');
				let Divrow = document.createElement('div');
				Divrow.setAttribute('class','row');
				let Divcol3 = document.createElement('div');
				let selectlabel =  document.createElement('label');
				selectlabel.setAttribute('class','form-label')
				selectlabel.innerHTML = "<?php echo e(lang('Envato Purchase Code')); ?>";
				let divcol9 = document.createElement('div');
				let selecthSelectTag =  document.createElement('input');
				selecthSelectTag.setAttribute('class','form-control');
				selecthSelectTag.setAttribute('type','search');
				selecthSelectTag.setAttribute('id', 'envato_id');
				selecthSelectTag.setAttribute('name', 'envato_id');
				selecthSelectTag.setAttribute('placeholder', 'Enter Your Purchase Code');
				let selecthSelectInput =  document.createElement('input');
				selecthSelectInput.setAttribute('type','hidden');
				selecthSelectInput.setAttribute('id', 'envato_support');
				selecthSelectInput.setAttribute('name', 'envato_support');
				selectDiv.append(Divrow);
				Divrow.append(Divcol3);
				Divcol3.append(selectlabel);
				divcol9.append(selecthSelectTag);
				divcol9.append(selecthSelectInput);
				Divrow.append(divcol9);
				$('.purchasecode').attr('disabled', true);

			}else{
				$('#envato_id')?.empty();
				$('#envatopurchase .row')?.remove();
				$('.sprukoapiblock').removeAttr('disabled');
				$('.purchasecode').removeAttr('disabled');
			}
			<?php endif; ?>


			// projectlist
			if(data.subcategories.length >= 1){

				$('#subcategory')?.empty();
				$('#selectSubCategory .removecategory')?.remove();
				let selectDiv = document.querySelector('#selectSubCategory');
				let Divrow = document.createElement('div');
				Divrow.setAttribute('class','removecategory');
				let selectlabel =  document.createElement('label');
				selectlabel.setAttribute('class','form-label')
				selectlabel.innerText = "Projects";
				let selecthSelectTag =  document.createElement('select');
				selecthSelectTag.setAttribute('class','form-control select2-show-search');
				selecthSelectTag.setAttribute('id', 'subcategory');
				selecthSelectTag.setAttribute('name', 'project');
				selecthSelectTag.setAttribute('data-placeholder','Select Projects');
				let selectoption = document.createElement('option');
				selectoption.setAttribute('label','Select Projects')
				selectDiv.append(Divrow);
				Divrow.append(selectlabel);
				Divrow.append(selecthSelectTag);
				selecthSelectTag.append(selectoption);
				//
				$('.select2-show-search').select2();
				$.each(data.subcategories,function(index,subcategory){
				$('#subcategory').append('<option value="'+subcategory.name+'">'+subcategory.name+'</option>');
				})
			}
			else{
				$('#subcategory')?.empty();
				$('#selectSubCategory .removecategory')?.remove();
			}
		}
	})
});


// category submit form
$('body').on('submit', '#sprukocategory_form', function(e){
	e.preventDefault();
	var actionType = $('#pribtnsave').val();
	var fewSeconds = 2;
	$('#btnsave').html('Sending..');
	var formData = new FormData(this);
	$.ajax({
		type:'POST',
		url: SITEURL + "/admin/category/change",
		data: formData,
		cache:false,
		contentType: false,
		processData: false,

		success: (data) => {
			$('#CategoryError').html('');
			$('#sprukocategory_form').trigger("reset");
			$('#addcategory').modal('hide');
			$('#pribtnsave').html('<?php echo e(lang('Save Changes')); ?>');
			toastr.success(data.success);
			window.location.reload();


		},
		error: function(data){
			$('#CategoryError').html('');
			$('#CategoryError').html(data.responseJSON.errors.category);
			$('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
		}
	});
})


<?php $module = Module::all(); ?>

<?php if(in_array('Uhelpupdate', $module)): ?>


// Purchase Code Validation
$("body").on('keyup', '#envato_id', function() {
	let value = $(this).val();
	if (value != '') {
		if(value.length == '36'){
			var _token = $('input[name="_token"]').val();
			$.ajax({
				url: "<?php echo e(route('guest.envatoverify')); ?>",
				method: "POST",
				data: {data: value, _token: _token},

				dataType:"json",

				success: function (data) {
					if(data.valid == 'true'){
						$('#envato_id').addClass('is-valid');
						$('#envato_id').attr('readonly', true);
						$('.sprukoapiblock').removeAttr('disabled');
						$('#envato_id').css('border', '1px solid #02f577');
						$('#envato_support').val('Supported');
						toastr.success(data.message);
					}

					if(data.valid == 'expried'){
						<?php if(setting('ENVATO_EXPIRED_BLOCK') == 'on'): ?>
						$('#envato_id').addClass('is-invalid');
						$('.sprukoapiblock').attr('disabled', true);
						$('#envato_id').css('border', '1px solid #e13a3a');
						$('#envato_support').val('Expired');
						toastr.error(data.message);
						<?php endif; ?>
						<?php if(setting('ENVATO_EXPIRED_BLOCK') == 'off'): ?>
						$('#envato_id').addClass('is-valid');
						$('#envato_id').attr('readonly', true);
						$('.sprukoapiblock').removeAttr('disabled');
						$('#envato_id').css('border', '1px solid #02f577');
						$('#envato_support').val('Expired');
						toastr.warning(data.message);
						<?php endif; ?>

					}

					if(data.valid == 'false'){
						$('.sprukoapiblock').attr('disabled', true);
						$('#envato_id').css('border', '1px solid #e13a3a');
						toastr.error(data.message);
					}


				},
				error: function (data) {

				}
			});
		}
	}else{
		toastr.error('<?php echo e(lang('Purchase Code field is Required', 'alerts')); ?>');
		$('.purchasecode').attr('disabled', true);
		$('#envato_id').css('border', '1px solid #e13a3a');
	}
});

<?php endif; ?>

// delete note dunction
function deletePost(event) {
	var id  = $(event).data("id");
	let _url = `<?php echo e(url('/admin/ticketnote/delete/${id}')); ?>`;

	let _token   = $('meta[name="csrf-token"]').attr('content');

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
				url: _url,
				type: 'DELETE',
				data: {
				_token: _token
				},
				success: function(response) {
					toastr.success(response.success);
					$("#ticketnote_"+id).remove();
				},
				error: function (data) {
					console.log('Error:', data);
				}
			});
		}
	});
}

// Scrolling Js Start
var page = 1;
$(window).scroll(function() {
	if($(window).scrollTop() + $(window).height() >= $(document).height()) {
		page++;
		loadMoreData(page);
	}
});

function loadMoreData(page){
	$.ajax(
	{
		url: '?page=' + page,
		type: "get",

	})
	.done(function(data)
	{
		$("#spruko_loaddata").append(data.html);
		console.log(data.html);
	})
	.fail(function(jqXHR, ajaxOptions, thrownError)
	{
		alert('server not responding...');
	});
}

// End Scrolling Js

// ReadMore JS
let readMore = document.querySelectorAll('.readmores')
readMore.forEach(( element, index)=>{
	if(element.clientHeight <= 200)    {
		element.children[0].classList.add('end')
	}
	else{
		element.children[0].classList.add('readMore')
	}
})
$(`.readMore`).showmore({
	closedHeight: 300,
	buttonTextMore: 'Read More',
	buttonTextLess: 'Read Less',
	buttonCssClass: 'showmore-button',
	animationSpeed: 0.5
});
// ReadMore Js End

let ctmername =  <?php echo json_encode($ticket->cust->username); ?>;

let encryptpchase =  <?php echo json_encode($ticket->purchasecode); ?>;

let pchase
if(encryptpchase != null){
	let exampletext = ` json_encode(($ticket->purchasecode)) !!}`
	const newFirstElement = '{!'
	let exampletext2 =  '!'+exampletext.slice(0, 13)+'decrypt'+exampletext.slice(13, 41);
	pchase = newFirstElement + exampletext2;

}

if(pchase != null && pchase != 'undefined'){
	$.ajax({
		url:"<?php echo e(route('admin.ticketlicenseverify')); ?>",
		type:"POST",
		data: {
			envatopurchase_id: encryptpchase
		},
		success:function (data) {
			if(data?.client){
				if(data?.client?.trim() === ctmername.trim()){
					$('#custmermismatch').addClass("d-none");
				}else{
					$('#custmermismatch').removeClass("d-none");
				}
			}
		},
		error:function(data){
			// $('#purchasedata').html('');
		}

	});
}


$('body').on('click', '#purchasverified, #reverttowrong', function()
{
	var _id = $(this).data('id');

	console.log(_id);

	$.ajax({
		url:"<?php echo e(route('purchasedetailsverify')); ?>",
		type:"get",
		data: {
			id: _id
		},
		success:function (data) {
			toastr.success(data.success);
			location.reload();
		},
		error:function(data){
			// $('#purchasedata').html('');
		}

	});
});

$('body').on('click', '#wrongcustomer, #reverttoverify', function()
{
	var _id = $(this).data('id');

	console.log(_id);

	$.ajax({
		url:"<?php echo e(route('wrongcustomer')); ?>",
		type:"get",
		data: {
			id: _id
		},
		success:function (data) {
			toastr.success(data.success);
			location.reload();
		},
		error:function(data){
			// $('#purchasedata').html('');
		}

	});
});

$('body').on('click', '#purchasecodebtn', function()
{
	var envatopurchase_id = $(this).data('id');

	<?php if(!empty(Auth::user()->getRoleNames()[0]) && Auth::user()->getRoleNames()[0] == 'superadmin'): ?>
	var envatopurchase_i = envatopurchase_id;

	<?php else: ?>
		<?php if(setting('purchasecode_on') == 'on'): ?>
		var envatopurchase_i = envatopurchase_id;
		<?php else: ?>
		var trailingCharsIntactCount = 4;

		var envatopurchase_i = new Array(envatopurchase_id.length - trailingCharsIntactCount + 1).join('*') + envatopurchase_id.slice( -trailingCharsIntactCount);
		<?php endif; ?>
	<?php endif; ?>



	$('.modal-title').html('Purchase Details');
	$('.purchasecode').html(envatopurchase_i);
	$('#addpurchasecode').modal('show');
	$('#purchasedata').html('');

	$.ajax({
		url:"<?php echo e(route('admin.ticketlicenseverify')); ?>",
		type:"POST",
		data: {
			envatopurchase_id: envatopurchase_id
		},
		success:function (data) {
			$('#purchasedata').html(data.output);
		},
		error:function(data){
			$('#purchasedata').html('');
		}

	});
});

// Canned Maessage Select2
$('.cannedmessage').select2({
	minimumResultsForSearch: '',
	placeholder: "Search",
	width: '100%'
});
var cannedMsg=<?php echo json_encode($cannedmessages);?>


$('.select2').on('click', () => {
    let selectField = document.querySelectorAll('.select2-search__field')
    selectField.forEach((element,index)=>{
        element?.focus();
    })
});

// On Change Canned Messages display
$('body').on('change', '#cannedmessagess', function(){
	let optval = $(this).val();
	console.log(optval);
	$('.note-editable').html(cannedMsg[optval].messages);
	$('.summernote').html(cannedMsg[optval].messages);
	$('#btnsprukodisable').attr ('disabled', false);
});

$('#btnsprukodisable').attr('disabled', true);

$('.sprukostatuschange').on('click', function(e){
	$(e.target).prop("checked", false)
	if(this){
			$(this).prop("checked", true);
		}else{
			$(this).prop("checked", false);
		}
	if(e.target.value == 'On-Hold')
	{


		let teatareasecond = $('#holdremove textarea').val();
		if(teatareasecond == '')
		{
			$('#btnsprukodisable').attr('disabled', true);
		}else{
			$('#btnsprukodisable').attr('disabled', false);
		}
	}else{
		if($('#summernoteempty').val() == '')
		{
			$('#btnsprukodisable').attr('disabled', true);
		}else{
			$('#btnsprukodisable').attr('disabled', false);
		}
	}
});

$('.summernote').summernote({
	placeholder: '',
	tabsize: 1,
	height: 200,
	toolbar: [['style', ['style']], ['font', ['bold', 'underline', 'clear']], // ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
	['fontname', ['fontname']], ['fontsize', ['fontsize']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph']], // ['height', ['height']],
	['table', ['table']], ['insert', ['link', 'picture', 'video']], ['view', ['fullscreen']], ['help', ['help']]],
});

$('.summernote').on('summernote.keyup summernote.keydown', function(we, e) {
	if((e.target.value == '') || $('.summernote').val() == ''){
		$('#btnsprukodisable').attr ('disabled', true);
	}else{
		$('#btnsprukodisable').attr('disabled', false);
	}
});


if(ticket_status != 'Closed' && ticket_status != 'Suspend'){
	let ticketId = <?php echo json_encode($ticket->id); ?>;

	let dataEntry = document.querySelector('.note-editable')

	dataEntry.addEventListener('blur', function(){
	console.log('blur');
	let userid = <?php echo json_encode(Auth::user()->id); ?>;

	$.ajax({
	method:'POST',
	url: SITEURL + "/admin/employeesreplyingremove/",
	data: {
		userID : userid,
		ticketId : ticketId,
	},
	});
	})
	dataEntry.addEventListener('focus', function(){
		console.log('focus');

	let userid = <?php echo json_encode(Auth::user()->id); ?>;

	$.ajax({
	method:'POST',
	url: SITEURL + "/admin/employeesreplyingstore/",
	data: {
	userID : userid,
	ticketId : ticketId,
	},
	});
	})

	setInterval(() => {
    $.ajax({
    method:'GET',
    url: SITEURL + "/admin/getemployeesreplying/"+ticketId,
    success : function (data) {
    let replyStatus = document.querySelector('#replyStatus');
    replyStatus.innerHTML = '';
    // replyStatus.innerText = data;
    if(data['employees'].length ){
    let mainCard = document.createElement('div');
    mainCard.classList.add('d-flex','gap-2', 'mt-sm-0', 'mt-3');

    let divCard = document.createElement('div');
    divCard.classList.add('px-1','py-1','d-flex','align-items-center','border','rounded-pill');
    let avatar;
    if (data['employees'][0].image == null){
    avatar = `<span class="avatar  brround border border-success avatar-typing-active ms-3" style="background-image: url(../../uploads/profile/user-profile.png)"></span>`
    }else{
    avatar = `<span class="avatar  brround border border-success avatar-typing-active" style="background-image: url(../../uploads/profile/${data['employees'][0].image})"></span>`
    }
    let icon = document.createElement('span');

    icon.classList.add('avatar','brround','me-0','bg-transparent')
    icon.style.backgroundImage = 'url(../../assets/images/typing.gif)'
    let p = document.createElement('p');
    p.classList.add('font-weight-semibold', 'd-block','d-sm-flex','my-auto');
	let span = document.createElement('span');
    span.classList.add('font-weight-semibold','text-nowrap');
    span.textContent = data['employees'][0].name;
    // let small = document.createElement('small');
    // small.classList.add('text-muted','ms-1','text-nowrap');
    // small.textContent = 'Working on it';
    p.append(span);
    // p.append(small);
    divCard.append(icon);
    divCard.append(p);
    divCard.insertAdjacentHTML('beforeend' ,avatar);
    mainCard.append(divCard);

    let divCardStacked = document.createElement('div');
    divCardStacked.classList.add('px-1','d-flex','align-items-center','rounded-pill','avatar-list','avatar-list-stacked');
    let spanCount;

    data['employees'].forEach(function(emp, i){
    if(i != 0){
    let avatar;
    if (data['employees'][0].image == null){
    avatar = `<span class="avatar  brround" style="background-image: url(../../uploads/profile/user-profile.png)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${emp['name']}"></span>`
    }else{
    avatar = `<span class="avatar  brround" style="background-image: url(../../uploads/profile/${emp.image})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="${emp['name']}"></span>`
    }

    divCardStacked.insertAdjacentHTML('beforeend' ,avatar);
    }
    mainCard.append(divCardStacked);
    })

    replyStatus.append(mainCard);
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    };
    }
    });

    }, 1000);


//store the data of textarea on local storage
$('.note-editable').on('keyup', function(e){
localStorage.setItem(`usermessage${ticketId}`, e.target.innerHTML)
})

$(window).on('load', function(){
document.querySelector(".note-editable p").remove();
if(localStorage.getItem(`usermessage${ticketId}`) == '' || localStorage.getItem(`usermessage${ticketId}`) == null || localStorage.getItem(`usermessage${ticketId}`) == undefined){
document.querySelector(".note-editable").innerHTML = document.querySelector(".note-editable").innerHTML
$('#btnsprukodisable').attr ('disabled', true);
}else{
document.querySelector(".note-editable").innerHTML += localStorage.getItem(`usermessage${ticketId}`)
$('#btnsprukodisable').attr ('disabled', false);
}
});

$('.deletelocalstorage').click(function(){
localStorage.removeItem(`usermessage${ticketId}`)
});

$('body').on('keyup keydown', '#holdremove textarea', function(e){
	if((e.target.value == '') || $('.summernote').val() == ''){
		$('#btnsprukodisable').attr ('disabled', true);
	}else{
		$('#btnsprukodisable').attr('disabled', false);
	}

})
}

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
					location.replace('<?php echo e(route('admin.dashboard')); ?>');
				},
				error: function (data) {
					console.log('Error:', data);
				}
			});
		}
	});

});
// TICKET DELETE SCRIPT END

let suspend = document.getElementById('suspend'),
	unsuspend = document.getElementById('unsuspend');
/*** Suspend Ticket ***/
	if(suspend != null){

		suspend.addEventListener('click', function(event){
			event.preventDefault();

			const ticket_id = suspend.getAttribute('data-id');

			swal({
				title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
				text: "<?php echo e(lang('This might suspend the ticket permanently', 'alerts')); ?>",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) {
					let xhr = new XMLHttpRequest();
					let url = "<?php echo e(route('admin.suspend')); ?>";
					let _token   = $('meta[name="csrf-token"]').attr('content');
					xhr.open('POST', url, true);
					xhr.setRequestHeader("Content-Type", "application/json");

					// Create a state change callback
					xhr.onreadystatechange = function () {
						if (xhr.readyState === 4 && xhr.status === 200) {
							let data = JSON.parse(this.responseText)
							// Print received data from server
							toastr.success(data.success)
							location.reload();

						}

					};

					// Converting JSON data to string
					var data = JSON.stringify({ "ticket_id": ticket_id, "_token": _token});

					// Sending data with the request
					xhr.send(data);
				}
			});

		})
	}
/*** End Suspend Ticket ***/
/*** UnSuspend Ticket ***/
	if(unsuspend != null)
	{
		unsuspend.addEventListener('click', function(event){
			event.preventDefault();

			const ticket_id = unsuspend.getAttribute('data-id')
				unsuspend = 'Inprogress';

			swal({
				title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
				text: "<?php echo e(lang('This action may remove the ticket from suspension.', 'alerts')); ?>",
				icon: "warning",
				buttons: true,
				dangerMode: true,
			})
			.then((willDelete) => {
				if (willDelete) {
					let xhr = new XMLHttpRequest();
					let url = "<?php echo e(route('admin.suspend')); ?>";
					let _token   = $('meta[name="csrf-token"]').attr('content');
					xhr.open('POST', url, true);
					xhr.setRequestHeader("Content-Type", "application/json");

					// Create a state change callback
					xhr.onreadystatechange = function () {
						if (xhr.readyState === 4 && xhr.status === 200) {
							let data = JSON.parse(this.responseText)
							// Print received data from server
							toastr.success(data.success)
							location.reload();

						}

					};

					// Converting JSON data to string
					var data = JSON.stringify({ "ticket_id": ticket_id, "unsuspend": unsuspend, "_token": _token});

					// Sending data with the request
					xhr.send(data);
				}
			});

		})
	}
/*** End UnSuspend Ticket ***/

//** Chat GPT modal script**//
$('body').on('click', '#gptmodal', function(){
        $('#spk_gpt').val('');
        $('#answershow').html('');
        $('#addgptmodal').modal('show');
        document.getElementById("copytoresponse-gpt").classList.add("d-none")
    });

</script>

<script type="module">
    import openai from "<?php echo e(asset('assets/js/openapi/openapi.min.js')); ?>"

    if(document.getElementById("priority_form1")){
        //Chat GPT
        document.getElementById("priority_form1").disabled = true

        var i

        if(document.getElementById("spk_gpt")){
            ['click','keyup'].forEach( evt =>
                document.getElementById("spk_gpt").addEventListener(evt,(ele)=>{
                    i = ele.target.value
                    if(i.length){
                        document.getElementById("priority_form1").disabled = false
                    }
                    else{
                        document.getElementById("priority_form1").disabled = true
                    }
                })
            );

        }



        document.getElementById("priority_form1").onclick = ()=>{
            if(i){
                // copytoresponse button
                document.getElementById("copytoresponse-gpt").classList.add("d-none")
                // End copytoresponse button

                // Loader
                document.getElementById("loader-gpt").classList.remove("d-none")
                //End Loader


                // text area

                //Adding the text area
                document.getElementById("answershow").innerText  = ""
                //End Adding the text area

                var aaaa;
                aaaa = <?php echo json_encode(setting('openai_api')); ?>


                const configuration = new openai.Configuration({
                    apiKey: aaaa,
                });
                const Openai = new openai.OpenAIApi(configuration);
                const main = async ()=>{
                    const question = i
                    const completion = await Openai.createCompletion({
                        model: "text-davinci-003",
                        prompt: question,
                        max_tokens:2048,
                        temperature:1
                    })
                    return completion.data.choices[0].text
                }
                let responce = main()

                responce.then((data)=>{

                    // Loader
                    document.getElementById("loader-gpt").classList.add("d-none")
                    //End Loader

                    //Adding the text area

                    document.getElementById("answershow").innerText  = data

                    // Adding the Copy Response
                    document.getElementById("copytoresponse-gpt").classList.remove("d-none")

                    //copytoresponse Button event
                    document.getElementById("copytoresponse-gpt").addEventListener("click",()=>{
                        document.querySelector(".note-editable").innerText = `${data}`
                        document.querySelector("#description").innerHTML = `${data}`
                    })
                })

                responce.catch((error)=>{
                    console.log(error?.response.data.error.message);

                    //To add The Error Message
                    document.getElementById("error-message-gpt").innerText = error?.response.data.error.message
                    //End To add The Error Message


                    // Loader
                    document.getElementById("loader-gpt").classList.add("d-none")
                    //End Loader

                    // copytoresponse button
                    document.getElementById("copytoresponse-gpt").classList.add("d-none")
                    // End copytoresponse button

                })
            }
            else{
                toastr.error('Please enter somthing!');
                document.getElementById("priority_form1").disabled = true
            }
        }
    }

</script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>

<?php if(setting('enable_gpt') == 'on'): ?>
    <!-- GPT Model-->
    <div class="modal fade"  id="addgptmodal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(lang('Ask Chat GPT')); ?></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                    <?php echo csrf_field(); ?>
                    <?php echo view('honeypot::honeypotFormFields'); ?>
                    <div class="modal-body pb-0 position-relative">
                        <div class="form-group">
                            <label class="form-label"><?php echo e(lang('Type your query here')); ?></label>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" placeholder="Enter Here" name="" id="spk_gpt">
                                <input type="button" class="btn btn-secondary" id="priority_form1"  value="<?php echo e(lang('Generate Text')); ?>">
                            </div>
                            <span class="invalid-feedback d-block mt-4" role="alert">
                                <strong id="error-message-gpt"></strong>
                            </span>
                        </div>
                    </div>

                    <div class="gap-2 mt-sm-0 mt-3 me-2 position-absolute open-pen d-none" id="loader-gpt">
                        <div class="px-1 py-1 d-flex align-items-center">
                            <span class="avatar text-muted me-0 bg-transparent" style="background-image: url(&quot;<?php echo e(asset('assets/images/typing.gif')); ?>&quot;);">
                            </span>
                        </div>
                    </div>

                    <div class="modal-body pt-0">
                        <div class="form-group" id="main-gpt">
                            <div id="textares-gpt">
                                <label class="form-label"><?php echo e(lang('Generated text')); ?></label>
                                <div class="" >
                                    <div class="form-control openanswer" name="" id="answershow" ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="ms-0 btn btn-primary d-none" name="" id="copytoresponse-gpt" data-bs-dismiss="modal" value="<?php echo e(lang('Copy Response')); ?>">
                        <a href="#" class="btn btn-outline-danger" data-bs-dismiss="modal"><?php echo e(lang('Close')); ?></a>
                    </div>
            </div>
        </div>
    </div>
    <!-- GPT Model end -->
<?php endif; ?>

<!-- Add note-->
<div class="modal fade"  id="addnote" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" ></h5>
				<button  class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form method="POST" enctype="multipart/form-data" id="note_form" name="note_form">
				<input type="hidden" name="ticket_id" id="ticket_id">
				<?php echo csrf_field(); ?>
				<?php echo view('honeypot::honeypotFormFields'); ?>
				<div class="modal-body">

					<div class="form-group">
						<label class="form-label"><?php echo e(lang('Note:')); ?></label>
						<textarea class="form-control" rows="4" name="ticketnote" id="note" required></textarea>
						<span id="noteError" class="text-danger alert-message"></span>
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
<!-- End  Add note  -->

<?php echo $__env->make('admin.modalpopup.assignmodal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- End Assigned Tickets  -->

<!-- Priority Tickets-->
<div class="modal fade sprukopriority"  id="addpriority" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" ></h5>
				<button  class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<form method="POST" enctype="multipart/form-data" id="priority_form" name="priority_form">
				<?php echo csrf_field(); ?>
				<?php echo view('honeypot::honeypotFormFields'); ?>
				<input type="hidden" name="priority_id" id="priority_id" value="<?php echo e($ticket->id); ?>">
				<?php echo csrf_field(); ?>
				<div class="modal-body">

					<div class="custom-controls-stacked d-md-flex" >
						<select class="form-control select2_modalpriority" data-placeholder="<?php echo e(lang('Select Priority')); ?>" name="priority_user_id" id="priority" >
							<option label="<?php echo e(lang('Select Priority')); ?>"></option>
							<option value="Low" <?php echo e(($ticket->priority == 'Low')? 'selected' :''); ?>><?php echo e(lang('Low')); ?></option>
							<option value="Medium" <?php echo e(($ticket->priority == 'Medium')? 'selected' :''); ?>><?php echo e(lang('Medium')); ?></option>
							<option value="High" <?php echo e(($ticket->priority == 'High')? 'selected' :''); ?>><?php echo e(lang('High')); ?></option>
							<option value="Critical" <?php echo e(($ticket->priority == 'Critical')? 'selected' :''); ?>><?php echo e(lang('Critical')); ?></option>
						</select>
					</div>
					<span id="PriorityError" class="text-danger"></span>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-secondary" id="pribtnsave" ><?php echo e(lang('Save')); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End priority Tickets  -->

<?php echo $__env->make('admin.viewticket.modalpopup.categorymodalpopup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<!-- PurchaseCode Modals -->
<div class="modal fade sprukopurchasecode"  id="addpurchasecode" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" ></h5>
				<button  class="close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<input type="hidden" name="purchasecode_id" id="purchasecode_id" value="">
			<div class="modal-body">
				<div class="mb-4">
					<!-- <strong><?php echo e(lang('Puchase Code')); ?> :</strong>
					<span class="purchasecode"></span> -->
				</div>
				<div id="purchasedata">

				</div>
			</div>
		</div>
	</div>
</div>
<!-- End PurchaseCode Modals   -->

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.adminmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/viewticket/showticket.blade.php ENDPATH**/ ?>