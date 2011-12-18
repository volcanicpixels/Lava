jQuery(document).ready(function(){
    jQuery('.js-only').removeClass('js-only');
    jQuery('select').dropkick();
    prettifyCheckboxes();
    prettifyPasswords();
    prettifyTexts();
    prettifyTimePeriods();
    addResetSettings();
    makeSticky();
});


function prettifyCheckboxes()
{
    jQuery('.setting[data-type="checkbox"]').each(function(){
        var checked = jQuery(this).find('input[type="checkbox"]').addClass( "invisible" ).hasAttr( "checked" );
        jQuery(this).find('input[type="checkbox"]').change(function(){
            var checked = jQuery(this).hasAttr( "checked" );
            var checkboxUx = jQuery(this).parents( '.setting' ).find( '.checkbox-ux' );
            if( checked )
            {
                jQuery( checkboxUx ).removeClass( "unchecked" ).addClass("checked");
            }
            else
            {
                jQuery( checkboxUx ).removeClass( "checked" ).addClass("unchecked");
            }
        });
        jQuery(this).find('.checkbox-ux' ).click(function(){
            if( jQuery(this).siblings('input[type="checkbox"]').hasAttr( "checked" ) )
            {
                jQuery(this).siblings('input[type="checkbox"]').removeAttr( "checked" ).change();
                jQuery(this).removeClass("checked").addClass("unchecked");
            }
            else
            {
                jQuery(this).siblings('input[type="checkbox"]').attr( "checked", "checked" ).change();
                jQuery(this).removeClass("unchecked").addClass("checked");
            }
        });
    });
}

function prettifyPasswords()
{
    jQuery('.setting[data-type="password"]').each(function(){
        jQuery(this).find( 'input[type="password"]' ).blur(function(){
            var password = jQuery(this).val();
            jQuery(this).siblings(".password-show").val(password);
            jQuery(this).parent( '.input-cntr' ).removeClass( "focus" ).click(function(){
                jQuery(this).find('input[type="password"]').focus();
            });

        }).focus(function(){
            jQuery(this).parent( '.input-cntr' ).addClass( "focus" );
        });
        jQuery(this).find( ".password-show" ).blur(function(){
            var password = jQuery(this).val();
            jQuery(this).siblings('input[type="password"]').val(password);
            jQuery(this).parent( '.input-cntr' ).removeClass( "focus" );
        }).focus(function(){
            jQuery(this).parent( '.input-cntr' ).addClass( "focus" );
        });

        jQuery(this).find( ".show-password-handle" ).click(function(){
            jQuery(this).parents('.setting').find('.input-cntr').attr("data-show", "text");
            jQuery(this).siblings(".hide-password-handle").show();
            jQuery(this).hide();
        });

        jQuery(this).find( ".hide-password-handle" ).click(function(){
            jQuery(this).parents('.setting').find('.input-cntr').attr("data-show", "password");
            jQuery(this).siblings(".show-password-handle").show();
            jQuery(this).hide();
        });
    });
}

function prettifyTexts()
{
    jQuery('.setting[data-type="text"]').each(function(){
        var currentValue = jQuery(this).find( 'input[type="text"]' ).val();
        var settingHtml =   '<div class="input-cntr clearfix">'+
                                '<div class="validation" data-state="not-invoked"></div>'+
                            '</div>';
        jQuery(this).find(".setting-control").append( settingHtml );
    });
}

function prettifyTimePeriods()
{
    jQuery('.setting[data-type="timeperiod"]').each(function(){
        jQuery(this).find('input[data-actual="true"]').addClass("invisible").change(function(){
            var newValue = jQuery( this ).val();
            newValue = Math.round( newValue / 60 ) * 60;
            jQuery( this ).val( newValue );//make sure it is a multiple of 60
            if( newValue % ( 60 * 60 * 24 * 7 ) == 0 )
            {
                jQuery( this ).parents( '.setting' ).find( '.time-period-ux' ).val( newValue / (60*60*24*7) );
                jQuery( this ).parents( '.setting' ).find( 'a[data-dk-dropdown-value="' + 60*60*24*7  + '"]' ).click();
            }
            else if( newValue % ( 60 * 60 * 24  ) == 0 )
            {
                jQuery( this ).parents( '.setting' ).find( '.time-period-ux' ).val( newValue / (60*60*24) );
                jQuery( this ).parents( '.setting' ).find( 'a[data-dk-dropdown-value="' + 60*60*24  + '"]' ).click();
            }
            else if( newValue % ( 60 * 60  ) == 0 )
            {
                jQuery( this ).parents( '.setting' ).find( '.time-period-ux' ).val( newValue / (60*60) );
                jQuery( this ).parents( '.setting' ).find( 'a[data-dk-dropdown-value="' + 60*60  + '"]' ).click();
            }
            else
            {
                jQuery( this ).parents( '.setting' ).find( '.time-period-ux' ).val( newValue / (60) );
                jQuery( this ).parents( '.setting' ).find( 'a[data-dk-dropdown-value="' + 60  + '"]' ).click();
            }
        });

        jQuery(this).find('select').change(function(){
            var quantity = jQuery(this).siblings('.input-cntr').find('.time-period-ux').val();
            var multiplier = jQuery(this).val();

            jQuery(this).siblings('input[data-actual="true"]').val( quantity * multiplier );
        });
        jQuery(this).find('.time-period-ux').change(function(){
            var quantity = jQuery(this).val();
            var multiplier = jQuery(this).parents('.setting-control').find('select').val();

            jQuery(this).parents('.setting-control').find('input[data-actual="true"]').val( quantity * multiplier );
            
        });
    });
}

function addResetSettings()
{
    jQuery( '.setting' ).each(function(){
        jQuery(this).find( '.reset-setting' ).click(function(){
            var settingParent = jQuery(this).parents( ".setting" );
            var defaultValue = jQuery(settingParent).attr("data-default-value");
            var valueChanged = changeSettingValue(settingParent, defaultValue);
            if( valueChanged )
            {
                jQuery(this).siblings('.undo-reset-setting').show();
                jQuery(this).hide();
                jQuery(settingParent).find('.show-status').each(function(){
                    var originalColor = jQuery(this).css("backgroundColor");
                    var newColor = '#FDEEAB';
                    jQuery(this)
                        .css({'background-image': 'none'})
                        .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                        .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                        .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                        .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100, function(){
                            jQuery(this).css({'background-image': ''});
                        });
                });
            }
        });
        jQuery(this).find( '.undo-reset-setting' ).click(function(){
            var settingParent = jQuery(this).parent().parent().parent();
            var newValue = jQuery(settingParent).attr("data-default-undo");
            var valueChanged = changeSettingValue(settingParent, newValue);
            jQuery(this).siblings('.reset-setting').show();
            jQuery(this).hide();
            jQuery(settingParent).find('.show-status').each(function(){
                var originalColor = jQuery(this).css("backgroundColor");
                var originalImage = jQuery(this).css("backgroundImage");
                var newColor = '#FDEEAB';
                jQuery(this)
                    .css({'background-image': 'none'})
                    .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                    .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                    .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100)
                    .animate({backgroundColor: newColor}, 100).animate({backgroundColor: originalColor }, 100, function(){
                        jQuery(this).css({'background-image': ''});
                    });
            });
            
        });
    });
}

function changeSettingValue(settingSelector, settingValue)
{
    
    var settingCurrent = jQuery(settingSelector).find('*[data-actual="true"]').val();
    var settingType = jQuery(settingSelector).attr("data-type");
    var doDefault = true;
    var isChanged = false;

    if(settingType == 'checkbox')
    {
        settingCurrent = "off";
        if(jQuery(settingSelector).find('.checkbox-ux').hasClass('checked'))
        {
            settingCurrent = "on";
        }
        if( settingValue == "on" )
        {
            jQuery(settingSelector).find('input[type="checkbox"]').attr("checked", "checked").change();
        }
        else
        {
            jQuery(settingSelector).find('input[type="checkbox"]').removeAttr("checked").change();
        }
    }
    jQuery(settingSelector).attr('data-default-undo', settingCurrent);

    if( settingCurrent != settingValue)
    {
        isChanged = true;
    }
    if( doDefault )
    {
        jQuery(settingSelector).find('*[data-actual="true"]').val( settingValue ).change().blur();
    }
    return isChanged;
}

function makeSticky()
{
    var stickyVars = Object;
    stickyVars.topPadding = jQuery('#wpadminbar').height();
    stickyVars.scrollingDistance = jQuery('#lava-nav').offset();
    stickyVars.nudge = stickyVars.scrollingDistance.top - stickyVars.topPadding
    stickyVars.stuck = false;

    window.stickyVars = stickyVars;

    jQuery('.lava-sticky').each(function(){
        var random = Math.floor(Math.random()*100001);
        jQuery(this).attr('data-sticky', random);
        var theProperties = jQuery(this).offset();
        jQuery(this).attr('data-sticky-left', theProperties.left);
        jQuery(this).attr('data-sticky-top', theProperties.top);
        jQuery(this).attr('data-sticky-width', jQuery(this).width());
        jQuery(this).attr('data-sticky-height', jQuery(this).height());
        jQuery(this).after( '<div class="sticky-padding" style="display:none;width:' + jQuery(this).width() +'px;height:'+ jQuery(this).height() + 'px;"></div>' );
    });

	jQuery(window).scroll( function() {
		if(jQuery(document).scrollTop()  > stickyVars.nudge && stickyVars.stuck == false){
            stickyVars.stuck = true;
            jQuery('.lava-sticky').each(function(){
                var random = jQuery(this).attr('data-sticky');
                var stickyLeft = jQuery(this).attr('data-sticky-left');
                console.log(stickyLeft);
                var stickyTop = jQuery(this).attr('data-sticky-top');
                var stickyWidth = jQuery(this).attr('data-sticky-width');
                var stickyHeight = jQuery(this).attr('data-sticky-height');
                jQuery(this).css({'position':'fixed','top':stickyTop - stickyVars.nudge, 'right': 15,'left': '', 'width': stickyWidth, 'height': stickyHeight});
            });
            jQuery( '.sticky-padding' ).show();
			
		}else if(jQuery(document).scrollTop()  < stickyVars.nudge && stickyVars.stuck == true) {
			stickyVars.stuck = false;
			jQuery( '.sticky-padding' ).hide();
            jQuery('.lava-sticky').each(function(){
                var random = jQuery(this).attr('data-sticky');
                var stickyLeft = jQuery(this).attr('data-sticky-left');
                var stickyTop = jQuery(this).attr('data-sticky-top');
                var stickyWidth = jQuery(this).attr('data-sticky-width');
                var stickyHeight = jQuery(this).attr('data-sticky-height');
                jQuery(this).css({'position':'relative','top':0, 'left':0, 'width': '', 'height': ''});
            });
		}
		
		//console.log(jQuery('#sticky-nav').css('opacity'))
	
	});
}