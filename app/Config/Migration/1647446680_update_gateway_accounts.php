<?php
class UpdateGatewayAccounts extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_gateway_accounts';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
		),
		'down' => array(
		),
	);

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
    public function after($direction) {
        //This migration creates gateway accounts for all merchant's specified in the CSV file
        $Merchant = ClassRegistry::init('Merchant');            
        $MerchantCancellation = ClassRegistry::init('MerchantCancellation');            
        $Address = ClassRegistry::init('Address');
        $MerchantUw = ClassRegistry::init('MerchantUw');
        $TimelineEntry = ClassRegistry::init('TimelineEntry');
        $TimelineItem = ClassRegistry::init('TimelineItem');
        $AddressType = ClassRegistry::init('AddressType');

        if ($direction === 'up') {
            
            $dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
            $folder = new Folder($dir);

            if (is_null($folder->path)) {
                throw new Exception("Directory $dir was not found!");
            }
            $row = 1;
            echo ">> Updating AxiaMed merchant Gateway product accounts ...\n";
            if (($handle = fopen($dir . "DBGatewayProductMigration.csv", "r")) !== false) {
                while (($data = fgetcsv($handle, 10000, ",")) !== false) {

                    //Indexes
                    //[0] = MID
                    //[1] = MID Type
                    //[2] = DBA
                    //[3] = Client ID
                    //[4] = Parter
                    //[5] = Submitted Date
                    //[6] = Approval Date
                    //[7] = Related Acquiring MID
                    //[8] = Rep

                    //We don't care about headers
                    if ($row === 1 && (strtolower($data[0]) === 'mid')) {
                        $row ++;
                        continue;
                    }
                    $errors = '';
                    $mid = trim($data[0]);
                    $relatedMID = trim($data[7]);                    
                    $merchantData = [];
                    $parentMerchAccount = [];


                    //load gateway merchant from csv data
                    $gatewayMerchant = $Merchant->find('first', [
                        'conditions' => ['merchant_mid' => $mid],
                    ]);
                    if (empty($gatewayMerchant['Merchant']['id'])) {
                        echo " ************ Gateway Merchant with MID $mid not found and was skipped \n\n";
                        continue;
                    }
                    //load related merchant data
                    if (!empty($relatedMID)) {
                        $parentMerchAccount = $Merchant->find('first', [
                            'conditions' => ['merchant_mid' => $relatedMID],
                            'contain' => ['MerchantCancellation']
                        ]);
                    }

                    $merchantData['Merchant'] = $gatewayMerchant['Merchant'];
                    $merchantData['Merchant']['active'] = 1;

                    if (!empty($parentMerchAccount['Merchant']['id']) && (!empty($parentMerchAccount['MerchantCancellation']['id']) || $parentMerchAccount['Merchant']['active'] == 0)) {
                        $merchantData['Merchant']['active'] = 0;
                        if (!$MerchantCancellation->hasAny(['merchant_id' => $merchantData['Merchant']['id']]) && !empty($parentMerchAccount['MerchantCancellation']['id'])) {
                            $merchantData['MerchantCancellation'] = $parentMerchAccount['MerchantCancellation'];
                            $merchantData['MerchantCancellation']['id'] = CakeText::uuid();
                            $merchantData['MerchantCancellation']['merchant_id'] = $merchantData['Merchant']['id'];
                        }
                    }
                    if (!$TimelineEntry->hasAny(['merchant_id' => $merchantData['Merchant']['id'], 'timeline_item_id' => TimelineItem::GO_LIVE_DATE])) {
                        $merchantData['TimelineEntry'][] = array(
                            'id' => CakeText::uuid(),
                            'merchant_id' => $merchantData['Merchant']['id'],
                            'timeline_item_id' => TimelineItem::GO_LIVE_DATE,
                        );
                    }
                    if (!$MerchantUw->hasAny(['merchant_id' => $merchantData['Merchant']['id']])) {
                        $merchantData['MerchantUw'] = [
                            'id' => CakeText::uuid(),
                            'merchant_id' => $merchantData['Merchant']['id'],
                            'expedited' => false
                        ];

                    }
                    if (!$Address->hasAny(['merchant_id' => $merchantData['Merchant']['id'], 'address_type_id' => AddressType::BUSINESS_ADDRESS])) {
                        $merchantData['Address'][] = [
                                'id' => CakeText::uuid(),
                                'merchant_id' => $merchantData['Merchant']['id'],
                                'address_type_id' => AddressType::BUSINESS_ADDRESS,
                        ];
                        $merchantData['Address'][] = [
                                'id' => CakeText::uuid(),
                                'merchant_id' => $merchantData['Merchant']['id'],
                                'address_type_id' => AddressType::CORP_ADDRESS,
                        ];
                        $merchantData['Address'][] = [
                                'id' => CakeText::uuid(),
                                'merchant_id' => $merchantData['Merchant']['id'],
                                'address_type_id' => AddressType::MAIL_ADDRESS,
                        ];
                        $merchantData['Address'][] = [
                                'id' => CakeText::uuid(),
                                'merchant_id' => $merchantData['Merchant']['id'],
                                'address_type_id' => AddressType::BANK_ADDRESS,
                        ];
                    }

                    $result = $Merchant->saveAll($merchantData, ['validate' => false, 'deep' => true]);

                    if ($result == false) {
                         $errors .= "IMPORTANT !!! - !!! Could not save merchant MID $mid !!! - !!!";
                    }
                    $errors .= (!empty($errors))? "\n------------------------------------\n":'';
                    echo $errors;
                }
                fclose($handle);
            } else {
                throw new Exception("fopen failed to open stream: No such file or directory!");
            }
        }
        return true;
    }
}
