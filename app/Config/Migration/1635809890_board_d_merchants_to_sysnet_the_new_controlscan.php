<?php
App::uses('ControlScan', 'Model');
class BoardDMerchantsToSysnetTheNewControlscan extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'board_d_merchants_to_sysnet_the_new_controlscan';
    public $merchantIds = [
                            '96161700-a52e-4180-ae43-0814bf9cb5ae',
                            '61521584-84e8-4c5d-991e-0fea0a01013e',
                            '7c40bbc8-9fb6-40c1-b376-165ab982f412',
                            '61521625-09f0-4094-bd59-05a80a01013e',
                            '6152168a-1938-4279-b9b1-05ab0a01013e',
                            '615c99c3-da58-4437-87f0-0fee0a01013e',
                            '615cc48e-cfe8-4bf7-b745-28710a01013e',
                            '763d641e-be88-489b-b0bf-ba48b046724d',
                            '5fb38715-957d-44a5-8660-35d552b47144',
                            '6152174d-d808-4ed8-9b4f-27fb0a01013e',
                            '615cc41b-70e4-44bd-a2a1-38300a01013e',
                            '61521792-e29c-4eec-a020-2e0d0a01013e',
                            '61438cfb-4770-4356-be69-38300a01013e',
                            '1351babb-3251-4b79-aa06-ef9b4bbaba4c',
                            '26dea267-a5cb-40dd-a74f-d7bd1733ebed',
                            'e346319f-26ea-468e-9799-8f1ad657ce7f',
                            '16d39a13-9e0c-4d72-a6e3-583f3b45c298',
                            '389d8a7a-8314-4f7e-9f42-e2269c5bae2d',
                            '04a8bf80-7d16-4813-b998-5a57d45562f5',
                            'fd473af2-8420-4c58-af20-1552cc666769',
                            'ace7ab5e-db91-4df4-83b0-75feeea137fc',
                            'fc0995fe-95d9-4b94-b240-301162eba200',
                            '615e34dc-1990-4c65-8257-0fe80a01013e',
                            'b0534959-64ba-4218-94a7-812e094871ea',
                            '99ff8ea6-57f1-43f8-b6ef-feff84b18862',
                            'dc05bda0-bfa2-4b34-9c1a-8f398ab29cfb',
                            'cf411f99-5444-4527-81bd-6a875b17073c',
                            '965dd73c-337c-4aca-868b-4b98236fb7c5',
                            '1088b9ca-75ab-4849-a11b-386fd8924b16',
                            '6dcd8dc6-f990-4719-af0c-550038da05b4',
                            'b0be5a73-9cfd-4c3b-b15b-8f0e60d0dd3f',
                            'bf99fbfb-b542-408e-8a35-965feffd72fe',
                            '952b3d1a-14c2-4fdc-82bb-1309e4ddfa7d',
                            'e586504b-a01e-4dd2-be20-7cc1f47a4ed5',
                            'c0ec1b8e-1862-4424-9d19-f4993c7ba3f8',
                            '40dca1f8-55f3-4e2e-9be3-f61d8712d8ae',
                            '0b68703a-7187-462d-abdb-77cd7ccf54be',
                            'febb1288-b82b-4ad8-8e02-3fd6bda49119',
                            'b3725021-8592-4744-9330-c359ee30b082',
                            'e0804c1f-32dc-4619-8364-9ed86dff0f87',
                            '056cbcd4-9702-4851-a58a-57630d6806ef',
                            '75d93d67-abe9-4b9a-9392-38d2a13ea02d',
                            'a9b3abc6-42ea-4750-8500-db1c40120543',
                            'c6ab9454-cfa1-4905-8eb1-c1fd44215115',
                            'a864d472-23b3-48dd-b6f4-d5b73db9b738',
                            'eeb99209-2761-4b01-afe1-7503f2302f9f',
                            '29855da0-72ce-44c0-82d8-063ad3ca0ee6',
                            'e61f4aa6-0524-41d8-b66f-f3ce3b6c8ca9',
                            'f4aa9790-2609-455c-a16b-686f30315103',
                            'c5c9bd1e-fa98-41ea-b0e5-9544f6f0f3d2',
                            'efc3fec2-4490-4f0d-9b98-72280c1bb698',
                            'f52eeab2-cc94-46b5-91d3-251c80119142',
                            '7413c8c4-9f1c-4aa8-95ba-e43452808e42',
                            'df54430f-f6e2-453c-b4b9-b25b3d8860f4',
                            '3bda56eb-3374-4cd6-9427-cb18088fa79e',
                            '37ce4c49-af40-4de7-855b-faa1c9cef6c6',
                            '44cb506b-8bd5-4b40-aabf-5037173f1a1f',
                            'e198fe29-e989-41bc-90ed-2afe234717dc',
                            '7ebde234-94fb-43ab-87da-693d0d6943c3',
                            '1f47dc6d-baff-4a5b-a029-d4d3bae90133',
                            '3a4297bd-474a-4fd6-96e5-8c804ae1945a',
                            '12efdf2b-b1df-4982-b3b9-61bab67b3c39'];
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
        $MerchantPci = $this->generateModel('MerchantPci');
        if ($direction === 'up') {
            $boardCount = 0;
            $alreadyBoarded = 0;
            foreach ($this->merchantIds as $id) {
                if ($MerchantPci->hasAny(['merchant_id' => $id, 'controlscan_boarded' => true])) {
                   $alreadyBoarded+=1;
                } else {
                    //wait between api calls to prevent rate limit errors
                    sleep(2);
                    $ControlScan = ClassRegistry::init('ControlScan');
                    $ControlScan->boardMerchant($id);
                    $boardCount += 1;
                    unset($ControlScan);
                    ClassRegistry::flush();
                }
            }
            echo "Out of a total of " . count($this->merchantIds) ." merchants, $boardCount merchants were boarded by this migration and $alreadyBoarded were skpped because they were already boarded.\n\n";
        }
		return true;
	}
}
