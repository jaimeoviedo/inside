var my = my || {};

$(function () {

    my.vm = function () {

        var self = this;
        
        self.posts = ko.observableArray();
        self.counterViews = ko.observable(0);
        
        self.exportData = function () {
        	if(self.posts().length>0){
        		var url = Routing.generate('ji_expor_posts');
            	window.open(url, '_blank');
            }
        	else{
        		alert("There are not posts");
        	}
        };
        
        self.savePost = function () {
        	
        	var url_add_post = Routing.generate('ji_add_post');
        	
        	var imagen = $('#imagen').val();
        	if(imagen==''){
        		alert("You must select an image");
        		return;
        	}
        	
        	$('#div_loader').show();
        	
            var formData = new FormData(document.getElementById("form_post"));
            $.ajax({
                url: url_add_post,
                type: "post",
                dataType: "html",
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            })
            .done(function(respuesta){
            	var datos = JSON.parse(respuesta);
            	
            	var code_error = datos.code_error;
            	var mensaje = datos.mensaje;
            	
            	if(code_error==0){//post created ok
            		var postCreated = datos.postCreated;
            		var nuevo_conjunto = [];
                	
                	var post = new my.Post();
                	post.parsearJson(postCreated);
                	nuevo_conjunto.push(post);
                	
                	$.each(self.posts(), function (i, p) {
                        nuevo_conjunto.push(p);
                    });
                	
                	self.posts(nuevo_conjunto);
                	
                	$('.div_post_list').show();
            	}
            	
            	toastr.success(mensaje);
            	$('#resetform').click();
            	
            	$('#div_loader').hide();
            });
        };
    };
    
    my.vm.loadPosts = function () {
    	
    	$('#div_loader').show();
		var nuevo_conjunto = [];
		
		$.ajax({
			async:true,   
        	url: Routing.generate('ji_load_posts'),
            processData: true,
            type: 'POST',
            complete: function ( data ) {
            	
            	var datos = data.responseText;
            	var datos_parse = JSON.parse(datos);
            	
            	var code_error = datos_parse.code_error;
            	if(code_error==0){//there was not error
            		var array_datos = datos_parse.posts;
            		
                	$.each(array_datos, function (i, po) {
                        var post = new my.Post();
                        post.parsearJson(po);
                        nuevo_conjunto.push(post);
                    });

                    self.posts(nuevo_conjunto);
                    
                    if(self.posts().length>0){
                    	$('.div_post_list').show();
                    }
            	}
                
                $('#div_loader').hide();
            }
        });
    };
    
    my.vm.loadContador = function () {
    	
		$.ajax({
			async:true,   
        	url: Routing.generate('ji_load_counter'),
            processData: true,
            type: 'POST',
            complete: function ( data ) {
            	var datos = data.responseText;
            	var datos_parse = JSON.parse(datos);
            	
            	var code_error = datos_parse.code_error;
            	if(code_error==0){//there was not error
            		var contador = datos_parse.contador;
                	
                	self.counterViews(contador);
                	$('#span_counter_views').show();
            	}
            }
        });
    };
    
    my.vm.loadData = function () {
    	my.vm.loadPosts();
    	my.vm.loadContador();
    };
    
    
    ko.applyBindings(my.vm);
    
});
