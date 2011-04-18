(function($){
	$(function(){

		pyro.clear_notifications = function(){
			$('.notification .close').click();
			return pyro;
		};
		pyro.add_notification = function(notification, append){
			if ( ! append)
			{
				pyro.clear_notifications();
			}
			$('#shortcuts').after(notification);
			return pyro;
		};
		pyro.attachments = {
			$list			: $('#attachments-list'),
			$empty			: $('#attachments-list > li.empty'),
			$attachments	: $('#attachments-list > li:not(.tmpl, .empty)'),

			tmpl: '',

			init: function(){
				pyro.attachments.tmpl = $('<div />').html(pyro.attachments.$list.children('.tmpl').hide().removeClass('tmpl')).html();
			},
			add_attachment: function(data){
				var attachment = pyro.attachments.tmpl
					.replace('{id}', data.id)
					.replace('{title}', data.title)
					.replace('{type}', data.type),

				$attachemnt = $(attachment)
					.appendTo(pyro.attachments.$list);

				pyro.attachments.$attachments.add($attachemnt);

				if (pyro.attachments.$empty.is(':hidden'))
				{
					$attachemnt.fadeIn('fast');
				}
				else
				{
					pyro.attachments.$empty
						.fadeOut()
						.slideUp(function(){
							$attachemnt.fadeIn('fast');
						});
				}

				return pyro.attachments;
			}
		};
		pyro.attachments.init();

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
				if (data && data.status == 'success')
				{
					pyro.add_notification(data.message)
						.attachments.add_attachment(data.attachment);
				}
				else if (data && data.status == 'error')
				{
					pyro.add_notification(data.message);
				}
			}, 'json');
		});
	});
})(jQuery);