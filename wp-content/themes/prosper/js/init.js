// =============================================
// PLUGINS
// =============================================

(function(){var b,f;b=this.jQuery||window.jQuery;f=b(window);b.fn.stick_in_parent=function(d){var A,w,J,n,B,K,p,q,k,E,t;null==d&&(d={});t=d.sticky_class;B=d.inner_scrolling;E=d.recalc_every;k=d.parent;q=d.offset_top;p=d.spacer;w=d.bottoming;null==q&&(q=0);null==k&&(k=void 0);null==B&&(B=!0);null==t&&(t="is_stuck");A=b(document);null==w&&(w=!0);J=function(a,d,n,C,F,u,r,G){var v,H,m,D,I,c,g,x,y,z,h,l;if(!a.data("sticky_kit")){a.data("sticky_kit",!0);I=A.height();g=a.parent();null!=k&&(g=g.closest(k));if(!g.length)throw"failed to find stick parent";v=m=!1;(h=null!=p?p&&a.closest(p):b("<div />"))&&h.css("position",a.css("position"));x=function(){var c,f,e;if(!G&&(I=A.height(),c=parseInt(g.css("border-top-width"),10),f=parseInt(g.css("padding-top"),10),d=parseInt(g.css("padding-bottom"),10),n=g.offset().top+c+f,C=g.height(),m&&(v=m=!1,null==p&&(a.insertAfter(h),h.detach()),a.css({position:"",top:"",width:"",bottom:""}).removeClass(t),e=!0),F=a.offset().top-(parseInt(a.css("margin-top"),10)||0)-q,u=a.outerHeight(!0),r=a.css("float"),h&&h.css({width:a.outerWidth(!0),height:u,display:a.css("display"),"vertical-align":a.css("vertical-align"),"float":r}),e))return l()};x();if(u!==C)return D=void 0,c=q,z=E,l=function(){var b,l,e,k;if(!G&&(e=!1,null!=z&&(--z,0>=z&&(z=E,x(),e=!0)),e||A.height()===I||x(),e=f.scrollTop(),null!=D&&(l=e-D),D=e,m?(w&&(k=e+u+c>C+n,v&&!k&&(v=!1,a.css({position:"fixed",bottom:"",top:c}).trigger("sticky_kit:unbottom"))),e<F&&(m=!1,c=q,null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.detach()),b={position:"",width:"",top:""},a.css(b).removeClass(t).trigger("sticky_kit:unstick")),B&&(b=f.height(),u+q>b&&!v&&(c-=l,c=Math.max(b-u,c),c=Math.min(q,c),m&&a.css({top:c+"px"})))):e>F&&(m=!0,b={position:"fixed",top:c},b.width="border-box"===a.css("box-sizing")?a.outerWidth()+"px":a.width()+"px",a.css(b).addClass(t),null==p&&(a.after(h),"left"!==r&&"right"!==r||h.append(a)),a.trigger("sticky_kit:stick")),m&&w&&(null==k&&(k=e+u+c>C+n),!v&&k)))return v=!0,"static"===g.css("position")&&g.css({position:"relative"}),a.css({position:"absolute",bottom:d,top:"auto"}).trigger("sticky_kit:bottom")},y=function(){x();return l()},H=function(){G=!0;f.off("touchmove",l);f.off("scroll",l);f.off("resize",y);b(document.body).off("sticky_kit:recalc",y);a.off("sticky_kit:detach",H);a.removeData("sticky_kit");a.css({position:"",bottom:"",top:"",width:""});g.position("position","");if(m)return null==p&&("left"!==r&&"right"!==r||a.insertAfter(h),h.remove()),a.removeClass(t)},f.on("touchmove",l),f.on("scroll",l),f.on("resize",y),b(document.body).on("sticky_kit:recalc",y),a.on("sticky_kit:detach",H),setTimeout(l,0)}};n=0;for(K=this.length;n<K;n++)d=this[n],J(b(d));return this}}).call(this);jQuery(document).ready(function($){(function(){var reset_scroll;$(function(){return $("[data-sticky_column]").stick_in_parent({parent:"[data-sticky_parent]",offset_top:64,});});reset_scroll=function(){var scroller;scroller=$("body,html");scroller.stop(true);if($(window).scrollTop()!==0){scroller.animate({scrollTop:0},"fast");}
return scroller;};window.scroll_it=function(){var max;max=$(document).height()- $(window).height();return reset_scroll().animate({scrollTop:max},max*3).delay(100).animate({scrollTop:0},max*3);};window.scroll_it_wobble=function(){var max,third;max=$(document).height()- $(window).height();third=Math.floor(max/3);return reset_scroll().animate({scrollTop:third*2},max*3).delay(100).animate({scrollTop:third},max*3).delay(100).animate({scrollTop:max},max*3).delay(100).animate({scrollTop:0},max*3);};$(window).on("resize",(function(_this){return function(e){return $(document.body).trigger("sticky_kit:recalc");};})(this));}).call(this);$('.menu-button').on('click',function(){var menu=$('.nav').slideToggle(400);$(this).toggleClass('active');$(window).resize(function(){var w=$(window).width();if(w>320&&menu.is(':hidden')){menu.removeAttr('style');}});});});

// =============================================
// BEGIN THEME SCRIPTS
// =============================================
jQuery(document).ready(function($) {

	/*====================================
	Sticky Column
	======================================*/

			     var window_width = jQuery( window ).width();

			     if (window_width < 768) {
			       jQuery(".navbar-default, .stickthis").trigger("sticky_kit:detach");
			     } else {
			       make_sticky();
			     }
			     jQuery( window ).resize(function() {

			       window_width = jQuery( window ).width();

			       if (window_width < 768) {
			         jQuery(".navbar-default,  .stickthis").trigger("sticky_kit:detach");
			       } else {
			         make_sticky();
			       }

			     });

			     function make_sticky() {
			       jQuery(".navbar-default").stick_in_parent({
			         parent: '#wrapall'
			       });
						 jQuery(".stickthis").stick_in_parent({
			         parent: '.row',
							 offset_top: 70,
			       });
			     }

	/*====================================
	MENU
	======================================*/
	$('ul.nav li.dropdown, ul.nav li.dropdown-submenu').hover(function() {
		$(this).find(' > .dropdown-menu').stop(true, true).delay(100).fadeIn();
	}, function() {
		$(this).find(' > .dropdown-menu').stop(true, true).delay(100).fadeOut();
	});

	/*====================================
	SIDEMENU
	======================================*/
	$(function(){
	  $('#sidemenu a').on('click', function(e){
		e.preventDefault();

		if($(this).hasClass('open')) {
		  // do nothing because the link is already open
		} else {
		  var oldcontent = $('#sidemenu a.open').attr('href');
		  var newcontent = $(this).attr('href');

		  $(oldcontent).fadeOut('fast', function(){
			$(newcontent).fadeIn().removeClass('hidden');
			$(oldcontent).addClass('hidden');
		  });


		  $('#sidemenu a').removeClass('open');
		  $(this).addClass('open');
		}
	  });
	});



    /*====================================
    Fixed Header
    ======================================*/
    $(window).bind('scroll', function() {
        var navHeight = $(window).height() - 480;
        if ($(window).scrollTop() > navHeight) {
            $('.navbar-fixed-top').addClass('on');
        } else {
            $('.navbar-fixed-top').removeClass('on');
        }
    });


    /*====================================
    Masonry
    ======================================*/
	$blocks = $(".wowitemboxlist,.edd_downloads_list");
	$blocks.imagesLoaded(function(){
		$blocks.masonry({
			itemSelector: '.wowitembox,.edd_download'
		});

		// Fade blocks in after images are ready (prevents jumping and re-rendering)
		$(".wowitembox,.edd_download").fadeIn();
	});
	$(document).ready( function() { setTimeout( function() { $blocks.masonry(); }, 500); });
	$(window).resize(function () {
		$blocks.masonry();
	});

	// =============================================
	// Toggle
	// =============================================
		(function () {

		$("h3.wow-toggle-trigger").click(function(){
		$(this).toggleClass("active").next().slideToggle("fast");return false;
		});

		}());


	$('#contactform').submit(function (e) {
		e.preventDefault();
		var oldh = $('#contactform #submit').html();
		$('#contactform #submit').html("<i class='fa fa-refresh fa-spin'></i> Please Wait...");
		$(this).ajaxSubmit({
			url: wpdm_ajax_url+"?action=contact_us",
			success: function (res) {
				$('#contactform #submit').html(oldh);
				$('#done').fadeIn();
			}
		});
	});





// =============================================
// END THEME SCRIPTS
// =============================================

});
