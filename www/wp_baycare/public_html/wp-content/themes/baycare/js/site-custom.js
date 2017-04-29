/**
 * Handles toggling the main navigation menu for small screens.
 */
jQuery( document ).ready( function( $ ) {
	
	//Mobile Menu
	$.fn.mobilemenuclass = function() {
		//Set mobile menu as closed by defualt
		$( 'body' ).addClass( 'mobile-closed' );
		//Set sub nav mobile menu closed by default
		$( 'body' ).addClass( 'sub-nav-closed' );
		//Set class for first level sub menu
		$( '.sub-menu' ).addClass( 'sub-nav-hidden' );
		//Set class for second level sub menu
		$( '.sub-menu .sub-menu' ).addClass( 'sub-sub-nav-hidden' ).removeClass( 'sub-nav-hidden' );
		//Set toggle functionality
		$( '#site-navigation .menu-toggle' ).click( function() {
			//If mobile menu is opened close it
			if($( 'body' ).hasClass('mobile-opened')){
				$( 'body' ).removeClass( 'mobile-opened' ).addClass( 'mobile-closed' );
			}
			//If mobile menu is closed open it
			else
				$( 'body' ).removeClass( 'mobile-closed' ).addClass( 'mobile-opened' );
		});
		
		//Set functionality for returning to main mobile menu
		$( '.sub-menu .sub-nav-back' ).click( function() {
			//Stop from effecting other elements
			event.stopPropagation();
			//Set classes on body
			$( '.sub-nav-opened' ).removeClass( 'sub-nav-opened' ).addClass( 'sub-nav-closed' );
			//Set class on sub menu to hide
			$( '.sub-nav-visible' ).removeClass( 'sub-nav-visible' ).addClass( 'sub-nav-hidden' );
		});
		
		//Set functionality for returning to first level sub menu
		$( '.sub-menu .sub-sub-nav-back' ).click( function() {
			//Stop from effecting other elements
			event.stopPropagation();
			//Show first level sub menu
			$( '.sub-nav-hide' ).removeClass( 'sub-nav-hide' )
			//Hide second level sub menu
			$( '.sub-sub-nav-visible' ).removeClass( 'sub-sub-nav-visible' ).addClass( 'sub-sub-nav-hidden' );
		});
		
		//Set functinality to show second level sub menu
		$( '.sub-sub-nav-hidden' ).click( function() {
			//Hide first level sub meu
			$( '.sub-nav-visible' ).addClass( 'sub-nav-hide' );
			//Show second level sub menu
			$(this).removeClass( 'sub-sub-nav-hidden' ).addClass( 'sub-sub-nav-visible' );
		});
		
		//Set functionality to show first level sub menu
		$( '.sub-nav-closed .sub-nav-hidden' ).click( function() {
			//Set class on body
			$( '.sub-nav-closed' ).removeClass( 'sub-nav-closed' ).addClass( 'sub-nav-opened' );
			//Show sub menu
			$(this).removeClass( 'sub-nav-hidden' ).addClass( 'sub-nav-visible' );
		});
	};
	
	//Sticky Nav Functionality
	$.fn.stickymenu = function() {
		
		//Set TOP of Header
		$('#headercontainer.sticky').css('top','0px');
		
		//Set Interval
		scrollIntervalID = setInterval(stickIt, 10);

		//Create function
		function stickIt() {
			//gets offset of header
			var topofDiv = $("#headercontainer.sticky").offset().top; 
			//gets height of header
			var height = $("#headercontainer.sticky").outerHeight(); 
			//Is window scrolled past header             
  			if ($(window).scrollTop() >= height) {     
				//Set classes for sticky nav
				$('body').removeClass( 'nav-fix' ).addClass( 'nav-stick' );
  			} 
			else {
    			//Remove classes for sticky nav
    			$('body').removeClass( 'nav-stick' ).addClass( 'nav-fix' );
  			}
		}
	}
	
	//On Scroll Nav Functionality
	$.fn.onscrollmenu = function() {
		// Hide Header on on scroll down
		var didScroll;
		var lastScrollTop = 0;
		var delta = 5;
		var navbarHeight = $('#headercontainer.on-scroll').outerHeight();

		$(window).scroll(function(event){
    		didScroll = true;
		});

		setInterval(function() {
    		if (didScroll) {
    		    hasScrolled();
        		didScroll = false;
    		}
		}, 250);

		function hasScrolled() {
    		var st = $(this).scrollTop();
    
    		// Make sure they scroll more than delta
    		if(Math.abs(lastScrollTop - st) <= delta)
    	   		return;
    
    		// If they scrolled down and are past the navbar, add class .nav-up.
    		// This is necessary so you never see what is "behind" the navbar.
    		if (st > lastScrollTop && st > navbarHeight){
    	    	// Scroll Down
    	    	$('body').removeClass('nav-down').addClass('nav-up');
    		} else {
        		// Scroll Up
        		if(st + $(window).height() < $(document).height()) {
        	    	$('body').removeClass('nav-up').addClass('nav-down');
        		}
    		}
    	
	    	lastScrollTop = st;
		}
	}

	//CSS IMAGE SLIDER
	$.fn.sliderer = function() {
		
		//Set First Input to Checked
		$('#img-0').attr('checked', true);
		
		//Get ID of lastt input
		$lastid = $('.slides input:last').attr('id');
		
		//Set Previous button of first slide to last input ID
		$('#img-prev-0').attr('for', $lastid);
		
		//Set Next button of last slide to first input ID
		$('.slides li:last label:last').attr('for', 'img-0');
		
		//Get number of slides
		$numslides = $('.slides').children('input').length;
		
		//Create LI for dots Navigation
		$('.slides').append('<li class="nav-dots"></li>');
		
		//Create dots navigation
		for(var i = 0; i < $numslides; i++) {
         $('.nav-dots').append('<label for="img-'+i+'" class="nav-dot" id="img-dot-'+i+'"></label>');
    	}
     	
		//set set default hovering variable for hover check
		hovering = false;
	
		//empty timer variable
		var itvl = null;
		
    	//set timer variable
		itvl = window.setInterval(function(){slideIT()},5000);	

		//Check for hover
		$(".slides").hover(
   			function() {
       			//if is hovering set to true
				hovering = true;
				//clear timer on function
				window.clearInterval(itvl);
   			},
    		
			function() {
				//if not hover set to false
        		hovering = false;
				//reset timer variable
				itvl = window.setInterval(function(){slideIT()},5000);
    		}
		);

		function slideIT() {
		
			if (!hovering) {
				//get checked input by name
				$radio = $('input[name=slide-btn]:checked');
				//get id of checked input
				$radioid = $radio.attr('id');
				//remove non numerical characters
				$res = $radioid.replace(/[A-Za-z$-]/g, "");
				//set number as an intger
				$number = parseInt($res);
				//Increase integer by 1
				$numb = $number+1;
				//build the input ID
				$imgnumb = "#img-"+$numb;
				//testing output
				//window.alert($imgnumb);
				
				//Input with this id exist
				if ($($imgnumb).length){
					//set that input to checked
        			$($imgnumb).attr('checked', 'checked');
    			}
				//Input with this id does not exist
				else {
					//set first input to checked
					$("#img-0").attr('checked', 'checked');
				}
			}
		};
	}
	
	
	if ($('#headercontainer.sticky').length) {$.fn.stickymenu();};
	if ($('#headercontainer.on-scroll').length) {$.fn.onscrollmenu();};
	if ($('.slides').length) {$.fn.sliderer();};
	$.fn.mobilemenuclass();
		
	$('.sub-nav-hidden').prepend('<li class="sub-nav-back"><a>Back</a></li>');
	$('.sub-sub-nav-hidden').prepend('<li class="sub-sub-nav-back"><a>Back</a></li>');

} );