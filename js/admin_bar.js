        jQuery( document ).ready(function() {
                jQuery("#wp-admin-bar-fastcgi_cache li").click(function(e){
                                var id = (typeof e.target.id != "undefined" && e.target.id) ? e.target.id : jQuery(e.target).parent("li").attr("id");
                                var action = '';

                                if(id == 'wp-admin-bar-delete_entire_cache'){
                                        action = 'delete_entire_cache';
                                }

                                jQuery.ajax({
                                        type: 'GET',
                                        url: ajaxurl,
                                        data : {"action": action},
                                        dataType : "json",
                                        cache: false,
                                        success: function(data){
						console.log(data);
                                        }
                                });
                });

        });
