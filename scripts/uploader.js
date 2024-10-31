jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */

	$('.npbm_files_upload_container').each(function(){
		let $button = $('.npbm_files_upload', this);
		let $input = $button.next();
		let $link = $input.next();
		let $remove = $link.next();

		$button.click(function(e){
			e.preventDefault();
			let uploader = wp.media({
					title: 'Insert PDF',
					library : { type : 'application/pdf' },
					button: { text: 'Use this PDF' },
					multiple: false
				});

			uploader
				.on('select', function(){
					let attachment = uploader.state().get('selection').first().toJSON();
					$input.val(attachment.id);
					$link.show().attr('href', attachment.url).text(attachment.url);
					$remove.show();
				})
				.open();
		});

		/*
		 * Remove image event
		 */
		$remove.click( function(){
			$input.val('');
			$link.attr('href', '').text("").hide();
			$remove.hide();
			return false;
		});
	});

});