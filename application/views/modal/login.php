<div class="form-group">
   <center><button onclick="fb_login();" class="btn btn-primary btn-lg btn-spinner btn-full">Log in with Facebook<i class="fa fa-spinner fa-pulse"></i></button></center>
</div>

<form method="post" name="login">

    <div class="form-group">
       <strong class="line-thru">or</strong>
    </div>
	<div class="form-group">
        <span class="signup-error login-error"></span>
		<input type="email" class="form-control" name="email" placeholder="Login (email)" autofocus="true" required>
	</div>
	<div class="form-group input-group">
		<input type="password" class="form-control" name="password" placeholder="Password" required>
		<div class="input-group-addon">
			<a data-modal-update="true" data-href="/reset-password/" title="Forgot password?"><span class="fa fa-question-circle"></span></a>
		</div>
	</div>
	<div class="form-group">
		<center><button type="submit" name="login" class="btn btn-primary btn-lg btn-spinner btn-full">Log in <i class="fa fa-spinner fa-pulse"></i></button></center>
	</div>
	<div class="form-group">
		  <center>Not registered yet? <a data-href="/sign-up/" data-modal-update="true">Sign up here!</a> or <br />
		</center>
	</div>  
</form>