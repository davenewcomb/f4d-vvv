var tcpShortcode;

(function($){
	var inputs = {}, ed;
	tcpShortcode = {
		init : function() {
			inputs.wrap = $('#tcp-wrap');
			inputs.backdrop = $('#tcp-backdrop');
			inputs.dialog = $('#tcp-dialog');
			inputs.close = $( '#tcp-close' );
			inputs.options = $('#tcp-dialog-options');
			inputs.submit = $('.tcp-dialog-submit');
			inputs.spinner = $('#tcp-dialog-update').find('.spinner');
			inputs.loading = false;
			inputs.submit.click( function(e){
				e.preventDefault();
				tcpShortcode.insert();
			});			
			inputs.close.add( inputs.backdrop ).add( '#tcp-dialog-cancel a' ).click( function( event ) {
				event.preventDefault();
				tcpShortcode.close();
			});		
		},		
		open : function() {
			if ( ! wpActiveEditor )
				return;

			inputs.wrap.show();
			inputs.backdrop.show();
		},
		close : function() {
			inputs.backdrop.hide();
			inputs.wrap.hide();			
		},
		compare : function( a, b ) {
			if( $(a).not(b).length === 0 && $(b).not(a).length === 0 )
				return true;
			else
				return false;		
		},
		parse : function() {
			var _this = this, fields = '', test = '', cLabels, get,
			defaults = JSON.parse( tcpsc.defaults );
			
			$('input', inputs.dialog).each(function(){
				if ( $(this).attr('id') && $(this).is(':checkbox') )
					fields += ' ' + $(this).attr('id') + '="' + $(this).prop("checked") + '"';
			
				else if ( $(this).attr('id') && $(this).not(':checkbox') && $(this).val() )
					fields += ' ' + $(this).attr('id') + '="' + $(this).val() + '"';
			});

			$('select', inputs.dialog).each(function(){
				if ( $(this).attr('id') && $(this).val())
					fields += ' ' + $(this).attr('id') + '="' + $(this).val() + '"';
			});
			
			$('textarea', inputs.dialog).each(function(){
				if ($(this).attr('id') && $(this).val())
					fields += ' ' + $(this).attr('id') + '="' + encodeURIComponent( $(this).val() ) + '"'; 
			});

			// Only add shortcode parameter if different with the defaul values
			$.each(['until', 'cLabels', 'cLabels1', 'compactLabels' ], function( i, e ) {
				get = $('[name="'+e+'[]"]').map(function() { return this.value; }).get();
				console.log( get );
				console.log( defaults[e] );
				if( ! _this.compare( defaults[e], get ) )
					fields += ' '+e+'="' + get.join(',') + '"';
			});							
			
			return fields;
		},
		insert : function() {
			var ed = tinymce.get( wpActiveEditor );
			
			shortcode = "[countdown" + this.parse() + "]";	

			if ( ed ) {
				tinymce.execCommand("mceBeginUndoLevel");
				tinymce.execCommand('mceInsertContent', false, shortcode);
				tinymce.execCommand("mceEndUndoLevel");
			}
			edInsertContent('', shortcode);
			tcpShortcode.close();
		},
		update : function() {
			if ( ! inputs.loading ) {
				inputs.loading = true;
				inputs.spinner.show();
				$.post( ajaxurl, { action: tcpsc.action, nonce: tcpsc.nonce, data:$(inputs.dialog).serialize() }, function( data ){
					inputs.spinner.hide();
					inputs.options.html(data);
					inputs.loading = false;
				});
			}
		},		
	}
	$(document).ready( tcpShortcode.init );
})(jQuery);