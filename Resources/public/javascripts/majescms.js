CmsAdmin = {}

CmsAdmin.Common = {
	init: function(){

		$("a.duplicateTranslation").click(function(e){
			var self = $(this);
			e.preventDefault();
			$('#duplicateTranslationModal').modal('show');

			$("#duplicateTranslationModal a.copy").attr('href', self.attr('href')+'?copy=1');
			$("#duplicateTranslationModal a.scratch").attr('href', self.attr('href'));
		});
		
		$("a.deleteBlock").click(function(e){
	            e.preventDefault();
	
	            if(confirm('Are you sure you want to delete this element?')){
	                event.preventDefault();
	
	            var page_template_block_id = $(event.target).data('pagetemplateblock');
	            var page_id = $(event.target).data('page');
	            var template_block_id = $(event.target).data('templateblock');
	            var id = $(event.target).data('id');
	            var lang = $(event.target).data('lang');
	            $.ajax({
	                url: $(event.target).attr('href'),
	                type: 'POST',
	                dataType: "json",
	                data: {wysiwyg: 0, id: id, page_template_block_id: page_template_block_id, page_id: page_id, template_block_id: template_block_id, lang: lang},
	                success: function(response){
	                     if(response.success){
	                     	location.reload(true);
	                     }else{
	                     	return false;
	                     }
	                }
	            });
	            }else{
	                return false;
	            }
	            
	        });
		

	}
}

$(document).ready(function(){
	CmsAdmin.Common.init();
});
