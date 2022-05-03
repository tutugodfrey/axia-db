<?php
class GenerateGatewayAccountsMassMigration extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'generate_gateway_accounts_mass_migration';

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
            $Client = $this->generateModel('Client');
            $MerchantType = $this->generateModel('MerchantType');
            $UwStatusMerchantXref = ClassRegistry::init('UwStatusMerchantXref');
            $UwStatus = $this->generateModel('UwStatus');
            $User = ClassRegistry::init('User');
        if ($direction === 'up') {
            
            //load metadata
            $merchantTypesList = $MerchantType->find('list', ['fields' => ['MerchantType.type_description', 'MerchantType.id']]);
            $clientList = $Client->find('list', ['fields' => ['client_id_global', 'id']]);
            $apprStatus = $UwStatus->field('id', ['name' => 'Approved']);
            $recStatus = $UwStatus->field('id', ['name' => 'Received']);

            $mUpdates = [];
            $dir = APP . 'Config' . DS . 'Migration' . DS . 'datafiles' . DS;
            $folder = new Folder($dir);

            if (is_null($folder->path)) {
                throw new Exception("Directory $dir was not found!");
            }
            $row = 1;
            echo ">> Adding AxiaMed merchant Gateway product accounts ...\n";
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
                    $midType = trim($data[1]);
                    $dba = trim($data[2]);
                    $clientIdNum = substr(trim($data[3]), 0, 8); 
                    $partnerName = trim($data[4]);
                    $subDate = trim($data[5]);
                    $apprDate = trim($data[6]);
                    $relatedMID = trim($data[7]);
                    $repName = trim($data[8]);
                    $merchantData = [];
                    if ($Merchant->hasAny(['merchant_mid' => $mid])) {
                        echo "Merchant with MID $mid already exists!! Skipping $mid.\n";
                        continue;
                    }

                    //load foreing keys /data
                    $clientUuid = Hash::get($clientList, $clientIdNum);
                    $partnerUser = $User->find('first', array(
                        'fields' => array('distinct(User.id) AS "User__id"', 'User.fullname'),
                        'conditions' => array(
                            'OR' => array(
                               'trim(BOTH FROM "User"."user_first_name" || \' \' || "User"."user_last_name") = ' . "'$partnerName'",
                                'trim(BOTH FROM "User"."user_first_name" || "User"."user_last_name") = ' . "'$partnerName'"
                            ),
                        ),
                        'joins' => array(
                            array('table' => 'users_roles',
                                'alias' => 'UsersRole',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'User.id = UsersRole.user_id',
                                )
                            ),
                            array('table' => 'roles',
                                'alias' => 'Role',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'UsersRole.role_id = Role.id',
                                    "Role.name = '" .User::ROLE_PARTNER. "'"
                                )
                            ),
                        )
                    ));
                    $repUser = $User->getByNameAndRole($repName, [User::ROLE_REP, User::ROLE_PARTNER_REP]);

                    $errors .= (empty($clientUuid))? "Client not found for MID $mid | ":'';
                    $errors .= (empty(Hash::get($merchantTypesList, $midType)))? "Merchant Type not found for MID $mid | ":'';
                    
                    $newMerchUUID = CakeText::uuid();
                    $merchantData['Merchant'] = [
                        'id' => $newMerchUUID,
                        'merchant_mid' => $mid,
                        'merchant_type_id' => Hash::get($merchantTypesList, $midType),
                        'merchant_dba' => $dba,
                        'client_id' => $clientUuid,
                        'partner_id' => Hash::get($partnerUser,'User.id'),
                        'related_acquiring_mid' => $relatedMID,
                        'user_id' => $repUser['User']['id'],
                        'active' => 1,
                    ];
                    
                    $Merchant->clear();
                    $Merchant->create();
                    $saved = $Merchant->save($merchantData['Merchant'], ['validate' => false]);
                    if ($saved == false) {
                         $errors .= "Could not save merchant with MID $mid! - \n";
                         $errors .= print_r($Merchant->validationErrors, true) . "\n";
                         throw new Exception($errors);
                    }

                    $uwStatusMerchRecord = array(
                        'id' => CakeText::uuid(),
                        'merchant_id' => $merchantData['Merchant']['id'],
                        'uw_status_id' => $apprStatus,
                        'datetime' => "$apprDate 00:00:00",
                        'notes' => 'Approval date was set programmatically with a migration.',
                    );
                    
                    $UwStatusMerchantXref->clear();
                    $UwStatusMerchantXref->create();
                    $saved = $UwStatusMerchantXref->save($uwStatusMerchRecord, ['validate' => false]);
                    if ($saved == false) {
                         throw new Exception("Could not save approval date for MID $mid!");
                    }

                    $uwStatusMerchRecord = array(
                        'id' => CakeText::uuid(),
                        'merchant_id' => $merchantData['Merchant']['id'],
                        'uw_status_id' => $recStatus,
                        'datetime' => "$subDate 00:00:00",
                        'notes' => 'Received date was set programmatically with a migration.',
                    );
                    
                    $UwStatusMerchantXref->clear();
                    $UwStatusMerchantXref->create();
                    $saved = $UwStatusMerchantXref->save($uwStatusMerchRecord, ['validate' => false]);

                    if ($saved == false) {
                         throw new Exception("Could not save received date for MID $mid!");
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
