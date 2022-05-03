<div class="row">
	<div style="width:70%" class="center-block">
		<div class="col-md-12">
			<h2><strong>Welcome</strong></h2>
			<p>
				The Data Warehouse is a client-side system used to access current and historical data stored in one single place as well as reporting and data analysis, and is considered one of the organization's core components of business intelligence.<br>
				This documentation provides information about the data warehouse’s client-side features and workflows. Please contact <a href="mailto:webmaster@axiatech.com">webmaster@axiatech.com</a> to suggest adding information which will help answer questions about the site’s functionality that are not already covered herein.
			</p>
			<hr/>
			<hr/>
			<h3><a name='newAcct'></a><strong>Create New Merchants Accounts:</strong></h3>
			<p>
				New merchant or client accounts are created by uploading all their business and account setup information using a CSV file. The CSV file may originate from either Axia's <a href="https://app.axiatech.com/users/login" target='_blank'>online application and e-signature system</a>, or from data exported from a PDF application, or it can even be from a manually created CSV file.<br/>

				<?php
				echo $this->Element('HelpDocs/noteWarning', ['noteText' => __('<strong>Uploading a CSV file twice with the same merchant data will cause the merchant account that was created on the first attempt to be completely deleted and recreated! </strong>')]);
				?>
				<h5><strong><u>Required Configuration:</u></strong></h5>
				The following configuration requirements must be met in order to successfully create a merchant account:
				<ul>
					<li>
						<strong>Properly Setup User Compensation:</strong> All users that are associated with the merchant (Reps/Partners/Referrers/Resellers) account being created must have properly setup <a href='#UCP'>User Compensation Profiles (UCP).</a> 
					</li>
					<li>
						<strong>Properly Configured User Associations:</strong> If a partner, referrer and/or reseller are associated with the merchant then the Representative must have a UCP associated with those users. Particularly when a partner is involved, the Rep must have a <a href='#UCP::CreatePartnerRep'>Partner-Rep UCP</a> where the rep and the partner are associated. Additionally, any managers that the rep might have must also be associated with the rep in that Representative's user compensation profile.
					</li>
					<li>
						<strong>Rate Structures: </strong> Rate structure and Qualification Exceptions names in the CSV file must exactly match those defined in the database. These values will determine the BET that will be assigned to the merchant.
					</li>
					<li>
						<strong>Product Configuration: </strong> Which products are added automatically to new merchant accounts during the upload is determined by the data in the CSV file and by how the system was configured to assign products to new merchants.
						<br/>The following table shows the current system configuration that determines how products are added. For example, when "<strong>Always</strong>" is set, the product will always be activated if the merchant accepted to use that product, which is determined from the CSV file information. The "<strong>Always</strong>" directive has precedence over all the other configuration directives and works as an override when it is set.
						<br/>When the product is not configured to "<strong>Always</strong>" be added then the other configuration options will determine when the product will be activated; for example the product will only be added to the merchant when it is <strong>Enabled for rep</strong>" in their UCP.
						<br/> If more than one non-conflicting configuration option is set, then they will be combined to determine which products are activated for the merchant account being created.
						<?php
							$prodConfig = Configure::read('OnUploadMerchantProducts');
							$prodConfig = Hash::extract($prodConfig, '{s}.{n}');
							$rows = [];
							foreach ($prodConfig as $prodCfg) {
								$rows[] = array_merge([Hash::get($prodCfg, 'product_name')], Hash::extract($prodCfg, 'activate_when.{s}'));
							}
							$headers = array_merge(['product_name'], array_keys(Hash::extract($prodCfg, 'activate_when')));
							$headers = array_map('Inflector::humanize' , $headers);

							echo $this->Element('HelpDocs/noteInfo', ['noteText' => __('<strong>Only system super admins can modify this configuration. Configuration definition is located in app_config.php</strong>')]);
							echo $this->Html->tag('table', 
								$this->Html->tableHeaders($headers) . 
								$this->Html->tableCells($rows) ,['class' => 'table table-bordered table-hover table-condensed']);
						?> 
					</li>
				</ul> 
			</p>

			<h4><a name='newAcct::gw1'></a><strong>Create Gateway 1 Merchant Accounts:</strong></h4>
			<p>
				In order to add merchants with the Gateway 1 product or a non-acquiring gateway-only merchant the following CSV header and corresponding data must be present in the CSV file:
				<?php
					$Merchant = ClassRegistry::init('Merchant');
					echo $this->Html->tag('table', 
							$this->Html->tableHeaders(['Header Name:']) . 
							$this->Html->tableCells(array_map(function ($val){return [$val];}, $Merchant->gw1CsvHeaders)) ,['class' => 'col-md-4 col-sm-4 table-bordered table-hover table-condensed']);
					echo $this->Html->tag('div', null, ['class' => 'clearfix']) . $this->Html->tag('/div');
				?>
				<br/><strong>Important Notes:</strong>
				<br/><strong>"Gateway"</strong> header and a value are required and the accepted values for the "Gateway" CSV column are "YES"/"On"/"TRUE", which indicate that the client wants to use this product.<br/>
				<strong>"Gateway_ID"</strong> header and a value are required. This is a numerical identifier at least 5 digits long.<br/>
				<strong>"Gateway_Name"</strong> header and a value are required. The gateway names must exactly match the existing gateway names in the database.
				<br/>All Other Headers are optional.

			</p>
			<h4><a name='newAcct::Pfusion'></a><strong>Create Payment Fusion Merchant Accounts:</strong></h4>
			<p>
				In order to add merchants with the Payment Fusion product, the following CSV header and corresponding data must be present in the CSV file:
				<?php
					echo $this->Html->tag('table', 
							$this->Html->tableHeaders(['Header Name:']) . 
							$this->Html->tableCells(array_map(function ($val){return [$val];}, $Merchant->pmntFusionCsvHeaders)) ,['class' => 'col-md-4 col-sm-4 table-bordered table-hover table-condensed']);
					echo $this->Html->tag('div', null, ['class' => 'clearfix']) . $this->Html->tag('/div');
				?>
				<br/><strong>Important Notes:</strong>
				<br/><strong>"PaymentFusion"</strong> header and a value are required and the accepted values for the "PaymentFusion" CSV column are "YES"/"On"/"TRUE", which indicate that the client wants to use this product.<br/>
				<strong>"PF_ID"</strong> header and a value are required. This is a numerical identifier at least 6 digits long.
				<br/>All Other Headers are optional.
			</p>
			<h4><a name='newAcct::achProd'></a><strong>Create Merchant Accounts with ACH product:</strong></h4>
			<p>
				In order to add merchants with the ACH product, the following CSV header and corresponding data must be present in the CSV file:
				<?php
					echo $this->Html->tag('table', 
							$this->Html->tableHeaders(['Header Name:']) . 
							$this->Html->tableCells(array_map(function ($val){return [$val];}, $Merchant->achCsvHeaders)) ,['class' => 'col-md-4 col-sm-4 table-bordered table-hover table-condensed']);
					echo $this->Html->tag('div', null, ['class' => 'clearfix']) . $this->Html->tag('/div');
				?>
				<br/><strong>Important Notes:</strong>
				<br/><strong>"ach_accepted"</strong> header and a value are required and the accepted values for the "ach_accepted" CSV column are "YES"/"On"/"TRUE", which indicate that the client wants to use this product.<br/>
				<strong>"ach_provider_name"</strong> header and a value are required. The provider names must exactly match the existing ACH provider names in the database.
				<br/>All Other Headers are optional.
			</p>
			<hr/>
			<h3><a name='addingProductsM'></a><strong>Adding Products to Merchants:</strong></h3>
			<p>
				To add a single product to a merchant, simply click on the product under the inactive products list and click ok when asked to confirm.	
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image002.png']);
			?>
			<p>
				Some products require configuring their own pricing settings; if that's the case for the product being added, you will be redirected to the product's settings where you can configure the product's pricing. Note however that if the merchant had pending changes prior to adding the product, the product will be added but you will get a notification message stating that the merchant has pending changes and you will not be automatically redirected to the product configuration page. You will have to approve any pending changes, return to Products and Services, edit the product and enter the product's settings.	
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image004.png']);
			?>

			<p>
				To add multiple products select the products using the checkboxes. When more than one product is selected the green “Activate” button will become enabled so selected products can be added.
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image006.png']);
			?>
			<p>
				Depending on which products are added, some (but not all) might require manually configuring their pricing which must be done after the system adds all the selected products to the merchant. In general, you can identify products that need to be configured by their new individual sections which will appear at the bottom of the products and services section containing no data, as illustrated below:
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image008.png']);
			?>
			<hr/><!--END SEGMENT-->
			<h3><a name='removingProductsM'></a><strong>Deactivating Products from Merchants:</strong></h3>
			<p>
				To deactivate products from merchants, follow the same instructions listed above for activating products but select active products instead. When a product that has its own pricing data is deactivated from a merchant, the product will be deactivated but the data will not be deleted. The data will remain stored in the database for historical purposes. If the same product is reactivated for the same merchant in the future, that product's stored pricing data will be re-enabled and will reappear on the merchant's products and services view and will need to be updated.
			</p>
			<hr/><!--END SEGMENT-->
			<h3><a name='cancellations'></a><strong>Merchant Cancellations:</strong></h3>
			<strong>Current Cancellation: </strong>This represents cancellation data of a merchant that is currently inactive due to a cancellation.<br>
			<strong>Previous Cancellations History: </strong> When a merchant that previously cancelled is reactivated, any current cancellation will automatically become archived and it will be listed under the cancellation history list.
			<hr/><!--END SEGMENT-->
			<h3><a name='UCP'></a><strong>User Compensation Profiles (UCP):</strong></h3>
			<p>
				Users can have multiple user compensation profiles depending on their role in the organization. Possible compensation profiles include: <strong>Default UCP, Partner-Rep UCP and Manager UCP.</strong>
			</p>
			<h4><a name='UCP::CreateDefault'></a><strong>Creating Default UCP:</strong></h4>
			<p>
				A default UCP is assigned to all Users who are compensated and it is required before adding any additional compensation profiles. In general a user can have only one Default UCP but may have more than one of the others mentioned previously, depending on their Role.
				A default UCP can be created from the user profile by using one of the following buttons:
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image010.png']);
			?>
			<p>
				The second menu option above will generate a completely blank user compensation profile. This will require manually configuring all aspects of the user's compensation which could be very laborious and time consuming but could be useful when a new UCP require a unique configuration. Copying an existing compensation profile from another user with the first option however, can save time since you would have to update less data.
			</p>
			<p>
				The Copy dialog below will be displayed in order to create copies of other users UCPs. Initially, for users who don't yet have any compensation profiles, the list will only be populated with the Default UCP of the user selected in the dropdown. However, after the Default compensation profile is created, the list will be populated with all the compensation profiles owned by the user selected in the dropdown menu. <strong>In many cases the list will be filtered down to show UCPs that the receiving user doesn't already have or its role is compatible with.</strong>
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image010_1.png']);
			?>
				<strong>Below is an example of what the list would display if both the target user (the user for whom the copies are being made), and the user selected in the dropdown are both Partner-Reps.</strong> In this case the target user is a Partner-Rep who already has a Default profile, therefore the list no longer shows the <strong>Default UCP</strong> option and instead it has been populated with the selected user's Partner-Rep compensation profiles as options from which to copy. 
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image010_2.png']);
			?>
			<?php
				echo $this->Element('HelpDocs/noteWarning', ['noteText' => __('<strong>Copying UCPs is a time consuming process. The more UCPs are selected to be copied the longer the process will take. If the system determines that the process will take a long time, the system will process the request in the background (instead of in a browser session) and will send an email to the user who initiated the copy process once it finishes. This allows the user to continue working on other tasks such as creating more copies!</strong>')]);
				echo $this->Element('HelpDocs/noteInfo', ['noteText' => __('In summary, at first you can only create/copy Default UCP for new Reps. Repeating the process will then allow additional ones if the user is a Partner-Rep or a Manager')]);
			?>
			<p>
				Once a default UCP is created for any user, it will appear as a blue tab in the Compensation Profiles selector and the buttons mentioned above will permanently vanish as shown here:
			</p>
			<?php
				echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image012.png']);
			?>

			<h4><a name='UCP::CreatePartnerRep'></a><strong>Creating Partner-Rep UCP:</strong></h4>
			<p>
				To be clear, a Partner-rep is a user who has been assigned the role of Partner-Rep. In other words, a Partner-Rep is a Representative (Rep) who is or will be in some way associated with an Axia Partner. Creating a Partner-rep UCP is slightly more involved than creating a default one.<br>
				Below we will go through the steps of creating a Partner-Rep UCP for the Rep Randal as an example:
			</p>
			<ol>
				<li>
					<?php
						echo __("As mentioned before, all users must have a default UCP before any additional ones can be added.");
					?>
				</li>
				<li>
					<?php
						echo __("We must ensure that Randal has the role of “Partner Rep” by accessing that user's profile and verifying his Role:");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image014.png', 'class' => 'col-md-7']);
						echo $this->Element('HelpDocs/noteWarning', ['noteText' => __('Without the Partner-Rep role, it is not possible to continue to the next steps. Users can have multiple roles so the Partner-Rep role can be selected as an additional role. For example, a Rep can also have a role of Partner-Rep.')]);
					?>
				</li>
				<li>
					<?php
						echo __("Next, for this example, we will create Randal's Partner Rep UCP with the Partner called “Experian Health”. To do so we can either use the <strong>Create User Comp Profile</strong> button to create a copy from another Partner-Rep. Or, to create a new blank one we must exit Randal's user profile and access Experian's user profile.");
						echo $this->Element('HelpDocs/noteInfo', ['noteText' => __("Adding new blank Partner Rep UCP is done from the Partner's user profile rather than the Partner-Rep's. It is also possible to create copies FOR Partner-Reps FROM within the associated Partner.")]);
						echo __("In the image below we see Experian's default UCP in blue and three other Partner-Rep UCPs that were previously created under the partner Experian. It is important to note that Partners can only have Default compensation profiles, so the others shown in the image do not belong to Experian, they are actually Partner-Rep UCPs that belong to the users whose names appear in the tabs.");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image016.png']);
						echo __("In step 2 we setup Randal with a role of Partner Rep, this allowed his name to appear in the 'Create User Comp Profile' menu for partner reps and will allow us to add a Partner-Rep compensation profile. We have the choice to <strong>\"Create New Partner-Rep UCP\"</strong> which will create a blank one or create it by copying an existing one from another Partner-Rep's UCP.");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image018.png']);
						echo $this->Html->link('', '', ['name' => 'UCP::CreatePartnerRep::NoMore']);
						echo $this->Element('HelpDocs/noteInfo', ['noteText' => __("Once a Partner-Rep's UCP is created, his/her name will never again be displayed in the menu above under the same partner profile, but it will in profiles of partners with which hi/she has not been associated. In the unlikely event where all Partner-reps are associated to the same partner, the drop down will display as follows:") . $this->Element('HelpDocs/helpImage', ['imageFile' => 'image027.png'])]);
					?>
				</li>
				<li>
					<?php
						echo __("Pressing the Create Copy button will once again display the Copy dialog message mentioned in the \"Creating Default UCP\" section:");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image020.png']);
					?>
				</li>
				<li>
					<?php
						echo __("In this scenario we have selected another Partner-Rep who already has a UCP also associated with the partner Experian. ");
						echo __("Pressing the Submit button will create the copy of the Partner-Rep selected in the drop-down and will redirect us to Randal's user profile rather than back to the partner profile. From here, we can see now that in addition to Randal's Default UCP, the new compensation as Partner Rep under the partner Experian Health has been added:");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image024.png']);
						echo __("To clarify, the new Partner-Rep UCP belongs to Randal, although it shows the Partner Name \"Experian Health\". This helps easily differentiate among multiple Partner-Rep compensation profiles that any given Partner-Rep might have, as it is possible for a Partner-Rep to have many different forms of compensation that may vary with each different Partner. If we were to go back to the Partner Profile we will also be able to see Randal’s new Partner-Rep UCP from there along with the others that were previously there as shown in the image in step 3 above.");
					?>
				</li>
				<li>
					<?php
						echo __("Finally, the various sections within the new Partner Rep UCP must be updated to reflect the correct compensation for the Partner Rep. This can vary significantly so it cannot be covered here, except for the <strong>Permission Levels</strong> section. This section is important because this is where permissions to access the Rep’s or Partner-Rep’s merchants are given to his Manager1/Manager2/Partner/Referrer/Reseller.");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image026.png']);
						echo __("Since we created a copy of Randal’s default UCP we can see that his managers are already set with the proper permissions. All that’s left to do is assign Experian as the partner with partner Permissions and click the Add button.");
						echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image029.jpg']);
						echo $this->Element('HelpDocs/noteWarning', ['noteText' => __('As previously mentioned, when copying other UCPs the permission levels will be copied. It is very important to update copied permissions to include the correct users with whom the Rep/Partner-Rep should be associated.')]);
					?>
				</li>
			</ol>
			<hr/>
			<h3><a name='RReport'></a><strong>Residual Reporting:</strong></h3>
			<h4><strong>Residual Admin:</strong></h4>
			<?php echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image030.png', 'class' => 'img-thumbnail center-block']); ?>
			<p>
				The residual admin page displays a high level overview of data that has been generated for the Residual report and allows users to Activate and Deactivate report data. When data is inactive only Administrators can view the report for that inactive year, month and product.<br/>
				<h5><strong><u>Generating Residual Data:</u></strong></h5>
				In order to generate new residual data, a CSV file is required and it must contain the following CSV column headers: <strong>MID,DBA,Item Count,Volume</strong><br/>
				The headers are case sensitive so they must match exactly the above example.<br/>
				<?php
				echo $this->Element('HelpDocs/noteInfo', ['noteText' => __('It is possible to generate Residual Report data for the same product, month and year using separate CSV files without having to delete existing data as long as the merchants in each CSV file are different.')]);
				?>
				<h5><strong><u>Requirements to Generate Data:</u></strong></h5>
				In order for residual report data to be generated for the product selected and for the merchants present in the CSV file being uploaded, the following conditions must be met:
				<ul>
					<li>
						Before attempting to generate residual data for a given product/month/year and for the merchants in the CSV file, merchant pricing data must have been previously archived for the same set of merchants, products and for the same month/year.
					</li>
					<li>
						All merchants listed in the CSV file must have been approved by underwriting and have the status of "Approved" in the undewriting section except when generating residuals for the "Gateway 1" product. Gateway 1 residuals may be generated even for non-approved merchants.
					</li>
					<li>
						Payment Fusion and Gateway 1 products have a product ID that is unique to each and every client that has those products. When generating residuals for the Gateway 1 and Payment Fusions products, the residual admin process will look for the Gateway 1/Payment Fusion product ID and match that against the values under MID column in the CSV file. Threfore, be sure to use the product ID for each merchant <strong>instead of the merchants' ID (MID)</strong> under the MID column of the CSV file when processing these two products. For all other products the normal merchant ID (MID) must be used.
					</li>
					<li>
						Residual data will always be generated for products marked as <strong>Do Not Display</strong> under the User Parameters section of the reps compensation profile but it will not be displayed on the reports for non admin users.
						<?php echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image031.png', 'class' => 'img-thumbnail center-block']); ?>
					</li>
				</ul> 
			</p>
			<hr/>
			<h3><a name='Security'></a><strong>Security:</strong></h3>
			<h4><a name='Security::rbac'></a><strong>Role-based Access Control (RBAC):</strong></h4>
			<p>
				Access to the various resources in the database web site is controlled with the use of roles which are assigned to every user when new user profiles are created and can be changed at any time.

				<br/>Roles can inherit permissions in an inverted hierarchy fashion, where the roles with the least amount of access are at the top of the hierarchy and the roles with the highest access are at the bottom, so roles have increasingly more access going from top to bottom. When a role at the top of the hierarchy is given permission to a specific asset, that permission is automatically inherited down to the roles with higher permissions levels below it. This does not work the other way around however, which is helpful when, for example, full access needs to be given to super admins but not to lower-access-level admins. The image below shows the role hierarchy, and as you can see lower-access-level roles start at the top and access-level increases as you go down the hierarchy.
				<?php echo $this->Element('HelpDocs/helpImage', ['imageFile' => 'image032.png', 'class' => 'img-thumbnail center-block']); ?>
				With this hierarchy we could give Admin III permissions we don’t want Admin IV to have because such permissions will cascade down and be inherited to Admin II and Admin I. In most cases we want higher-access-level Roles to inherit the level of access that lower-access-level ones have.
				<br/>Roles and their permissions can be configured from the RBAC Admin page accessible from the top site navigation under the Admin menu.

			</p>
			<h4><a name='Security::ownControl'></a><strong>Asset Owner Access Control:</strong></h4>
			<p>
				In addition to RBAC, the system restricts access to records that a given user has no ownership of, is not related to and/or is not responsible for. For example merchant accounts are associated to specific Sales Reps, Managers, secondary managers, partners, and sometimes Referrers and Resellers. Any user not associated to a merchant will be denied access (except users with administrator Roles)
			</p>





















		</div>
		
	</div>
</div>