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

$gCms = cmsms();
if (!is_object($gCms)) exit;

if (!$this->CheckAccess()) {
	return $this->DisplayErrorPage($id, $params, $returnid, $this->Lang('accessdenied'));
}

if (FALSE == empty($params['active_tab'])) {
	$tab = $params['active_tab'];
} else {
	$tab = '';
}

echo $this->StartTabHeaders();
echo $this->SetTabHeader('coupons', $this->Lang('title_coupons'), ('coupons' == $tab) ? true : false);
if ($this->CheckAccess('ModifyCouponsCore')) {
	echo $this->SetTabHeader(
		'options',
		$this->Lang('title_options'),
		('options' == $tab) ? true : false
	);
}
echo $this->EndTabHeaders();

// The content of the tabs
echo $this->StartTabContent(); {
	// --- Start tab coupons ---
	echo $this->StartTab('coupons', $params);

	// Display the coupons
	include(dirname(__FILE__) . '/function.admin_couponstab.php');

	echo $this->EndTab();
	// --- End tab coupons ---


	// --- Start tab options ---
	echo $this->StartTab('options', $params);

	// Display the options
	include(dirname(__FILE__) . '/function.admin_optionstab.php');

	echo $this->EndTab();
	// --- End tab options ---


}
echo $this->EndTabContent();
