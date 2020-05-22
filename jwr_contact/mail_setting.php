<?php
if (!defined('WP_ADMIN')) {
    die('Invalid request.');
}
$is_update = false;
if(isset($_POST['submit'])){
    unset($_POST['submit']);
    update_option('smtp_666', $_POST);
    $is_update = true;
}
$opt = get_option('smtp_666',[]);
$smtp_host = isset($opt['smtp_host']) ? $opt['smtp_host'] : '';
$smtp_port = isset($opt['smtp_port']) ? $opt['smtp_port'] : 465;
$smtp_encrypt = isset($opt['smtp_encrypt']) ? $opt['smtp_encrypt'] : 'ssl';
$smtp_auth = isset($opt['smtp_auth']) ? $opt['smtp_auth'] : 1;
$smtp_username = isset($opt['smtp_username']) ? $opt['smtp_username'] : '';
$smtp_password = isset($opt['smtp_password']) ? $opt['smtp_password'] : '';
?>
<div class="wrap">
    <h1>Mail Setting</h1>
    <?php if ($is_update) : ?>
        <div class="updated notice is-dismissible">
            <p><strong>Mail Setting</strong> Updated</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Ignore this message.</span></button>
        </div>
    <?php endif ?>
    <form method="POST" action="#">
        <table class="form-table">
            <tr>
                <th scope="row"><label>SMTP HOST</label></th>
                <td>
					<input name="smtp_host" value="<?=$smtp_host?>" class="regular-text" type="text">
                    <p class="description">
                        Only support smtp protocal to send, such as:smtp.mail.yahoo.com<br>
                        Yahoo:smtp.mail.yahoo.com<br>
                        Google:smtp.gmail.com
                    </p>
				</td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP PORT</label></th>
                <td>
                    <input name="smtp_port" value="<?=$smtp_port?>" class="regular-text" type="text">
                    <p class="description">
                        Yahoo:465<br>
                        Google:465
                    </p>
				</td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP ENCRYPTION</label></th>
                <td>
                    <label><input name="smtp_encrypt" value="" class="regular-text" <?=($smtp_encrypt=='' ? 'checked':'')?> type="radio">NONE</label>
                    <label><input name="smtp_encrypt" value="ssl" class="regular-text" <?=($smtp_encrypt=='ssl' ? 'checked':'')?> type="radio">SSL/TLS</label>
				</td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP AUTH</label></th>
                <td>
                    <label><input name="smtp_auth" value="0" class="regular-text" <?=($smtp_auth==0 ? 'checked':'')?> type="radio">NO</label>
                    <label><input name="smtp_auth" value="1" class="regular-text" <?=($smtp_auth==1 ? 'checked':'')?> type="radio">YES</label>
				</td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP USERNAME</label></th>
                <td>
                    <input name="smtp_username" value="<?=$smtp_username?>" class="regular-text" type="text">
                    <p class="description">your email</p>
				</td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP PASSWORD</label></th>
                <td>
                    <input name="smtp_password" value="<?=$smtp_password?>" class="regular-text" type="password">
                    <p class="description">
                        Attention：This is not the log in password of the email address<br>
                        Yahoo Special Password<a target="_blank" href="https://help.yahoo.com/kb/account/set-manage-yahoo-account-key-sign-password-sln25781.html">Application Site</a><br>
                        Google Special Password<a target="_blank" href="https://myaccount.google.com/security">Application Site</a>
                    </p>
				</td>
			</tr>

        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="SAVE">
        </p>
    </form>
</div>