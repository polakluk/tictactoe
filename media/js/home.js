function Home() {
	
}

// this method initializes and caches all important variables for the game object
Home.Init = function() {
	Home.elements = {};
	Home.elements.main_container = $( 'div.container-Home' );
	Home.elements.form_group = $( 'div.form-group', Home.elements.main_container );
	Home.elements.user_input = $( '#username', Home.elements.form_group );
	Home.elements.go_button = $( '#go_button', Home.elements.form_group );
	Home.elements.form = $( 'form', Home.elements.main_container );
}

// this method determines, whether the game object should be used for this page
Home.Ready = function() {
	return Home.elements.main_container.size() == 1;	
}

//this method binds all events on elements
Home.BindEvents = function() {
	if( Home.elements.go_button.size() ) {
		Home.elements.go_button.on( 'click', function() {
			if( Home.elements.user_input.val().length == 0 ) {
				Home.elements.form_group.addClass( 'has-error' );
				Home.elements.user_input.focus();
			} else {
				Home.elements.form.submit();
			}
		});
	}
}

$(function(){
	Home.Init();
	if( Home.Ready() ) {
		Home.BindEvents();		
	}	
});
