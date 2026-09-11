<!-- Back to top -->
<a href="#top" id="back-to-top"><span class="feather feather-chevrons-up"></span></a>


<!--Moment js-->
<script src="<?php echo e(asset('assets/plugins/moment/moment.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- Bootstrap4 js-->
<script src="<?php echo e(asset('assets/plugins/bootstrap/popper.min.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>?v=<?php echo time(); ?>"></script>


<!-- P-scroll js-->
<script src="<?php echo e(asset('assets/plugins/p-scrollbar/p-scrollbar.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- Select2 js -->
<script src="<?php echo e(asset('assets/plugins/select2/select2.full.min.js')); ?>?v=<?php echo time(); ?>"></script>

<!--INTERNAL Horizontalmenu js -->
<script src="<?php echo e(asset('assets/plugins/horizontal-menu/horizontal-menu.js')); ?>?v=<?php echo time(); ?>"></script>

<!--INTERNAL Sticky js -->
<script src="<?php echo e(asset('assets/plugins/sticky/sticky2.js')); ?>?v=<?php echo time(); ?>"></script>

<?php echo $__env->yieldContent('scripts'); ?>

<!--INTERNAL Toastr js -->
<script src="<?php echo e(asset('assets/plugins/toastr/toastr.min.js')); ?>?v=<?php echo time(); ?>"></script>

<!--INTERNAL sweetalert js -->
<script src="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.min.js')); ?>?v=<?php echo time(); ?>"></script>


<script type="text/javascript">

	"use strict";

	// Custom js
	<?php echo customcssjs('CUSTOMJS') ?>

	<?php if(auth()->guard()->guest()): ?>
	<?php else: ?>
	<?php if(Auth::guard('customer')->check() && Auth::guard('customer')->user()->image != null): ?>

	// Mobile Menu Toggle
	$(document).ready(function() {
		// Toggle menu saat tombol hamburger diklik
		$('.horizontal-navtoggle').on('click', function(e) {
			e.preventDefault();
			$('.horizontalMenu-list').toggleClass('open');
			$(this).toggleClass('active');
		});

		// Tutup menu saat klik di luar
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.horizontalMenu').length) {
				$('.horizontalMenu-list').removeClass('open');
				$('.horizontal-navtoggle').removeClass('active');
			}
		});
	});

	// Remove Image
	var SITEURL = '<?php echo e(url('')); ?>';
	function deletePost(event) {
		var id  = $(event).data("id");
		let _url = `<?php echo e(url('/customer/image/remove/${id}')); ?>`;

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
					location.reload();
				},
				error: function (data) {
					console.log('Error:', data);
							}
				});
			}
		});
	}

	<?php endif; ?>
	<?php if(auth()->guard('customer')->user()): ?>

	let playAudio = ()=>{
		let audio = new Audio();
		audio.src = "../assets/sounds/norifysound.mp3";
		audio.load();
		audio.play();
	}

	setInterval( function() {

		$.ajax({
			url: "<?php echo e(route('customer.update.notificationalerts')); ?>",
			type: 'GET',
			dataType: 'json',
			success: function(res) {
				$.map( res, function( value, index ) {
					if(index === 0){
						toastr.success('<?php echo e(lang("You have")); ?> '+ res.length +' <?php echo e(lang("new notification")); ?>');
						readnotify();
						playAudio();
					}
				});
			}
		});

		notificationsreading();
		badgecount();
		markasreadcount();
	}, 5000);
	function readnotify(){

		$.ajax({
			url: "<?php echo e(route('customer.update.notificationalertsread')); ?>",
			type: 'post',
			dataType: 'json',
			success: function(res) {

			}
		});
	}

	$('.notifyreading').load('<?php echo e(route('customer.notificationsreading')); ?>')

	function notificationsreading()
	{

		$('.notifyreading').load('<?php echo e(route('customer.notificationsreading')); ?>')

	}
	function badgecount()
	{

		$('.badgecount').load('<?php echo e(route('customer.badgecount')); ?>')

	}
	function markasreadcount()
	{


		$('.markasreadcount').load('<?php echo e(route('customer.markasreadcount')); ?>')

	}

	// Mark as Read
	function sendMarkRequest(id = null) {
		return $.ajax("<?php echo e(route('customer.markNotification')); ?>", {
			method: 'GET',
			data: {
				id
			}
		});
	}
	(function($) {
		$('.mark-as-read').on('click', function() {
			let request = sendMarkRequest($(this).data('id'));
			request.done(() => {
				$(this).parents('div.alert').remove();
			});
		});
		$('.cmark-all').on('click', function() {
			let request = sendMarkRequest();
			request.done(() => {
				$('div.alert').remove();
			})
		});

		$('body').on('click', '.mark-read', function(e){
			e.preventDefault();
			$.ajax({
				type: "POST",
				url: '<?php echo e(route('customer.notify.markallread')); ?>',
				success: function (data) {
					notificationsreading();
					badgecount();
					// location.reload();
				},
				error: function (data) {
				console.log('Error:', data);
				}
			});
		});


	})(jQuery);
	<?php endif; ?>

	<?php endif; ?>

</script>

<!-- Custom html js-->
<script src="<?php echo e(asset('assets/js/custom.js')); ?>?v=<?php echo time(); ?>"></script>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/scripts.blade.php ENDPATH**/ ?>