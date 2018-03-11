<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12"><center><h1>Reset your password</h1></center></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <center>

              <?php echo form_open('', array('name'=>'resetPassword', 'class'=>'col-md-6 col-md-offset-3'));?>
                    <div class="alert alert-danger alert-dismissible" role="alert" style="display: none";>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <span>We cannot reset the password for this token (<?php echo $resetToken;?>). Please, check the link you received or ask a reset again.</span>
                    </div>
                    <div class="alert alert-success alert-dismissible" role="alert" style="display: none";>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <span>Hurray, you've got your account back! You'll be redirected in 5 seconds to the home page. Happy toolwatching!</span>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Set a password">
                        <span class="signup-error password-error"></span>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm your password">
                        <span class="signup-error confirm-password-error"></span>
                    </div>
                    <div class="form-group">
                        <center>
                            <input type="hidden" name="resetToken" value="<?php echo $resetToken; ?>">
                            <button type="submit" class="btn btn-primary btn-lg">Reset password</button>
                        </center>
                    </div>
                </form>
            </center>
        </div>
    </div>
</div>
