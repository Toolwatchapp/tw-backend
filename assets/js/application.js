/*!
 * ToolwatchApp v1.0 (http://www.toowatch.io)
 * Copyright 2015 ToolwatchApp 
 */

$(document).ready(function() 
{	
	$('body').on('click', 'a[data-modal-update="true"]', function()
	{
		var dataHref = $(this).attr("data-href");
		$.get(dataHref, {ajax: true}, function(data)
		{
			$('#pageModal .modal-body').html(data);
		});
	});
	
	$('#pageModal').on('hidden.bs.modal', function (e) 
	{
		$.get('/login/', {ajax: true}, function(data)
		{
			$('#pageModal .modal-body').html(data);
		});
	})
	
    $('body').on('click', 'a.signupNextStep', function()
	{
		$('.signup-error').hide();
        
        var userEmail = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var confirmPassword = $('input[name="confirmPassword"]').val();
        
        if(validateEmail(userEmail))
        {
            if((password.length >= 6) && (password != "")) 
            {
                if(confirmPassword == password)
                {
                    $('.stepOne').hide();
                    $('.stepTwo').show();
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
});

function validateEmail(email)
{
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}
 /*   
	
submitSignup = function()
    {
        $('.signup-error').hide();
        var userEmail = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var name = $('input[name="name"]').val();
        var birthDate = $('input[name="birthDate"]').val();
        
        if(name != "")
        {
            if(birthDate != "")
            {
                   
            }
            else
            {
                $('.birthDate-error').html('It seems that you\'ve entered a wrong birthdate').show();
            }
        }
        else
        {
            $('.name-error').html('We would like to have your name, can we?').show();
        }
    };*/