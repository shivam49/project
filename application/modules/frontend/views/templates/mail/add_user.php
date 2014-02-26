
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
<p>Thanks for registering at Uproar Festival Battle of the Bands! We are glad you have chosen to be a part of our community and we hope you enjoy your stay.</p>
<a href="http://www.8bitinc.com/projectx/login">Login</a>

