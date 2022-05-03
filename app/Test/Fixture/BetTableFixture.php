<?php
/**
 * BetTableFixture
 *
 */
class BetTableFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'length' => 36, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'length' => 64),
		'bet_extra_pct' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('unique' => true, 'column' => 'id'),
			'product_name_index' => array('unique' => true, 'column' => 'name')
		),
		'tableParameters' => array()
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '521df34a-0a90-4035-b90e-461534627ad4',
			'name' => '5612/7612tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-bf80-4ff2-8628-478c34627ad4',
			'name' => '5035/7035',
			'bet_extra_pct' => '0.3500'
		),
		array(
			'id' => '521df34a-2338-435a-b917-46ce34627ad4',
			'name' => '6147/8147tsysR',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34a-66bc-4de3-83aa-4ec634627ad4',
			'name' => '5050/7050tsys',
			'bet_extra_pct' => '0.5000'
		),
		array(
			'id' => '521df34a-15b4-421b-baa3-4fee34627ad4',
			'name' => '5610/7610tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-88e8-4ed1-bea4-48a534627ad4',
			'name' => '5179/7179',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-5074-4d8d-a86d-4a4c34627ad4',
			'name' => '5044/7044tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-96f4-4973-a72a-4c7434627ad4',
			'name' => '5025/7025',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34a-bb78-46af-91ff-437834627ad4',
			'name' => '5055/7055',
			'bet_extra_pct' => '0.5500'
		),
		array(
			'id' => '521df34a-0cf0-4f75-a628-419934627ad4',
			'name' => '5183/7183',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-349c-4f7f-a6ed-415c34627ad4',
			'name' => '5075/7075',
			'bet_extra_pct' => '0.7500'
		),
		array(
			'id' => '521df34a-b5e0-49b3-9749-4dd834627ad4',
			'name' => '6144/8144tsysN',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34a-4b74-4f73-8334-47a134627ad4',
			'name' => '5184/7184',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-35a8-408e-b489-40de34627ad4',
			'name' => '5042/7042tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-0358-4bb3-804d-401934627ad4',
			'name' => '5144/7144tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-6b94-4aae-8dfa-48b434627ad4',
			'name' => '5015/7015',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34a-51a0-4f16-ad5d-468e34627ad4',
			'name' => '5613/7613',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-f79c-4f0b-9caa-459934627ad4',
			'name' => '5610/7610',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-84ec-4037-8e68-428434627ad4',
			'name' => '6146/8146tsysN',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34a-43fc-4adf-be62-445534627ad4',
			'name' => '5611/7611tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-8164-4837-95d6-413934627ad4',
			'name' => '5202/7202',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-3a58-4606-8a53-426634627ad4',
			'name' => '5146/7146tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-7b04-4c82-8153-4b3534627ad4',
			'name' => '5145/7145tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-ac74-46d6-b1db-489534627ad4',
			'name' => '5080/7080tsys',
			'bet_extra_pct' => '0.8000'
		),
		array(
			'id' => '521df34a-d678-4b4d-ab58-485934627ad4',
			'name' => '6146/8146Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34a-312c-4fda-bd3c-446234627ad4',
			'name' => '5146/7146',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-4168-43b9-8516-434134627ad4',
			'name' => '5171/7171tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-8ef8-4e52-aa44-410934627ad4',
			'name' => '5141/7141',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-8e04-43f2-be91-4fb634627ad4',
			'name' => '5174/7174tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34a-94e0-4aa1-b6ac-4c6634627ad4',
			'name' => '5140/7140',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-3d98-464e-a542-479f34627ad4',
			'name' => '5020/7020tsys',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34b-d908-4ddf-bb66-445434627ad4',
			'name' => '5175/7175',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-0a70-4e4b-9660-467234627ad4',
			'name' => '5144/7144',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-2344-4624-ab8e-4d9a34627ad4',
			'name' => '6144/8144Adj',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34b-cee8-42c2-a251-4a7734627ad4',
			'name' => '5178/7178',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-6c58-48c1-a318-495734627ad4',
			'name' => '5145/7145',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-16bc-4f68-add2-4ce734627ad4',
			'name' => '5143/7143tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-720c-4710-b44f-47cd34627ad4',
			'name' => '5060/7060tsys',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34b-65fc-4204-adcd-4de934627ad4',
			'name' => '5015/7015tsys',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34b-13ac-4793-a56b-495e34627ad4',
			'name' => '5171/7171',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-5abc-45cc-b741-426a34627ad4',
			'name' => '5035/7035tsys',
			'bet_extra_pct' => '0.3500'
		),
		array(
			'id' => '521df34b-1798-49d2-85dc-452934627ad4',
			'name' => '6148/8148tsysN',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34b-1790-48a3-9768-47fc34627ad4',
			'name' => '6603/8603',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-5850-4d3b-aef1-489b34627ad4',
			'name' => '5025/7025tsys',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34b-64d4-4adc-b262-455e34627ad4',
			'name' => '6146/8146tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-2728-441e-9f87-41d834627ad4',
			'name' => '5177/7177',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-a240-423e-bf0d-4fbb34627ad4',
			'name' => '6149/8149tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-d2a8-465a-a899-451b34627ad4',
			'name' => '6147/8147Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34b-9d9c-457f-a846-4c3334627ad4',
			'name' => '5609/7609',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34b-859c-4e4c-9f81-40eb34627ad4',
			'name' => '5613/7613tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-8570-4e96-89fd-452634627ad4',
			'name' => '5182/7182tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-625c-4f91-a365-4a3034627ad4',
			'name' => '5010/7010tsys',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34b-14d8-4808-85ed-4ae234627ad4',
			'name' => '5045/7045',
			'bet_extra_pct' => '0.4500'
		),
		array(
			'id' => '521df34b-46ac-4b50-b42e-402034627ad4',
			'name' => '5055/7055tsys',
			'bet_extra_pct' => '0.5500'
		),
		array(
			'id' => '521df34b-3744-4075-8592-485434627ad4',
			'name' => '5612/7612',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-ca40-44a1-b97f-4f6b34627ad4',
			'name' => '5200/7200',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-1e10-47d3-bc4e-48b734627ad4',
			'name' => '5142/7142tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-4a08-4946-b410-426e34627ad4',
			'name' => '6145/8145tsysN',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34b-9648-46bf-8cc4-4e4c34627ad4',
			'name' => '6000/8000',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-92d8-4405-8afb-4da634627ad4',
			'name' => '5020/7020',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34b-8414-4747-9a7c-41cb34627ad4',
			'name' => '5181/7181',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34b-0c3c-44e4-88e5-482434627ad4',
			'name' => '6149/8149Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34b-a148-40d7-bc22-445d34627ad4',
			'name' => '6148/8148Adj',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34b-ae00-47f5-8f25-4b4334627ad4',
			'name' => '6147/8147Adj',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34b-238c-4f46-bc5b-4cf434627ad4',
			'name' => '5075/7075tsys',
			'bet_extra_pct' => '0.7500'
		),
		array(
			'id' => '521df34b-cd30-4ced-a7e7-428234627ad4',
			'name' => '5176/7176',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-1c74-4c25-8f2d-4cac34627ad4',
			'name' => '6145/8145tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-1f44-48a8-8296-49d634627ad4',
			'name' => '6601/8601tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-1274-4e4c-a355-47d434627ad4',
			'name' => '6149/8149Adj',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34b-b328-4e29-a270-422a34627ad4',
			'name' => '6147/8147tsysN',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34b-5684-46f7-961e-4b2834627ad4',
			'name' => '5040/7040',
			'bet_extra_pct' => '0.4000'
		),
		array(
			'id' => '521df34b-d714-42d0-8093-440034627ad4',
			'name' => '5180/7180',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-9884-4f79-88f9-4b0d34627ad4',
			'name' => '5065/7065tsys',
			'bet_extra_pct' => '0.6500'
		),
		array(
			'id' => '521df34b-c888-4584-96e7-438534627ad4',
			'name' => '5063/7063tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34b-b024-4a09-8911-414034627ad4',
			'name' => '5102/7102',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34b-ee38-4b9a-8e93-40a534627ad4',
			'name' => '6149/8149tsysN',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34b-7084-4136-a591-41fc34627ad4',
			'name' => '5060/7060',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34b-562c-419d-bd92-444834627ad4',
			'name' => '5050/7050',
			'bet_extra_pct' => '0.5000'
		),
		array(
			'id' => '521df34b-8a58-407d-94cd-401134627ad4',
			'name' => '6144/8144N',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34b-4cd8-4a2f-b76b-46aa34627ad4',
			'name' => '6601/8601',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-f7e8-42f3-b90f-48f434627ad4',
			'name' => '5005/7005tsys',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34c-01e4-4dfd-8155-425134627ad4',
			'name' => '5070/7070tsys',
			'bet_extra_pct' => '0.7000'
		),
		array(
			'id' => '521df34c-c6bc-4b64-af10-41ab34627ad4',
			'name' => '6148/8148tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-3c48-465b-8544-48bb34627ad4',
			'name' => '5124/7124',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-df64-491d-8e20-478334627ad4',
			'name' => '5143R/7143R',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-dc10-4f95-8362-4efc34627ad4',
			'name' => '6148/8148N',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34c-0278-4fb2-ab1f-439c34627ad4',
			'name' => '5030/7030',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34c-e76c-4ce4-a5af-4cc234627ad4',
			'name' => '5142/7142',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-8664-4fd3-b03c-480a34627ad4',
			'name' => '5040/7040tsys',
			'bet_extra_pct' => '0.4000'
		),
		array(
			'id' => '521df34c-090c-4d3c-8f6d-408d34627ad4',
			'name' => '5150/7150',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-87b0-434f-a16d-4c3934627ad4',
			'name' => '5143/7143tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-7e5c-4a25-b95c-4de934627ad4',
			'name' => '5085/7085tsys',
			'bet_extra_pct' => '0.8500'
		),
		array(
			'id' => '521df34c-3d34-4201-aa9a-4bb234627ad4',
			'name' => '6602/8602',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-da2c-4108-9d35-49c934627ad4',
			'name' => '5170/7170',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-e9ec-4a77-b9fe-4cb934627ad4',
			'name' => '6145/8145N',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34c-2b7c-437b-8542-474734627ad4',
			'name' => '6143/8143Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-7500-40c0-b835-4d2b34627ad4',
			'name' => '6147/8147N',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34c-cd78-4c96-8ba1-43a634627ad4',
			'name' => '5141/7141tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-4db4-46bd-aead-4d5a34627ad4',
			'name' => '6144/8144tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-9c70-47d5-b766-49a034627ad4',
			'name' => '5030/7030tsys',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34c-1470-4634-badc-45c234627ad4',
			'name' => '5070/7070',
			'bet_extra_pct' => '0.7000'
		),
		array(
			'id' => '521df34c-9128-4e9f-a2d1-46cc34627ad4',
			'name' => '6148/8148Res',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34c-4ef8-40ce-bc85-42f934627ad4',
			'name' => '6143/8143tsysN',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34c-a990-44f6-994b-4d0c34627ad4',
			'name' => '5605/7605tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-d330-4122-aa9d-44ba34627ad4',
			'name' => '6144/8144Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-4f60-4e3a-a313-4e4834627ad4',
			'name' => '5005/7005',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34c-6f60-400b-ab86-4f8534627ad4',
			'name' => '5182/7182',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-b350-4d1f-b77c-42fc34627ad4',
			'name' => '5140/7140tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-49c0-4283-95d5-4bf934627ad4',
			'name' => '6143/8143N',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34c-1f40-4149-9fe6-4d1d34627ad4',
			'name' => '5010/7010',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34c-9a20-4f34-a292-416534627ad4',
			'name' => '5143/7143PP',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-7f40-46ea-a8a7-4e8034627ad4',
			'name' => '5090/7090tsys',
			'bet_extra_pct' => '0.9000'
		),
		array(
			'id' => '521df34c-0278-4dee-86b5-428234627ad4',
			'name' => '6603/8603tsys',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-8b44-4b1e-93f0-4aba34627ad4',
			'name' => '5607/7607',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-21a0-409c-8cc9-423334627ad4',
			'name' => '5065/7065',
			'bet_extra_pct' => '0.6500'
		),
		array(
			'id' => '521df34c-5d38-49e3-b99b-41d234627ad4',
			'name' => '6143/8143Adj',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34c-03fc-49ca-9735-459334627ad4',
			'name' => '6146/8146Adj',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34c-20b8-436e-bf2c-4fab34627ad4',
			'name' => '5605/7605',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-a540-49b0-8f22-4b6134627ad4',
			'name' => '5173/7173',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-404c-46ca-b9ea-4be634627ad4',
			'name' => '5611/7611',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-3a50-434b-9dbe-462c34627ad4',
			'name' => '5085/7085',
			'bet_extra_pct' => '0.8500'
		),
		array(
			'id' => '521df34c-25a8-4c2a-abd1-452f34627ad4',
			'name' => '6146/8146N',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34c-84a0-4b66-a9d7-43a734627ad4',
			'name' => '6143/8143tsysR',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-8ec0-45d5-8bc8-42ac34627ad4',
			'name' => '6145/8145Res',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-8598-4ba2-90b0-45d834627ad4',
			'name' => '5143/7143',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-6244-447c-bfa0-494f34627ad4',
			'name' => '5174/7174',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34c-1a38-45aa-a4a6-4a4434627ad4',
			'name' => '5045/7045tsys',
			'bet_extra_pct' => '0.4500'
		),
		array(
			'id' => '521df34c-deac-4a8f-9dc3-41a534627ad4',
			'name' => '5149/7149',
			'bet_extra_pct' => null
		),
		array(
			'id' => '521df34c-c0ac-49e2-8aca-4f0034627ad4',
			'name' => '6145/8145Adj',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34c-dd28-4da8-a38b-4e9934627ad4',
			'name' => '6149/8149N',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34c-8db0-4223-b6d2-47e634627ad4',
			'name' => '5172/7172',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-5f90-4899-a941-4c6634627ad4',
			'name' => '5080/7080',
			'bet_extra_pct' => '0.8000'
		),
		array(
			'id' => '521df34d-8994-46c0-881c-485434627ad4',
			'name' => '5090/7090',
			'bet_extra_pct' => '0.9000'
		),
		array(
			'id' => '521df34d-ce74-435f-b10c-4fe234627ad4',
			'name' => '6200',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-c74c-4458-b6f4-48b434627ad4',
			'name' => '6742N',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34d-bf88-4dd8-a551-46f534627ad4',
			'name' => '6025',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34d-4b88-41a5-941b-49be34627ad4',
			'name' => '6144',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-d850-4a9e-bd27-4beb34627ad4',
			'name' => '6343',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-7d98-4ff1-9141-468b34627ad4',
			'name' => '6746N',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '521df34d-f574-4af3-9aee-462534627ad4',
			'name' => '6743N',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34d-d1bc-4f6b-98ca-402a34627ad4',
			'name' => '6065',
			'bet_extra_pct' => '0.6500'
		),
		array(
			'id' => '521df34d-3b6c-47cc-9776-480634627ad4',
			'name' => '6035',
			'bet_extra_pct' => '0.3500'
		),
		array(
			'id' => '521df34d-00d0-4956-92ef-458d34627ad4',
			'name' => '6745N',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34d-a128-4ed0-8c01-401b34627ad4',
			'name' => '6075',
			'bet_extra_pct' => '0.7500'
		),
		array(
			'id' => '521df34d-0838-42a6-b8dd-40f534627ad4',
			'name' => '6744N',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34d-0118-4623-a36f-4f8c34627ad4',
			'name' => '6030',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34d-7b94-4f6b-8483-4de034627ad4',
			'name' => '6160',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-dd74-479e-a577-4d0134627ad4',
			'name' => '6747N',
			'bet_extra_pct' => '0.3000'
		),
		array(
			'id' => '521df34d-80f4-47d4-ac42-476634627ad4',
			'name' => '6142',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-f4f0-443c-9740-4c5c34627ad4',
			'name' => '6020',
			'bet_extra_pct' => '0.2000'
		),
		array(
			'id' => '521df34d-3470-4dcb-a8e8-4f5e34627ad4',
			'name' => '6775',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-8b84-4e62-8e86-4f1434627ad4',
			'name' => '6141',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-a1c0-4add-8b46-4d5f34627ad4',
			'name' => '6345',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-32e4-4f01-ada7-45b134627ad4',
			'name' => '6045',
			'bet_extra_pct' => '0.4500'
		),
		array(
			'id' => '521df34d-3c58-4f4b-b202-4b6434627ad4',
			'name' => '6015',
			'bet_extra_pct' => '0.1500'
		),
		array(
			'id' => '521df34d-e8f8-4583-9f67-4adf34627ad4',
			'name' => '6090',
			'bet_extra_pct' => '0.9000'
		),
		array(
			'id' => '521df34d-71ac-4a2d-925f-4a1a34627ad4',
			'name' => '6753N',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34d-5ca0-4b28-9568-4f1f34627ad4',
			'name' => '6150',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-9130-40c2-a5e9-42d634627ad4',
			'name' => '6146',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-0a80-40fa-ab7a-43bd34627ad4',
			'name' => '6040',
			'bet_extra_pct' => '0.4000'
		),
		array(
			'id' => '521df34d-2804-4f1c-9f5f-492334627ad4',
			'name' => '6080',
			'bet_extra_pct' => '0.8000'
		),
		array(
			'id' => '521df34d-e4c4-48d5-89b7-47b634627ad4',
			'name' => '6140',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-be50-4de7-8d7e-425f34627ad4',
			'name' => '6005',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '521df34d-6190-4dc5-bda3-4d4e34627ad4',
			'name' => '6070',
			'bet_extra_pct' => '0.7000'
		),
		array(
			'id' => '521df34d-b778-4cc0-9ea7-484834627ad4',
			'name' => '6202',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-4518-4833-bc78-438e34627ad4',
			'name' => '6161',
			'bet_extra_pct' => '0.0000'
		),
		array(
			'id' => '521df34d-53e8-416f-aaff-481734627ad4',
			'name' => '6055',
			'bet_extra_pct' => '0.5500'
		),
		array(
			'id' => '521df34d-b438-4531-a64d-4d6a34627ad4',
			'name' => '6010',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '521df34d-9cd4-4c34-b1a3-483834627ad4',
			'name' => '6060',
			'bet_extra_pct' => '0.6000'
		),
		array(
			'id' => '521df34d-854c-4465-9c48-428c34627ad4',
			'name' => '6050',
			'bet_extra_pct' => '0.5000'
		),
		array(
			'id' => '521df34a-61ec-4136-81c3-418634627ad4',
			'name' => '6143CPtsys',
			'bet_extra_pct' => '0.1000'
		),
		array(
			'id' => '558ade3c-d180-423c-a50d-20ba34627ad4',
			'name' => '7005',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '558ade3b-0a28-4c31-82a1-20ba34627ad4',
			'name' => '5005',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '558ade3b-9990-4f05-bfed-20ba34627ad4',
			'name' => '3005',
			'bet_extra_pct' => '0.0500'
		),
		array(
			'id' => '558ade3c-2d94-45ea-8ba1-20ba34627ad4',
			'name' => '9350',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '558ade3c-3b38-492f-adb4-20ba34627ad4',
			'name' => '1000',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '558ade3b-4bb8-45c3-86c7-20ba34627ad4',
			'name' => '5025',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '558ade3b-4648-4c9e-8b1f-20ba34627ad4',
			'name' => '7143',
			'bet_extra_pct' => '0.2500'
		),
		array(
			'id' => '558ade3b-d4e8-430c-8d43-20ba34627ad4',
			'name' => '5143',
			'bet_extra_pct' => '0.1500'
		),
	);

}
