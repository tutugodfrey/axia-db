<?php 
/* Drop breadcrumb */
$this->Html->addCrumb(Inflector::humanize($this->name), ["controller" => $this->name, 'action' => 'main_menu']);
$this->Html->addCrumb('Database Metadata and Content editor', ["controller" => $this->name, 'action' => 'content']);
echo $this->Html->tag("div", null, ["class" => "well well-sm"]);
$contentActionslist = array(
	$this->Form->postLink('Generate API documentation now', ['controller' => 'MaintenanceDashboards', 'action' => 'generateApiDoc'])
);
echo $this->Html->tag("div", 
		$this->Html->link("More Content Actions " . $this->Html->tag("span", '<!--caret-->', ["class" => "caret"]), 'javascript::void(0)', ["escape" => false, "class" => "btn btn-sm btn-primary dropdown-toggle", "data-toggle" => "dropdown"]) . 
		$this->Html->nestedList($contentActionslist, ["class" => "dropdown-menu"]),
	["class" => "btn-group pull-right"]);
  
echo $this->Form->create('MaintenanceDashboard',[
			'inputDefaults' => [
				'div' => 'form-group',
				'label' => ['class' => 'col col-xs-6 control-label'],
				'wrapInput' => false,
				'class' => 'form-control'
			],
			'class' => 'form-inline',
			'url' => ['action' => 'edit']
		]
);

//Remember previous selection
echo $this->Html->tag('div', $this->Html->tag('span', '<h6><strong>Select Content &nbsp;</strong></h6>'), ['class' => 'form-group col-md-offset-4']);
echo $this->Form->input('modelName', ['label' => false, 'empty' => '', 'onChange' => 'showAdminBtns(); $("#adminTableOfContent").empty()']);
echo $this->Html->tag('span', null, ['id' => 'adminTableButtons', 'class' => 'form-group','style' => 'display:none']);
echo $this->Html->tag('div', 
	$this->Html->tag('span', 'Edit', [
			'class' => 'btn btn-sm btn-primary', 
			"onClick" => "getTableOfContent()"]
		), 
	['class' => 'form-group']);

echo $this->Form->end(['label' => 'Add', 'class' => 'btn btn-sm btn-success', 'div' => ['class' => 'form-group']]);
echo $this->Html->tag('/span', null);

echo $this->Html->tag("/div");//close outtermost div
echo $this->Html->tag('div', '<!--Ajax Content Renders here-->', ['id' => 'adminTableOfContent']);
?>
