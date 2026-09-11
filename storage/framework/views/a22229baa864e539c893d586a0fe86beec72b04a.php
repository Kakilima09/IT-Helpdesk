						<!--Section-->
						<section class="">
							<footer class="text-white footer-support">

								<div class="sub-footer">
									<div class="container">
										<div class="row d-flex align-items-center">
											<div class="col-lg-6 col-sm-12 pt-5 pd-b-md-2 pb-5 text-lg-start text-md-center">
												<?php echo $footertext->copyright; ?>

											</div>
											<div class="col-lg-6 col-sm-12 pt-5 pd-t-md-2 pb-5 text-lg-end text-md-center">
												<div class="">
													<nav>
														<ul class="custom-ul">
															<?php if(setting('FAQ_ENABLE') == 'yes'): ?>

																<li><a href="<?php echo e(url('/faq')); ?>"><?php echo e(lang('FAQ’s', 'menu')); ?></a></li>
															<?php endif; ?>
															<?php if(setting('CONTACT_ENABLE') == 'yes'): ?>

																<li><a href="<?php echo e(url('/contact-us')); ?>"><?php echo e(lang('Contact Us', 'menu')); ?></a></li>
															<?php endif; ?>
															<?php $__currentLoopData = $page; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<?php if($pages->status == '1'): ?>
																	<?php if($pages->viewonpages == 'both' || $pages->viewonpages == 'footer'): ?>

																		<li>
																			<a href="<?php echo e(url('page/' .$pages->pageslug)); ?>"><?php echo e($pages->pagename); ?></a>
																		</li>

																	<?php endif; ?>
																<?php endif; ?>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


														</ul>
													</nav>
												</div>
											</div>
										</div>
									</div>
								</div>
							</footer>
						</section>
						<!--Section--><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/footer.blade.php ENDPATH**/ ?>