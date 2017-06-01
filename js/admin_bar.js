        jQuery( document ).ready(function() {
                jQuery("#wp-admin-bar-fastcgi_cache li").click(function(e){
			var id = (typeof e.target.id != "undefined" && e.target.id) ? e.target.id : jQuery(e.target).parent("li").attr("id");
			var action = '';

			if(id == 'wp-admin-bar-delete_entire_cache'){
				action = 'delete_entire_cache';
			}

			if (action !== '') {
				jQuery.ajax({
					type: 'GET',
					url: ajaxurl,
					data : {"action": action},
					dataType : "json",
					cache: false,
					success: function(data){
					    if (data[0] == true) {
                              		        target.innerHTML = 'Entire cache was purged';
                                                jQuery('#the-list a.cache-purge-inline').each(function(){
                                                    jQuery(this).context.outerHTML = '<span>Cache Purged</span>';
                                                });
                                           }
					}
				});
			}
                });

        });
