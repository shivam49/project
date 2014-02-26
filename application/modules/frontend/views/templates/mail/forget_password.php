
<?php if($this->user_list['eType'] == 'Fan'){
    $name = $this->user_list['vName'].' '.$this->user_list['vLastname'];
}
if($this->user_list['eType'] == 'Band'){
    $name = $this->user_list['vTitle'];
}
?>

<div>
   <h5>Dear <?php print $name ?>,</h5> 
</div>

<div>
<p>You have requested to reset your password on Uproar Festival Battle of the Bands because you have forgotten your password. If you did not request this, please ignore it.

To reset your password, please visit the following page:</p>
</div>
<div>
<a href="http://www.8bitinc.com/projectx/forget-password/confirm/user/<?php echo $this->user_list['iId'];?>/act/c/key/<?php echo $this->user_list['password']; ?>/security/<?php echo $this->user_list['security']; ?>">Click here to confirm your new password!</a>
</div>
<div>
<p>When you visit that page, your password will be reset, and the new password will be emailed to you.</p>
</div><div>
<h6>Your username is: <?php echo $this->user_list['vEmail']; ?></h6>
</div>

