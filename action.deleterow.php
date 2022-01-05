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

switch ($params['table'])
{
	case 'Coupons':
		// Remove the orders that use this coupon
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dtcoupons_ordercoupon
			WHERE coupon_id = ?';
		$row = $db->GetRow( $query, array($params['coupon_id']) );
		if ($row) {
			$query = 'DELETE FROM '.cms_db_prefix().'module_dtcoupons_ordercoupon
				WHERE coupon_id = ?';
			$db->Execute($query, array($params['coupon_id']) );
		}
		// Now remove the coupon
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dtcoupons_coupon
			WHERE coupon_id = ?';
		$row = $db->GetRow( $query, array($params['coupon_id']) );
		if ($row) {
			$query = 'DELETE FROM '.cms_db_prefix().'module_dtcoupons_coupon
				WHERE coupon_id = ?';
			$db->Execute($query, array($params['coupon_id']) );
			$params = array('tab_message'=> $this->Lang('coupondeleted'), 'active_tab' => 'coupons');
		}
		break;
	default:
		break;
}

// redirect the user to the default admin screen
$this->Redirect($id, 'defaultadmin', $returnid, $params);
