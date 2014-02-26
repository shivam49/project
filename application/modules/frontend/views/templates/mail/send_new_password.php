
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
<p>Password change successfully</p>
</div>'


