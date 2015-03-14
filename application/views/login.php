<form>
	<div class="form-group">
		<input type="email" class="form-control" name="email" placeholder="Login (email)" autofocus="true" required>
	</div>
	<div class="form-group input-group">
		<input type="password" class="form-control" name="password" placeholder="Password" required>
		<div class="input-group-addon">
			<a data-modal-update="true" data-href="/reset-password/" title="Forgot password?"><span class="fa fa-question-circle"></span></a>
		</div>
	</div>
	<div class="form-group">
		<center><button type="submit" name="login" class="btn btn-primary btn-lg">Log in</button></center>
	</div>
	<div class="form-group">
		  <center>Not registered yet? <a data-href="/sign-up/" data-modal-update="true">Sign up here!</a></center>
	</div>  
</form>