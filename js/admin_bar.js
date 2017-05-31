        jQuery( document ).ready(function() {
		console.log('document ready');
                jQuery("#wp-admin-bar-fastcgi_cache li").click(function(e){
                                var id = (typeof e.target.id != "undefined" && e.target.id) ? e.target.id : jQuery(e.target).parent("li").attr("id");
console.log('in here ' + id);
                                var action = '';

                                if(id == 'wp-admin-bar-delete_entire_cache'){
                                        action = 'delete_entire_cache';
                                }

                                jQuery.ajax({
                                        type: 'GET',
                                        url: ajaxurl,
                                        data : {"action": action, "path"},
                                        dataType : "json",
                                        cache: false,
                                        success: function(data){
						console.log(data);
                                        }
                                });
                });

        });
