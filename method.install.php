<?php
# DTCoupons. A module for CMS - CMS Made Simple
# This module supports coupon maintenance and gives discount.
# Copyright (c) 2011 by Duketown
#
# This function will create tables and generate records needed for the module DTCoupons
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

// Initialize the Database
$db = cmsms()->GetDb();

// MySQL-specific, but ignored by other database
$taboptarray = array('mysql' => 'ENGINE=MyISAM');
$dict = NewDataDictionary($db);

// Table schema description
$flds = "
	coupon_id I KEY,
	coupon_code C(12),
	description C(80),
	start_date " . CMS_ADODB_DT . ",
	end_date " . CMS_ADODB_DT . ",
	type C(1),
	value F,
	order_minimum F,
	code_redemptions_max I,
	code_redemptions_used I,
	user_redemptions_max I,
	status L
	";

// Create it.
$sqlarray = $dict->CreateTableSQL(
	cms_db_prefix() . 'module_dtcoupons_coupon',
	$flds,
	$taboptarray
);
$dict->ExecuteSQLArray($sqlarray);

// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_dtcoupons_coupon_seq');


// Table schema description
$flds = "
	order_coupon_id I KEY,
	order_id I,
	coupon_id I,
	order_amount F,
	coupon_value F
	";

// Create it.
$sqlarray = $dict->CreateTableSQL(
	cms_db_prefix() . 'module_dtcoupons_ordercoupon',
	$flds,
	$taboptarray
);
$dict->ExecuteSQLArray($sqlarray);

// Create a sequence
$db->CreateSequence(cms_db_prefix() . 'module_dtcoupons_ordercoupon_seq');

// Permissions
$this->CreatePermission('ModifyCouponsCore', 'Simple Coupons: Modify core tables');

// Preferences
$this->SetPreference('defaultstatus', 1);
$this->SetPreference('dateformat', 'd-m-yy');
$this->SetPreference('generate', true);
$this->SetPreference('code_length', 4);
$this->SetPreference('code_upper', true);

// Write audit trail to the admin log
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
