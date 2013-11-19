Cms = {}

Cms.Common = {
	init: function(){

		$("a.editBlock").click(function(e){
    		e.preventDefault();
	
    		var page_template_block_id = $(event.target).data('pagetemplateblock');
    		var page_id = $(event.target).data('page');
    		var template_block_id = $(event.target).data('templateblock');
    		var id = $(event.target).data('id');
	
    		$.ajax({
    			url: $(this).attr('href'),
    			data: {wysiwyg: 1, id: id, page_template_block_id: page_template_block_id, page_id: page_id, template_block_id: template_block_id},
    			success: function(response){
    				$("#ajaxEditBlock").html(response);
    				$('#editModal').modal('show');
    			}
    		});
    		
    	});

        $("a.addBlock").click(function(e){
            e.preventDefault();
    
            var page_template_block_id = $(event.target).data('pagetemplateblock');
            var page_id = $(event.target).data('page');
            var template_block_id = $(event.target).data('templateblock');
            var id = $(event.target).data('id');
    
            $.ajax({
                url: $(this).attr('href'),
                data: {wysiwyg: 1, id: id, page_template_block_id: page_template_block_id, page_id: page_id, template_block_id: template_block_id},
                success: function(response){
                    $("#ajaxEditBlock").html(response);
                    $('#editModal').modal('show');
                }
            });
            
        });
		


	}
}

$(document).ready(function(){
	Cms.Common.init();
});