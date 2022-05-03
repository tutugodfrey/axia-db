<span class="contentModuleHeader">Additional Comments About Your Change</span><br>
<span class="subduedText">Submit a description of your change here. The description will accompany the change request in the notes section.</span><br>                            
<?php
/* Merchant hasMany MerchantNote but we are only saving one therefore field index is 0 */
echo $this->form->hidden('MerchantNote.0.note_type_id');
echo $this->form->hidden('MerchantNote.0.user_id');
echo $this->form->hidden('MerchantNote.0.merchant_id');
echo $this->form->hidden('MerchantNote.0.note_date');
echo $this->form->hidden('MerchantNote.0.note_title');
echo $this->form->hidden('MerchantNote.0.general_status');
echo $this->form->hidden('MerchantNote.0.loggable_log_id');
echo $this->form->input('MerchantNote.0.note', array(
	'label' => false,
	'div' => false,
	'required' => true,
	'rows' => "8",
	'cols' => "80"
));
