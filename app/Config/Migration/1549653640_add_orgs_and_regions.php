<?php
class AddOrgsAndRegions extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_orgs_and_regions';

/**
 * List of Organization Names
 *
 * @var array
 */
 	public $orgs = [
		[ "name" => "St. Joseph Health (CA)"],
		[ "name" => "Aegis"],
		[ "name" => "Aids Healthcare Foundation"],
		[ "name" => "AltaMed Health Services"],
		[ "name" => "American Medical Response"],
		[ "name" => "Antelope Valley Hospital"],
		[ "name" => "Associates for Behavioral Healthcare"],
		[ "name" => "Acumen Physician Solutions"],
		[ "name" => "Blessing Health System"],
		[ "name" => "Blount Memorial Hospital"],
		[ "name" => "Children's Mercy Hospitals and Clinics"],
		[ "name" => "Children's National Health System"],
		[ "name" => "Emanate"],
		[ "name" => "City of Hope"],
		[ "name" => "Denver Health"],
		[ "name" => "Diagnostic Laboratories"],
		[ "name" => "Eisenhower Medical Center"],
		[ "name" => "El Camino Hospital"],
		[ "name" => "Ephraim McDowell Health"],
		[ "name" => "Erlanger Health System"],
		[ "name" => "Estes Park Medical Center"],
		[ "name" => "Franciscan Missionaries of Our Lady Health System"],
		[ "name" => "Hallmark Health"],
		[ "name" => "Hollywood Presbyterian Medical Center"],
		[ "name" => "Imperial Health"],
		[ "name" => "IU Health"],
		[ "name" => "Johns Hopkins"],
		[ "name" => "King's Daughters' Health"],
		[ "name" => "Lafayette General Medical Center"],
		[ "name" => "Liberty Hospital"],
		[ "name" => "LifePoint"],
		[ "name" => "Little Company of Mary"],
		[ "name" => "Martha's Vineyard Hospital"],
		[ "name" => "Navicent Health"],
		[ "name" => "New York City Health and Hospitals Corporation"],
		[ "name" => "Northbay Medical Center"],
		[ "name" => "Northeast Georgia Health System"],
		[ "name" => "Northern Maine Medical Center"],
		[ "name" => "Norwegian American Hospital"],
		[ "name" => "Ochsner Health"],
		[ "name" => "Olympia Medical Center"],
		[ "name" => "Pekin Health"],
		[ "name" => "Phoebe Putney Health Systems"],
		[ "name" => "Phoenix Children's Hospital"],
		[ "name" => "Riverside Health System"],
		[ "name" => "RWJ Barnabas Health"],
		[ "name" => "Sacred Heart Hospital"],
		[ "name" => "Salinas Valley Memorial Hospital"],
		[ "name" => "Sansum Clinics"],
		[ "name" => "Saratoga Hospital"],
		[ "name" => "Silver Cross Hospital"],
		[ "name" => "South Shore Hospital"],
		[ "name" => "St Joseph Hospital (ME)"],
		[ "name" => "Swedish Covenant Hospital"],
		[ "name" => "Tahoe Forest Hospital"],
		[ "name" => "Terrebonne General Medical Center"],
		[ "name" => "The Christ Hospital"],
		[ "name" => "The Valley Hospital"],
		[ "name" => "Trinitas Regional Medical Center"],
		[ "name" => "UHS"],
		[ "name" => "USC Medical"],
		[ "name" => "Verity Health"],
		[ "name" => "Victor Valley Global Medical Center"],
		[ "name" => "Washington Hospital"],
		[ "name" => "Washington Regional Medical System"],
		[ "name" => "WellStar"],
		[ "name" => "Yale"],
		[ "name" => "Tidelands Georgetown Memorial Hospital"],
		[ "name" => "Allegiance Health"],
		[ "name" => "Covenant Health"],
		[ "name" => "Cape Regional Medical Central"],
		[ "name" => "RMMCC"],
		[ "name" => "Chesapeake Regional Healthcare"],
		[ "name" => "Children's Hospital & Medical Center Omaha"],
		[ "name" => "Christus Health"],
		[ "name" => "Cincinnati Children's Hospital"],
		[ "name" => "St. Mary's Medical Group"],
		[ "name" => "Coordinated Healthcare"],
		[ "name" => "Evangelical Community Hospital"],
		[ "name" => "Valley Health System"],
		[ "name" => "Hendry County Hospital"],
		[ "name" => "Holy Name Medical Center"],
		[ "name" => "Roane General Hospital"],
		[ "name" => "Inova"],
		[ "name" => "Jefferson Health"],
		[ "name" => "Lake Health"],
		[ "name" => "Lima Memorial Health System"],
		[ "name" => "Sutter Health"],
		[ "name" => "St. Joseph Health - Sonoma County"],
		[ "name" => "Pueblo Medical Imaging"],
		[ "name" => "Ridgecrest Regional Hospital"],
		[ "name" => "Austin Pain Associates"],
		[ "name" => "Spectrum Health"],
		[ "name" => "Hospital Sisters Health System"],
		[ "name" => "Steinberg Diagnostic Medical Imaging"],
		[ "name" => "MobilexUSA"],
		[ "name" => "The Vancouver Clinic"],
		[ "name" => "Trios Health"],
		[ "name" => "Union Hospital"],
		[ "name" => "University Health San Antonio"],
		[ "name" => "US Anesthesia Partners"],
		[ "name" => "Val Verde Regional Medical Center"],
		[ "name" => "Great Lakes Physician Practice"],
		[ "name" => "RCCH"],
		[ "name" => "Baycare Clinic"],
		[ "name" => "Methodist Healthcare"],
		[ "name" => "AHN"],
		[ "name" => "Integris Health"],
		[ "name" => "OSF Healthcare"],
		[ "name" => "Palo Pinto General Hospital"],
		[ "name" => "Trident US Health"],
		[ "name" => "Wood County"],
	];

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
		$Org = ClassRegistry::init('Organization');
		if ($direction === 'up') {
			$mercyOrg = [
				'Organization' => ['name' => 'Mercy Health'],
				'Region' => [
					['name' => 'Youngstown'],
					['name' => 'Toledo'],
					['name' => 'Springfield'],
					['name' => 'Lorain'],
					['name' => 'Lima'],
					['name' => 'Kentucky'],
					['name' => 'Cincinnati'],
				]
			];
			$Org->saveAssociated($mercyOrg, ['deep' => true]);
			$Org->saveMany($this->orgs);
		} else {
			$Org->query("TRUNCATE organizations");
			$Org->query("TRUNCATE regions");
		}
		return true;
	}
}

