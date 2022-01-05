<?php
# DTCoupons. A module for CMS - CMS Made Simple
# This module supports coupon maintenance and gives discount.
# Copyright (c) 2011 by Duketown
#
# This function allows the administrator to update the preferences
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

$gCms = cmsms();
if (!is_object($gCms)) exit;

if (!$this->CheckPermission('ModifyCouponsCore')) {
	return;
}
$user_id = '';
if (get_userid()) {
	$user_id = get_userid();
}

// Normal save function
$this->SetPreference('defaultstatus', $params['defaultstatus']);
$this->SetPreference('dateformat', $params['dateformat']);
$this->SetPreference('generate', 0);
if (isset($params['generate'])) {
	$this->SetPreference('generate', 1);
}
$this->SetPreference('code_length', $params['code_length']);
$this->SetPreference('code_upper', 0);
if (isset($params['code_upper'])) {
	$this->SetPreference('code_upper', 1);
}
// Save the cross modules value(s) for this user
set_preference($user_id, 'pagelimit', $params['pagelimit']);

$params = array('tab_message' => $this->Lang('optionsupdated'), 'active_tab' => 'options');
$this->Redirect($id, 'defaultadmin', '', $params);
