<div class="form-group">
  Login in allows you to access and track all your measures in one place.
</div>
<div class="form-group">
   <center>

     <a style="padding:15px; color:white; background-color: #36528c; width:100%;"
     href="#" onclick="fb_login();" class="col-md-12">
       <i class="fa fa-facebook" aria-hidden="true"></i>&nbsp;&nbsp;Log in with Facebook
     </a>

     <br>
     <br>
     <br>
   </center>
   <span id="fb_error" class="signup-error login-error"></span>
     <strong class="line-thru">or with email</strong>
</div>


<?php echo form_open('', array('name'=>'login'));?>

    <div class="form-group">
       <strong class="line-thru"></strong>
    </div>
	<div class="form-group">
        <span id="email_error" class="signup-error login-error"></span>
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
		  <center>Not registered yet? <a class="signup-here" data-href="/sign-up/" data-modal-update="true">Sign up here!</a> <br />
		</center>
	</div>
</form>
