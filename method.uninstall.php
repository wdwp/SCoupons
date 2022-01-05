<?php
# DTCoupons. A module for CMS - CMS Made Simple
# This module supports coupon maintenance and gives discount.
# Copyright (c) 2011 by Duketown
#
# This function will remove all module DTCoupons related material
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

// Initialize the Database
$db = cmsms()->GetDb();

// Remove the database table
$dict = NewDataDictionary( $db );
$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_dtcoupons_coupon' );
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_dtcoupons_ordercoupon' );
$dict->ExecuteSQLArray($sqlarray);

// Remove the sequence
$db->DropSequence( cms_db_prefix().'module_dtcoupons_coupon_seq' );
$db->DropSequence( cms_db_prefix().'module_dtcoupons_ordercoupon_seq' );

// Remove the permissions
$this->RemovePermission('ModifyCouponsCore');

// Remove all the preferences that are connected to this module
$this->RemovePreference();

// Prepare an audit trail in the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>