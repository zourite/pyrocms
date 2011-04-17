(function($){
	$(function(){
		// Pick a rule type, show the correct field
		$('input[name="attachment_type"]').change(function(){
			$('#attachment-' + $(this).val())

			// Show only the selected type
			.show().siblings().hide()

			// Reset values when switched
			.find(':input:not([value="http://"])').val('');

		// Trigger default checked
		}).filter(':checked').change();

		var attachments_key = $('input[name=attachments_key]').val();

		$('input[value="http://"]').data('default_value', 'http://').bind('keyup blur', $.debounce(350, function(e){
			var self = $(this);

			if (e.type == 'blur' && ! self.val().length)
			{
				self.val(self.data('default_value'));
			}
			else if (e.type == 'blur' || self.val().length > 11)
			{
				self[((self.val().indexOf('://') == -1) ? 'add' : 'remove')+'Class']('error');
			}
		}));

		$('#attachment-link .button.attach').click(function(e){
			e.preventDefault();

			var button	= $(this),
				url		= button.attr('href'),
				prefix	= 'input[name=attachment_link_',
				data	= {
					attachments_key	: attachments_key,
					link_url		: $(prefix + 'url]').val(),
					link_title		: $(prefix + 'title]').val(),
					link_class		: $(prefix + 'class]').val()
				}

			$.post(url, data, function(data){
				if (data.status == 'success')
				{
					// TODO: Create attachment item and append to attachments list
				}
				else if (data.status == 'error')
				{
					// TODO: Display inline error notification
				}
			}, 'json');
		});
	});
})(jQuery);