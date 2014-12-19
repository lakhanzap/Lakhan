window.dp = window.dp || {};

(function($) {

/* Custom Media Uploader
var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment;

$('.dp-upload-button').on('click', function(e) {
	e.preventDefault();
	
	var button = $(this),
		text = $(this).siblings('.dp-upload-text'),
		preview = $(this).siblings('.dp-upload-preview');
		
	_custom_media = true;
	wp.media.editor.send.attachment = function(props, attachment){
		if ( _custom_media ) {
			text.val(attachment.url);
			preview.html('<img src="'+attachment.url+'" />');
		} else {
			return _orig_send_attachment.apply( this, [props, attachment] );
		};
	}
	wp.media.editor.open(button);
	return false;
});

$('.add_media').on('click', function(){
	_custom_media = false;
}); */

dp.admin = {
	init: function(){
		var $this = this;
		
		$this.uploadMedia();
		$this.removeMedia();
		$this.toggleAll();
		$this.resetAll();
		$this.colorPicker();
		$this.sectionToggle();
		
		// Sortable List
		if($().sortable) {
			$('.dp-panel .sortable-list').sortable({
				cursor: 'move'
			});
		}
	},
	
	/* Custom Media Uploader */
	uploadMedia: function(){
		/* Custom Media Uploader, No Sidebar */
		var file_frame;
		var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
 
		$('.dp-panel').on('click', '.dp-upload-button', function(e){
			e.preventDefault();
	
			var $el = $(this),
				$post_id = $(this).data('post_id');
				text = $(this).siblings('.dp-upload-text'),
				preview = $(this).siblings('.dp-upload-preview');
	
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				// Set the post ID to what we want
				if($post_id)
					file_frame.uploader.uploader.param( 'post_id', $post_id );
				// Open frame
				file_frame.open();
				return;
			} else {
				// Set the wp.media post id so the uploader grabs the ID we want when initialised
				if($post_id)
					wp.media.model.settings.post.id = $post_id;
			}
 
			// Create the media frame.
			file_frame = wp.media.file_frame = wp.media({
				title: $el.data( 'choose' ),
				/* Tell the modal to show only images.
				library: {
					type: ['image']
				},*/
				button: {
					// Set the text of the button.
					text: $el.data('update'),
				},
				editing:   true,
				multiple: false
			});
 
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// console.log(attachment);
				if(file_frame.options.multiple) {
					var selected = [];
					var selection = file_frame.state().get('selection');
					selection.map(function(attachment) {
						attachment = attachment.toJSON();
						selected.push(attachment.url);
						// Do something else with attachment object
					});
					
					// console.log(selected.join(' '));
				} else {
					/* We set multiple to false so only get one image from the uploader*/
					attachment = file_frame.state().get('selection').first().toJSON();
	  
					text.val(attachment.url);
					preview.html('<img src="'+attachment.url+'" />');
				}
				
				// Restore the main post ID
				if($post_id)
					wp.media.model.settings.post.id = wp_media_post_id;
			});
 
			// Finally, open the modal
			file_frame.open();
		});
  
		// Restore the main ID when the add media button is pressed
		//$('a.add_media').on('click', function() {
			//wp.media.model.settings.post.id = wp_media_post_id;
		//});
	},
	
	removeMedia: function() {
		$('.dp-panel').on('click', ' .dp-remove-button', function(e){
			e.preventDefault();
			preview = $(this).siblings('.dp-upload-preview');
			text = $(this).siblings('.dp-upload-text');
			text.val('');
			preview.empty();
		});
	},
	
	// Toggle all postbox
	toggleAll: function() {
		$(".dp-panel .toggel-all").on('click', function(){
			if($(".postbox").hasClass("closed")) {
				$(".postbox").removeClass("closed");
			} else {
				$(".postbox").addClass("closed");
			};
			postboxes.save_state(pagenow);
				
			return false;
		});
	},
	
	// Reset all to defaults
	resetAll: function(){
		$('.dp-panel .reset').click(function(){
			if (confirm("Are you sure you want to reset to default options?")) { 
				return true;
			} else { 
				return false; 
			}
		});	
	},
	
	// Color Picker
	colorPicker: function() {
		$('.dp-panel .dp-color-handle').each(function(){
			current_color = $(this).next('.dp-color-input').attr('value');
			$(this).css('backgroundColor', current_color);
			var c = $(this).ColorPicker({
				color: $(this).next('.dp-color-input').attr('value'),
				onChange: function (hsb, hex, rgb, el) {
					$(c).css('backgroundColor', '#' + hex);
					$(c).next('.dp-color-input').attr('value', '#' + hex);
				}
			});
		});
	},
	
	// Section Toggle
	sectionToggle: function(){
		$('.dp-panel').on('click', '.handler .up', function(){
			var currentItem = $(this).parents('li'),
				prevItem = currentItem.prev('li');
				
			prevItem.before(currentItem);
		});
	
		$('.dp-panel').on('click', '.handler .down', function(){
			var currentItem = $(this).parents('li'),
				nextItem = currentItem.next('li');
			
			nextItem.after(currentItem);
		});
	
		$('.dp-panel').on('click', '.section-handlediv, .section-hndle', function(){
			$(this).parents('.section-box').find('.section-inside').toggle();
		});
	}
};

var detubeAdmin = function(){
	/* Color Scheme */
	$('#dp-color-scheme').change(function(){
			if($(this).val() == 'custom') {
				$('.in-color-scheme').parents('tr').show();
			} else {
				$('.in-color-scheme').parents('tr').hide();
			}
		}).change();
	
	/* Pattern Change */
	$('#dp-preset-bgpat').change(function(){
		var pat = $(this).val();
		if(pat != '')
			$('.dp-preset-bgpat-preivew').css('background', 'url('+pat+')');
	}).change();

	/* Logo Type */
	$('#dp-logo-type').change(function(){
		if($(this).val() == 'text') {
			$('#dp-logo').parents('tr').hide();
		} else {
			$('#dp-logo').parents('tr').show();
		}
	}).change();
};
	
$(document).ready(function($) {
	dp.admin.init();
	detubeAdmin();
});

})(jQuery);