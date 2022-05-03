<?php

//@rbac example: overriding plugin views to customize in your app
echo $this->Html->tag('h2', __('Security Roles'));
echo $this->Form->create(array());
echo $this->element('perms/security_roles');
echo $this->Form->end();
