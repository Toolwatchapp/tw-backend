/*!
 * ToolwatchApp v1.0 (http://www.toowatch.io)
 * Copyright 2015 ToolwatchApp 
 */

$(document).ready(function() 
{	
    resizeContent();
    $( window ).resize(function() 
    {
       resizeContent();
    });
    
    setInterval("changeBackground()", 7000);
    
    
    $(window).scroll(function()
    {
        if(window.location.pathname == "/")
        {
             if( $(window).scrollTop() >= '70')
            {
                $('header').addClass('blue');   
            }
            else
            {
                $('header').removeClass('blue');   
            }
        }
        
    });
   
    
    /*
     * Modal Update
     */
	$('body').on('click', 'a[data-modal-update="true"]', function()
	{
		var dataHref = $(this).attr("data-href");
		$.post(dataHref, {ajax: true}, function(data)
		{
			$('#pageModal .modal-body').html(data);
		});
	});
	
	$('#pageModal').on('hidden.bs.modal', function (e) 
	{
		$.post('/login/', {ajax: true}, function(data)
		{
			$('#pageModal .modal-body').html(data);
		});
	})
	
    /*
     * Next step on signup form
     */
    $('body').on('click', 'a.signupNextStep', function()
	{
		$('.signup-error').hide();
        
        var userEmail = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var confirmPassword = $('input[name="confirmPassword"]').val();
        var mailingList = $('input[name="malingList"]').is(':checked');
        
        if(validateEmail(userEmail))
        {
            if((password.length >= 6) && (password != '')) 
            {
                if(confirmPassword == password)
                {
                    $.post('/ajax/checkEmail', {email: userEmail}, function(data)
                    {
                        var result = $.parseJSON(data);
                        if(result.success == true)
                        {
                            $('.stepOne').hide();
                            $('.stepTwo').show();
                        }
                        else
                        {
                            $('.email-error').html('This email is already taken.').show();
                        }
                    });
                }
                else
                {
                    $('.confirm-password-error').html('Your password doesn\'t match.').show();
                }
            }
            else
            {
               $('.password-error').html('Your password should be at least 6 characters long.').show();
            }
            
        }
        else
        {
            $('.email-error').html('It seems that you\'ve entered a wrong email.').show();
        }
	});
    
    /*
     * Submit login
     */
    $('body').on('submit', 'form[name="login"]', function(e)
    {
        e.preventDefault();
        $(this).addClass('active');
        $('.btn-spinner i').css('display', 'inline-block');
        $('.signup-error').hide();
        
        var email = $('input[name=email]').val();
        var password = $('input[name=password]').val();
        
        $.post('/ajax/login', {email: email, password: password}, function(data)
        {
            var result = $.parseJSON(data);
            if(result.success == true)
            {
                setTimeout('window.location.replace("/measures/")', 1000);
            }
            else
            {
                $(this).removeClass('active');
                $('.btn-spinner i').css('display', 'none');
                $('.login-error').html('Wrong email and/or password.').show();
            }
        });
    });
    
    /*
     * Submit signup
     */
    $('body').on('submit', 'form[name="signup"]', function(e)
    {
        e.preventDefault();
        var email = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var name = $('input[name="name"]').val();
        var firstname = $('input[name="firstname"]').val();
        var timezone = $('select[name="timezone"]').val();
        var country = $('select[name="country"]').val();
        var mailingList = $('input[name="malingList"]').is(':checked');
        
        $(this).addClass('active');
        $('.btn-spinner i').css('display', 'inline-block');
        $('.signup-error').hide();
        
        $.post('/ajax/signup', {email: email, password: password, name: name, firstname: firstname, timezone: timezone, country: country, mailingList: mailingList}, function(data)
        {
            var result = $.parseJSON(data);
            if(result.success == true)
            {
               $.post('/sign-up-success/', {ajax: true}, function(data)
                {
                    $('#pageModal .modal-body').html(data);
                    setTimeout('window.location.replace("/measures/")', 5000);
                });
            }
            else
            {
                $(this).removeClass('active');
                $('.btn-spinner i').css('display', 'none');
                $('.global-error').html('Something went wrong... Try again later.').show();
            }
        });
    });
    
    /*
     * Submit ask reset password
     */
    $('body').on('submit', 'form[name="askResetPassword"]', function(e)
    {
        e.preventDefault();
        
        $('.signup-error').hide();
        var email = $('input[name="email"]').val(); 
        
        if(validateEmail(email))
        {
            $('.btn-spinner i').css('display', 'inline-block');
            
            $.post('/ajax/askResetPassword', {email: email}, function(data)
            {
                var result = $.parseJSON(data);
                if(result.success == true)
                {
                    $('.askReset').hide();
                    $('.confirmAskReset').show();
                }
                else
                {
                    $('.reset-error').html('Something went wrong. Did you miss spell your email?').show();
                    $('.btn-spinner i').css('display', 'none');
                }
                
            });
        }
        else
        {
            $('.reset-error').html('It seems that you\'ve entered a wrong email.').show();   
        }
    });
    
    
    /*
     * Submit reset password
     */
    $('body').on('submit', 'form[name="resetPassword"]', function(e)
    {
        e.preventDefault();
        $('.signup-error').hide();
        $('.alert-danger').hide();
        $('.alert-success').hide();
        var password = $('input[name="password"]').val(); 
        var confirmPassword = $('input[name="confirmPassword"]').val(); 
        var resetToken = $('input[name="resetToken"]').val(); 
        
        if((password.length >= 6) && (password != '')) 
        {
            if(confirmPassword == password)
            {
                $.post('/ajax/resetPassword', {resetToken: resetToken, password: password}, function(data)
                {
                    var result = $.parseJSON(data);
                    if(result.success == true)
                    {
                        $('.alert-success').show();
                        setTimeout('window.location.replace("/")', 5000);
                    }  
                    else
                    {
                         $('.alert-danger').show();
                    }
                });
            }
            else
            {
                $('.confirm-password-error').html('Your password doesn\'t match.').show();
            }
        }
        else
        {
           $('.password-error').html('Your password should be at least 6 characters long.').show();
        }
    });
    
    $('body').on('click', 'button[name="startSync"]', function(e)
    {
        e.preventDefault();
        var watchId = $('select[name="watchId"]').val();
        $('.watch-error').hide(); 

        if(watchId != null)
        {
            $('button[name="startSync"]').hide();
            $('button[name="syncDone"]').show();
            $('.sync-time').show();
            $('button[name="restartCountdown"]').show();
            $('.watch-select').hide();

            syncInterval = setInterval("syncCountdown()", 1000);
            var audio = new Audio('/assets/audio/bips.mp3');
            audio.play();
        }
        else
        {
           $('.watch-error').show(); 
        }
        
    });

    $('body').on('submit', 'form[name="addWatch"]', function(e)
    {
        
        $('.watch-error').hide();

        var brand = $('input[name="brand"]').val();

        if(brand == ""){
            $('.brand-error').show();
             e.preventDefault();
        }

     });
    
    $('body').on('submit', 'form[name="newMeasure"]', function(e)
    {
        e.preventDefault();
        $('.signup-error').hide();
        
        var watchId = $('select[name="watchId"]').val();
        var userTime = $('input[name="userTime"]').val();
        var getAccuracy = $('input[name="getAccuracy"]').val();
        
        var myDate = new Date();
        // Timezone difference from Europe/Paris
        var userTimezone= (myDate.getTimezoneOffset()/60)+1;
                
        if(/\d+:\d+:\d+/.test(userTime))
        {
            $('.btn-spinner i').css('display', 'inline-block');
            
            $.post('/ajax/newMeasure', {watchId: watchId, userTime: userTime, userTimezone: userTimezone, getAccuracy: getAccuracy}, function(data)
            { 
                var result = $.parseJSON(data);
                if(result.success == true)
                {
                    $('.userTime').hide();
                    $('button[name="syncDone"]').hide();
                    $('.sync-time').hide();
                    $('button[name="restartCountdown"]').hide();
                    $('.sync-success').show();
                    $('.backToMeasure').show();
                    
                    if(result.data.accuracy != null)
                    {
                        if(result.data.accuracy > 0)
                            result.data.accuracy = '+'+result.data.accuracy;
                        
                        $('.watch-accuracy').html(result.data.accuracy);
                    }
                }  
                else
                {
                    $('.measure-error').show();
                    $('.btn-spinner i').css('display', 'none');
                }
                
            });        
        }
        else
        {
            $('.time-error').show();
        }
    });
    
    $('body').on('submit', 'form[name="contact"]', function(e)
    {
        e.preventDefault();
        $('.alert').hide();
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var message = $('textarea[name="message"]').val();
        
        $.post('/ajax/contact', {name: name, email: email, message: message}, function(data)
        {
            var result = $.parseJSON(data);
            if(result.success == true)
            {
                $('.alert-success').show();
                $('input[name="name"]').val('');
                $('input[name="email"]').val('');
                $('textarea[name="message"]').val('');
            }
            else
            {
                $('.alert-danger').show();
            }
        });
    });
    
    $('body').on('click', '.home-intro .continue', function(e)
     {
        var pictoScroll = $('.home-picto').offset();
        $('html, body').animate({scrollTop: pictoScroll.top-100}, 1000);
    });
        
    $('body').on('click', 'button[name="restartCountdown"]', function(e)
    {
        e.preventDefault();
        if($('.sync-time').html() == 'Go!')
        {
            $('.userTime').hide();
            $('button[name="startSync"]').trigger('click');
        }
        
        $('.sync-time').html('5');
    });
    
    $('body').on('click', '.submitGetAccuracy', function(e)
    {
        e.preventDefault();
        var watchId = $(this).attr('data-watch');
        
        $('form[name="get-accuracy-'+watchId+'"]').submit();
    });
    
    $('body').on('click', '.submitDeleteWatch', function(e)
    {
        e.preventDefault();
        var watchId = $(this).attr('data-watch');
        
        if(confirm('Are you sure you want to delete this watch?'))
        {
            $('form[name="delete-watch-'+watchId+'"]').submit();
        }
    });
    
    $('body').on('click', '.submitDeleteMeasures', function(e)
    {
        e.preventDefault();
        var watchId = $(this).attr('data-watch');
        
        if(confirm('Are you sure you want to delete this measures?'))
        {
            $('form[name="delete-measures-'+watchId+'"]').submit();
        }
    });
    
    $('body').on('click', '[data-trigger="slideDown"]', function(e)
    {
        e.preventDefault();
        var target = $(this).attr('data-target');
        
        if($(target).css('display') == 'none')
        {
            $(target).slideDown();
            $(this).html('Hide Steps');
        }
        else
        {
            $(target).slideUp();
            $(this).html('Show Steps');
        }
    });
        
});

var syncInterval = 0;

/*
 * Validate email format
 */
function validateEmail(email)
{
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function resizeContent()
{
    var windowHeight = $(window).height();   
    var windowWidth = $(window).width();   
    var headerHeight = $('header').height();   
    var footerHeight = $('footer').height();   
        
    $('.content').css('min-height', (windowHeight-(headerHeight-30)-footerHeight)+'px');
    $('.home-intro, .home-intro-overlay').css('min-height', windowHeight+'px');
}


var bips = new Audio('/assets/audio/bips.mp3');
var lastBip = new Audio('/assets/audio/last-bip.mp3');

function syncCountdown()
{
    var countdown = $('.sync-time').html();
    if((countdown-1) > 0)
    {
        bips.play();
        $('.sync-time').html(countdown-1);
    }
    else
    {
        lastBip.play();
        clearInterval(syncInterval);
        syncInterval = 0;
        
        $('.sync-time').html('Go!');
        $('.userTime').show();
        $('button[name="syncDone"]').removeAttr('disabled');
        
        $.post('/ajax/getReferenceTime');
    }
}

var currentBg = 0;

function changeBackground()
{
    
    currentBg = (currentBg+1)%4;
    var bgNumber = currentBg+1;
    $('.home-intro').css('background-image', 'url("/assets/img/home_'+bgNumber+'.jpg")');
}