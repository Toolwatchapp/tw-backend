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
    
    setInterval("changeBackground()", 30000);
    
    
    $(window).scroll(function()
    {
        if(window.location.href == "")
        {
            if( $(window).scrollTop() >= '100')
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
        
        if(validateEmail(userEmail))
        {
            if((password.length >= 6) && (password != '')) 
            {
                if(confirmPassword == password)
                {
                    $.post('/ajax/checkEmail', {email: userEmail}, function(data)
                    {
                        if(data.indexOf('SUCCESS') >= 0)
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
            if(data.indexOf('SUCCESS') >= 0)
            {
                setTimeout('window.location.replace("/")', 1000);
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
        
        $(this).addClass('active');
        $('.btn-spinner i').css('display', 'inline-block');
        $('.signup-error').hide();
        
        $.post('/ajax/signup', {email: email, password: password, name: name, firstname: firstname, timezone: timezone, country: country}, function(data)
        {
            if(data.indexOf('SUCCESS') >= 0)
            {
               $.post('/sign-up-success/', {ajax: true}, function(data)
                {
                    $('#pageModal .modal-body').html(data);
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
        
        $.post('/ajax/askResetPassword', {email: email}, function(data)
        {
            console.log(data);
            if(data.indexOf('SUCCESS') >= 0)
            {
               $('.askReset').hide();
               $('.confirmAskReset').show();
            }  
            else
            {
                 $('.reset-error').html('Something went wrong. Did you miss spell your email?').show();
            }
        });
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
                    if(data.indexOf('SUCCESS') >= 0)
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
        $('button[name="startSync"]').hide();
        $('button[name="syncDone"]').show();
        $('.sync-time').show();
        $('.watch-select').hide();
        
        syncInterval = setInterval("syncCountdown()", 1000);
    });
    
    $('body').on('submit', 'form[name="newMeasure"]', function(e)
    {
        e.preventDefault();
        $('.signup-error').hide();
        
        var watchId = $('select[name="watchId"]').val();
        var userTime = $('input[name="userTime"]').val();
        var myDate = new Date();
        // Timezone difference from Europe/Paris
        var userTimezone= (myDate.getTimezoneOffset()/60)+1;
                
        if(/\d+:\d+:\d+/.test(userTime))
        {
            $.post('/ajax/newMeasure', {watchId: watchId, userTime: userTime, userTimezone: userTimezone}, function(data)
            { 
                if(data.indexOf('SUCCESS') >= 0)
                {
                    $('.userTime').hide();
                    $('button[name="syncDone"]').hide();
                    $('.sync-time').css('font-size', '14px').html('Congratulations, you watch is now synchronized. Please come back in a few days so we can measure if your watch is still accurate.<br><br>Caution : Your watch must NOT stop running until then!');
                }  
                else
                {
                    $('.measure-error').show();
                }
                
                console.log(data);
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
            console.log(data);
            if(data.indexOf('SUCCESS') >= 0)
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
    var headerHeight = $('header').height();   
    var footerHeight = $('footer').height();   
    
    $('.content').css('min-height', (windowHeight-(headerHeight-30)-footerHeight)+'px');
    $('.home-intro, .home-intro-overlay').css('min-height', windowHeight+'px');
}

function syncCountdown()
{
    var countdown = $('.sync-time').html();
    if((countdown-1) > 0)
    {
        $('.sync-time').html(countdown-1);
    }
    else
    {
        clearInterval(syncInterval);
        syncInterval = 0;
        
        $('.sync-time').html('Go!');
        $('.userTime').show();
        $('button[name="syncDone"]').removeAttr('disabled');
        
        $.post('/ajax/getReferenceTime');
    }
}

function changeBackground()
{
    var bgNumber = Math.floor(Math.random()*3)+1;
    $('.home-intro').css('background-image', 'url("/assets/img/home_'+bgNumber+'.jpg")');
}