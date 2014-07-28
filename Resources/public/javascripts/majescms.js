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
	}		
}

$(document).ready(function(){
	CmsAdmin.Common.init();
});
