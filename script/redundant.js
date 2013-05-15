			//
            // Filters
            //
            
            // programme filter change
            $('#programmes').change(function(){
                
                // get selected filter
                var selected_programme = $(this).children(":selected").attr("id");
                
                if(selected_programme!="0") {
					$('#filters').html('<h2>Loading ...</h2>');
                    // filter based on selected programme
                    $.get('filter.php?type=P&data='+selected_programme, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // show all
                    $.get('filter.php?type=$data=', function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
            
            // course filter change
            $('#courses').change(function(){
                
                // get selected filter
                var selected_course = $(this).children(":selected").attr("id");
                
                if(selected_course!="0") {
					$('#filters').html('<h2>Loading ...</h2>');
                    // filter based on selected programme
                    $.get('filter.php?type=C&data='+selected_course, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // course filter has been cleared ...
                    // get the currently selected programme
                    var selected_programme = $('#programmes').children(":selected").attr("id");
                    
                    // show filters based on the selected programme
                    $.get('filter.php?type=P$data='+selected_programme, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
			
			// course years filter change
            $('#courseyears').change(function(){
                
                // get selected filter
                var selected_year = $(this).children(":selected").attr("id");
                
                if(selected_year!="0") {
					$('#filters').html('<h2>Loading ...</h2>');
                    // filter based on selected programme
                    $.get('filter.php?type=Y&data='+selected_year, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // course years filter has been cleared ...
                    // get the currently selected programme
                    var selected_programme = $('#programmes').children(":selected").attr("id");
                    
                    // show filters based on the selected programme
                    $.get('filter.php?type=P$data='+selected_programme, function(data){
                        $('#filters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
			
			// units filter change
            $('#units').change(function(){
                
                // get selected filter
                var selected_unit = $(this).children(":selected").attr("id");
                
                if(selected_unit!="0") {
					$('#filters').html('<h2>Loading ...</h2>');
                    // filter based on selected unit
                    $.get('filter.php?type=U&data='+selected_unit, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // unit filter has been cleared ...
                    // get the currently selected programme
                    var selected_programme = $('#programmes').children(":selected").attr("id");
                    
                    // show filters based on the selected programme
                    $.get('filter.php?type=P$data='+selected_programme, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
			
			$('#back_to_filter').live("click", function() {
				$('#results_container').hide();
				$('#filter_container').show();
				$('#back_to_filter').hide();
			});
									
			$('#showuserenrolments').live("click", function() {
				var selected_programme = $('#programmes').children(":selected").attr("id");
				var selected_courseyear = $('#courseyears').children(":selected").attr("id");
				var selected_course = $('#courses').children(":selected").attr("id");
				var selected_unit = $('#units').children(":selected").attr("id");
				
				
				$('#filter_container').hide();
				$('#results_container').html('Loading Results ...');
				
				// show courses that user is enrolled on
				$.get('grid.php?T=ue&P=' + selected_programme +
					  '&Y=' + selected_courseyear +
					  '&C=' + selected_course +
					  '&U=' + selected_unit, function(data) {
						
						$('#results_container').html(data);
						$('#back_to_filter').show();
						$('#results_container').show();
						
						/*$('#hiddenresultslightbox').html(data);
						
						$('#hiddenresultslightbox').lightbox_me({
							centered: false,
							appearEffect: 'show',
							lightboxSpeed: 'fast',
							overlaySpeed: 'fast',
							closeClick: false,
							closeEsc: false, 
							onLoad: function() { 
                                // do anything after lightbox is loaded?
                                $('#hiddenresultslightbox').css('width','400px');
								$('#hiddenresultslightbox').css('background','#ffffff');
                            }
                        });*/
				        
					});
				
				return false;
			});
			
			
			// show possible enrolments
			$('#showpossibleenrolments').live("click", function() {
				var selected_programme = $('#programmes').children(":selected").attr("id");
				var selected_courseyear = $('#courseyears').children(":selected").attr("id");
				var selected_course = $('#courses').children(":selected").attr("id");
				var selected_unit = $('#units').children(":selected").attr("id");
				
				$('#filter_container').hide();
				$('#results_container').html('Loading Results ...');
				
				// show courses that user is enrolled on
				$.get('grid.php?T=pe&P=' + selected_programme +
					  '&Y=' + selected_courseyear +
					  '&C=' + selected_course +
					  '&U=' + selected_unit, function(data) {
						
						$('#results_container').html(data);
						$('#back_to_filter').show();
						$('#results_container').show();
						
						/*$('#hiddenresultslightbox').html(data);
						
						$('#hiddenresultslightbox').lightbox_me({
							centered: false,
							appearEffect: 'show',
							lightboxSpeed: 'fast',
							overlaySpeed: 'fast',
							closeClick: false,
							closeEsc: false, 
							onLoad: function() { 
                                // do anything after lightbox is loaded?
                                $('#hiddenresultslightbox').css('width','400px');
								$('#hiddenresultslightbox').css('background','#ffffff');
                            }
                        });*/
				        
					});
				
				return false;
			});
			
			
			
			//
			// end of filters ---------------------------------->
			//
			
			
						//
			// results
			//
			
			// show possible enrolments
			$('#pagenumber').live("click", function() {
				var selected_programme = $('#programmes').val();
				var selected_courseyear = $('#courseyears').val();
				var selected_course = $('#courses').val();
				var selected_unit = $('#units').val();
				var result_type = $('#resulttype').val();
				var pagenum = $(this).attr('name');
				
				$('#filter_container').hide();
				$('#results_container').html('Loading Results ...');
				
				// show courses that user is enrolled on
				$.get('results.php?pagenum='+ pagenum +'&T='+result_type+'&P=' + selected_programme +
					  '&Y=' + selected_courseyear +
					  '&C=' + selected_course +
					  '&U=' + selected_unit, function(data) {
						
						$('#results_container').html(data);
						$('#back_to_filter').show();
						$('#results_container').show();
						
						/*$('#hiddenresultslightbox').html(data);
						
						$('#hiddenresultslightbox').lightbox_me({
							centered: false,
							appearEffect: 'show',
							lightboxSpeed: 'fast',
							overlaySpeed: 'fast',
							closeClick: false,
							closeEsc: false, 
							onLoad: function() { 
                                // do anything after lightbox is loaded?
                                $('#hiddenresultslightbox').css('width','400px');
								$('#hiddenresultslightbox').css('background','#ffffff');
                            }
                        });*/
				        
					});
				
				return false;
			});
			
			
			//
			// end of results ---------------------------------->
			//