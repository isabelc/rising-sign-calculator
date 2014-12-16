jQuery(function() {
		// send birthtime and city for processing, returns GMT time offset back to form
	    function calculateOffset(e) {
		    jQuery.ajax({
				url: isa_ajax_object.tzoffset,
				type: "POST",
				data: jQuery("#ajaxbirthdt :input").serialize(),
				dataType: "json",
				success: function( data ) {
	                    jQuery("#offset_geo").val(data.ofs1g);
				}
		
			});
		}
		// auto fill geo timezone
		function tzon1geoHIDDEN(poplat, poplng) {
					// Get timezone id by coordinates from Geonames webservice
						jQuery.ajax({
								dataType: "jsonp",
								url: "http://api.geonames.org/timezoneJSON",
								data: {
					                        lat: poplat,
					                        lng: poplng,
											username: "@todoyourusername"
					                    },
					
								success: function( data ) {

									jQuery("#hidTzgeo1").html("<input type='hidden' id='zon1_geo' name='zon1_geo' value='" + data.timezoneId + "' />");


			                    }
						});// end ajax
			
		}
	
	    // auto fill longitude
        function lonfill(populateddecimal) {
				jQuery("#long_decimal_1").val(populateddecimal);
		}

	    // auto fill latitude
		function latfill(populateddecimal) {
				jQuery("#lat_decimal_1").val(populateddecimal);
		}

        jQuery( "#city" ).autocomplete({
            source: function( request, response ) {
                jQuery.ajax({
                    url: "http://api.geonames.org/searchJSON",
                    dataType: "jsonp",
                    data: {
					featureClass: "P",
					style: "full",
					maxRows: 12,
					name_startsWith: request.term,
					username: "@todoyourusername",
					lang: isa_ajax_object.lang
                    },
                    success: function( data ) {
                        response( jQuery.map( data.geonames, function( item ) {
                            return {
                                label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName, 
                                value: item.name,
								lngdeci: item.lng,
								latdeci: item.lat,


								
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function( event, ui ) {
				jQuery( "#city1label" ).text( isa_ajax_object.sele + " " );
				jQuery( "#place1" ).val( ui.item.label );
				jQuery( "#lat1label" ).text( isa_ajax_object.lati + " " );
				jQuery( "#lng1label" ).text( isa_ajax_object.longit + " " );
				jQuery("#newlog1").show();
				jQuery("#offsetwrap").show();
				jQuery("#nocity1").empty();
				tzon1geoHIDDEN( ui.item.latdeci, ui.item.lngdeci );// used to get the tz, to get offset
                latfill( ui.item.latdeci );
                lonfill( ui.item.lngdeci );
				jQuery( "#gmt1label" ).text( isa_ajax_object.gmt + " " );

            },

            open: function() {
                jQuery( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                jQuery( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });

		// ends city autocomplete

		jQuery('#fetchOffset').click(function(e) {
	        e.preventDefault();
	        calculateOffset();
	    });

	    function isaRequestRisingSign() {

			    jQuery.ajax({
					url: isa_ajax_object.ajaxurl,
					type: "POST",
					data: jQuery("#orderform").serialize(),
					dataType: "json",
					success: function( risingSignData ) {
						jQuery('#risingreport').show();
		                	jQuery('#risinginterp').html( risingSignData.interp );
						jQuery("#rscform").hide();
						var targetOffset = $('#risingreport').offset().top - 200;
						jQuery('html,body').animate({scrollTop: targetOffset}, 0);
      				}
				});
		}
		jQuery('#fetchFields').click(function() {
		        isaRequestRisingSign();
	    });

});