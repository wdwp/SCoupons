<?php
# DTCoupons. A module for CMS - CMS Made Simple
# This module supports coupon maintenance and gives discount.
# Copyright (c) 2011 by Duketown
#
# This function supports the back end for the module DTCoupons
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/dtcoupons
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

$gCms = cmsms(); if( !is_object($gCms) ) exit;

// Prepare list of possible date formats
$dateformatlist = array();
$dateformatlist = DTC_utils::GetDateformats();

// Prepare list of possible lengths of code
$code_lengthlist = array();
$code_lengthlist = DTC_utils::GetListCodeLength();

// Prepare the list of page limits
$pagelimitlist = array();
$pagelimitlist = DTC_utils::GetListPageLimit();

$this->smarty->assign('startform', $this->CreateFormStart ($id, 'save_admin_options', $returnid));
$smarty->assign('title_dateformat',$this->Lang('title_dateformat'));
$smarty->assign('input_dateformat', $this->CreateInputDropdown($id, 'dateformat',$dateformatlist, -1, 
	$this->GetPreference('dateformat', 'd-m-yy')));
$smarty->assign('title_code_generator',$this->Lang('title_code_generator'));
$smarty->assign('title_generate',$this->Lang('title_generate'));
$smarty->assign('input_generate', $this->CreateInputCheckbox($id, 'generate', true, 
	$this->GetPreference('generate', true)));
$smarty->assign('title_code_length',$this->Lang('title_code_length'));
$smarty->assign('input_code_length', $this->CreateInputDropdown($id, 'code_length',$code_lengthlist, -1, 
	$this->GetPreference('code_length', 4)));
$smarty->assign('title_code_upper',$this->Lang('title_code_upper'));
$smarty->assign('input_code_upper', $this->CreateInputCheckbox($id, 'code_upper', true, 
	$this->GetPreference('code_upper', true)));
$smarty->assign('title_pagelimit',$this->Lang('title_pagelimit'));
$smarty->assign('input_pagelimit', $this->CreateInputDropdown($id, 'pagelimit', $pagelimitlist, -1, 
	get_preference(get_userid(),'pagelimit')));

$smarty->assign('submit', $this->CreateInputSubmit ($id, 'optionssubmitbutton', $this->Lang('submit')));
$smarty->assign('endform', $this->CreateFormEnd ());

// Display the options
echo $this->ProcessTemplate('listoptions.tpl');

?>
