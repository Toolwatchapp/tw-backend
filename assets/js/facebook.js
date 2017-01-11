
$(document).ready(function() {
        $.ajaxSetup({ cache: true });
        $.getScript('//connect.facebook.net/en_US/sdk.js', function(){
            FB.init({
            appId: '{807383452677000}',
            version: 'v2.7' // or v2.1, v2.2, v2.3, ...
            });     
            FB.getLoginStatus(statusChangeCallback);
        });
  });

var userInitiatedFbLogin = false;

// This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected' && userInitiatedFbLogin === true) {
      // Logged into your app and Facebook.
      sendLoginFb();
    } 
  }

  function fb_login(){

    userInitiatedFbLogin = true;

    FB.login(function(response) {
      if (response.status === 'connected') {
        sendLoginFb();
      } else if (response.status === 'not_authorized') {
        // The person is logged into Facebook, but not your app.
      } else {
        // The person is not logged into Facebook, so we're not sure if
        // they are logged into this app or not.
      }
    }, {scope: 'public_profile,email'});
  }

  

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

// Here we run a very simple test of the Graph API after login is
// successful.  See statusChangeCallback() for when this call is made.
function sendLoginFb() {
  FB.api('/me', function(response) {

    $.post('/ajax/facebookSignup', {email: response.email, last_name: response.last_name, firstname: response.first_name, timezone: response.timezone, country: response.country}, function(data)
    {
          var result = $.parseJSON(data);
          if(result.success == "signup")
          {
            $('#pageModal .modal-body').html(result.thanks);
            setTimeout('window.location.replace("/measures/")', 5000);

          }else if(result.success == "signin"){
              setTimeout('window.location.replace("/measures/")', 1000);
          }else if(result.success == "email"){
              $('#fb_error').html('You already have an email account, please use it to connect.').show();
          } else {
              $('#fb_error').html('Something went wrong... Try again later.').show();
          }
    });
    console.log(response);
  });
}