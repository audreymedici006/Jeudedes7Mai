/* On attend le fin de chargement de la page */
jQuery( document ).ready(function( $ ){	
	
	/* ecouter si le formulaire de lancé est encoyé */
	$( "body").on("submit",".frm_jdr_des" ,function( event ) {
        event.preventDefault();
		
		/* On vérifie que l'utilisateur a indiqué au moins un nombre de tirage positif */
		var isOk = false;
		$(".jdr_des_nb").each(function(){
			if ($(this).val()>0){
			isOk=true;	
			/* On peut arreter le traitement, voir comment */
			
			}
		});
		
		if (!isOk){
			alert('Indiquez un nombre de tirage');
			return false;
		}
		
		var data = {
			/* Déclencher action wp_ajax_jdr_des_lances de wordpress */	
			'action': 'jdr_des_lances',
			'nonce':ajax_object.ajax_nonce,
			'data': $( this ).serialize(),
		};
        $.post(ajax_object.ajax_url, data, function(response) {
		$('.jdr_des_res').html(response.html);
		}, "json");
		});
});