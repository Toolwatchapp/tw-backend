  <?php echo form_open('', array('name'=>'askResetPassword'));?>

    <div class="form-group">
		<h1>Reset password</h1>
	</div>
    <fieldset class="askReset">
        <div class="form-group">
            <input type="email" class="form-control" name="email" placeholder="Login (email)" autofocus="true">
            <span class="signup-error reset-error"></span>
        </div>
        <div class="form-group">
            <center><button type="submit" class="btn btn-primary btn-lg btn-spinner">Reset password <i class="fa fa-spinner fa-pulse"></i></button></center>
        </div>
    </fieldset>
    <fieldset class="confirmAskReset">
        <div class="form-group">
            <center>We've sent you an email to reset your password.</center>
        </div>
    </fieldset>
	<div class="form-group">
		  <center>Oops! I remember it now... <br>So, <a data-modal-update="true" data-href="/login/">log me in!</a></center>
	</div>
</form>
