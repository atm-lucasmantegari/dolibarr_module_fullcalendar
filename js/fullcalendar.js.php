<?php

	require '../config.php';
	
	//var_dump($user->array_options);
	
	list($langjs,$dummy) =explode('_', $langs->defaultlang);
	
	readfile(dol_buildpath('/fullcalendar/lib/moment/min/moment.min.js'));
	readfile(dol_buildpath('/fullcalendar/lib/fullcalendar/dist/fullcalendar.min.js'));
	readfile(dol_buildpath('/fullcalendar/lib/fullcalendar/dist/lang/'.$langjs.'.js'));
	
	if(!empty($user->array_options['options_googlecalendarapi'])) {
	//	readfile(dol_buildpath('/fullcalendar/lib/fullcalendar/dist/fullcalendar/gcal.js'));
		
	}
	
	
	$defaultView='month';
	
?>
if(document.location.href.indexOf('/comm/action/index.php') != -1) {
	
	
	$(document).ready(function() {
		var year = $('form[name=listactionsfilter]').find('input[name=year]').val();
		var month = $('form[name=listactionsfilter]').find('input[name=month]').val();
		var defaultDate = year+'-'+month+'-01';
		
		$('head').append('<link rel="stylesheet" href="<?php echo dol_buildpath('/fullcalendar/lib/fullcalendar/dist/fullcalendar.min.css',1) ?>" type="text/css" />');
			$('table.cal_month').hide();	
			
			$('table.cal_month').after('<div id="fullcalendar"></div>');
			
			$('#fullcalendar').fullCalendar({
		        header:{
		        	left:   'title',
				    center: 'agendaDay,agendaWeek,month',
				    right:  'prev,next today'
		        }
		        ,defaultDate:defaultDate
		        <?php
			   /* if(!empty($user->array_options['options_googlecalendarapi'])) {
			    	?>
			    	,googleCalendarApiKey: '<?php echo $user->array_options['options_googlecalendarapi']; ?>'
			    	,eventSources: [
		            	{
		                	googleCalendarId: '<?php echo $user->array_options['options_googlecalendarurl']; ?>'
		            	}
		            ]
			    	<?php
			    }*/
				?>
		        
		        ,lang: 'fr'
		        ,weekNumbers:true
		        ,defaultView:'<?php echo $defaultView ?>'
		        ,events : '<?php echo dol_buildpath('/fullcalendar/script/interface.php',1) ?>'+'?'+$('form[name=listactionsfilter]').serialize() 
			,eventRender:function( event, element, view ) {
				element.tipTip({
					maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50  
					,content : '<strong>'+event.title+'</strong><br />'+ event.note
				});

				if(event.fk_soc>0) element.append('<div>'+event.societe+'</div>');
				if(event.fk_contact>0) element.append('<div>'+event.contact+'</div>');

				element.find(".classfortooltip").tipTip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});

			 }
		        ,eventDrop:function( event, delta, revertFunc, jsEvent, ui, view ) { 
		        	console.log(delta);	
		        	
		        	$.ajax({
		        		url:'<?php echo dol_buildpath('/fullcalendar/script/interface.php',1) ?>'
		        		,data:{
							put:'event-move'
							,id:event.id
							,data:delta._data
		        		}
		        	})
		        }
		        ,eventResize:function( event, delta, revertFunc, jsEvent, ui, view ) { 
		        	console.log(delta);
	
		        	$.ajax({
		        		url:'<?php echo dol_buildpath('/fullcalendar/script/interface.php',1) ?>'
		        		,data:{
							put:'event-resize'
							,id:event.id
							,data:delta._data
		        		}
		        	})
		        }
		        ,dayClick:function( date, jsEvent, view ) { 
		        	console.log(date.format());
		        	//document.location.href = "<?php echo dol_buildpath('/comm/action/card.php?action=create',1); ?>"
		        	
		        	$('#pop-new-event').remove();
		        	
		        	$div = $('<div id="pop-new-event"></div>');
		        	$div.append('<input type="text" name="label" value="" placeholder="<?php echo $langs->trans('Title') ?>" style="width:300px">');
		        	$div.append('<br /><textarea name="note" value="" placeholder="<?php echo $langs->trans('Note') ?>"  style="width:300px" rows="3"></textarea>');
		        	
		        	$('body').append($div);
		        		
		        	$('#pop-new-event').dialog({
		        		modal:true
		        		,width:'auto'
		        		,title:"<?php echo $langs->transnoentities('AddAnAction') ?>"
		        		,buttons:{
		        			    "<?php echo $langs->transnoentities('Add') ?>": function() {									if($('#pop-new-event input[name=label]').val() != '') {
										
										$.ajax({
											url:'<?php echo dol_buildpath('/fullcalendar/script/interface.php',1) ?>'
							        		,data:{
												put:'event'
												,label:$('#pop-new-event input[name=label]').val()
												,note:$('#pop-new-event input[name=note]').val()
												,date:date.format()
							        		}
										}).done(function() {
											$('#fullcalendar').fullCalendar( 'refetchEvents' );
											$('#pop-new-event').dialog( "close" );
										});
										
									}
		        			    	
		        			    }
						        ,"<?php echo $langs->transnoentities('Cancel') ?>": function() {
						          $('#pop-new-event').dialog( "close" );
						        }
		        		}
		        	});
		        		
		        }
		        
		    });		
		    
		    
	});
}
