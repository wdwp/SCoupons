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
if (isset($params['cancel'])) {
	$this->Redirect($id, 'defaultadmin', $returnid);
}


$coupon_id = '';
if (isset($params['coupon_id'])) {
	$coupon_id = $params['coupon_id'];
}
$dateformat = $this->GetPreference('dateformat', 'd-m-yy');

$coupon_code = '';
if (isset($params['coupon_code'])) {
	$coupon_code = trim($params['coupon_code']);
}
$start_date = '';
if (isset($params['start_date'])) {
	$start_date = trim($params['start_date']);
}
$end_date = '';
if (isset($params['end_date'])) {
	$end_date = trim($params['end_date']);
}
$type = '';
if (isset($params['type'])) {
	$type = $params['type'];
}
$value = '';
if (isset($params['value'])) {
	$value = trim(str_replace('%', '', $params['value']));
}
$order_minimum = '';
if (isset($params['order_minimum'])) {
	$order_minimum = trim($params['order_minimum']);
}
$code_redemptions_max = '';
if (isset($params['code_redemptions_max'])) {
	$code_redemptions_max = trim($params['code_redemptions_max']);
}
$user_redemptions_max = '';
if (isset($params['user_redemptions_max'])) {
	$user_redemptions_max = trim($params['user_redemptions_max']);
}
$status = $this->GetPreference('defaultstatus', true);
if (isset($params['status'])) {
	$status = 1;
} else {
	$status = 0;
}

$description = isset($params['description']) ? trim($params['description']) : '';

if (isset($params['submit'])) {
	if ($description != '') {
		$sqldatefrom = $this->PrepareSQLDate($start_date, $dateformat);
		$sqldateto = $this->PrepareSQLDate($end_date, $dateformat);

		$sqldatefrom .= ' 00:00:00';
		$sqldateto .= ' 23:59:59';
		if ($sqldatefrom > $sqldateto) {
			$sqldateto = $sqldatefrom;
		}
		$query = 'UPDATE ' . cms_db_prefix() . 'module_dtcoupons_coupon
			SET description = ?, start_date = ?, end_date = ?,
			type = ?, value = ?, order_minimum = ?, code_redemptions_max = ?,
			user_redemptions_max = ?, status = ?
			WHERE coupon_id = ?';
		$db->Execute($query, array(
			$description,
			$sqldatefrom, $sqldateto,
			$type, $value, $order_minimum, $code_redemptions_max,
			$user_redemptions_max, $status, $coupon_id
		));

		// Add more fields as needed to the send event
		@$this->SendEvent('SCouponsCouponEdited', array('coupon_id' => $coupon_id, 'coupon_code' => $coupon_code));

		$params = array('tab_message' => $this->Lang('couponupdated'), 'active_tab' => 'coupons');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	} else {
		echo $this->ShowErrors($this->Lang('nodescriptiongiven'));
	}
} else {
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon
		WHERE coupon_id= ?';
	$row = $db->GetRow($query, array($coupon_id));

	if ($row) {
		$coupon_code = $row['coupon_code'];

		$description = $row['description'];
		$start_date = $this->PrepareShowDate(substr($row['start_date'], 0, 10), $dateformat);
		$end_date = $this->PrepareShowDate(substr($row['end_date'], 0, 10), $dateformat);
		$type = $row['type'];
		$value = $row['value'];
		$order_minimum = $row['order_minimum'];
		$code_redemptions_max = $row['code_redemptions_max'];
		$code_redemptions_used = $row['code_redemptions_used'];
		$user_redemptions_max = $row['user_redemptions_max'];
		$status = $row['status'];
	}
}

// Prepare list of possible discount types
$coupontypelist = array();
$coupontypelist = DTC_utils::GetCouponTypes();

#Display template
$smarty->assign('startform', $this->CreateFormStart($id, 'editcoupon', $returnid));
$smarty->assign('endform', $this->CreateFormEnd());

$smarty->assign('title_coupon_code', $this->Lang('title_coupon_code'));
$smarty->assign('input_coupon_code', $coupon_code);
$smarty->assign('title_description', $this->Lang('title_description'));
$smarty->assign('input_description', $this->CreateInputText($id, 'description', $description, 50, 80));
$smarty->assign('title_start_date', $this->Lang('title_start_date'));
$smarty->assign('input_start_date', $this->CreateInputText($id, 'start_date', $start_date, 12, 20));
$smarty->assign('title_end_date', $this->Lang('title_end_date'));
$smarty->assign('input_end_date', $this->CreateInputText($id, 'end_date', $end_date, 12, 20));
$smarty->assign('title_type', $this->Lang('title_type'));
$smarty->assign('input_type', $this->CreateInputDropdown(
	$id,
	'type',
	$coupontypelist,
	-1,
	$type
));
$smarty->assign('title_value', $this->Lang('title_value'));
$smarty->assign('input_value', $this->CreateInputText($id, 'value', $value, 10));
$smarty->assign('title_order_minimum', $this->Lang('title_order_minimum'));
$smarty->assign('input_order_minimum', $this->CreateInputText($id, 'order_minimum', $order_minimum, 10));
$smarty->assign('title_code_redemptions_max', $this->Lang('title_code_redemptions_max'));
$smarty->assign('input_code_redemptions_max', $this->CreateInputText($id, 'code_redemptions_max', $code_redemptions_max, 5, 11));
$smarty->assign('title_code_redemptions_used', $this->Lang('title_code_redemptions_used'));
$smarty->assign('input_code_redemptions_used', $code_redemptions_used);
$smarty->assign('title_user_redemptions_max', $this->Lang('title_user_redemptions_max'));
$smarty->assign('input_user_redemptions_max', $this->CreateInputText($id, 'user_redemptions_max', $user_redemptions_max, 5, 11));
$smarty->assign('title_status', $this->Lang('title_status'));
$smarty->assign('input_status', $this->CreateInputCheckbox($id, 'status', true, $status));

$smarty->assign('hidden', $this->CreateInputHidden($id, 'coupon_id', $coupon_id)
	. $this->CreateInputHidden($id, 'coupon_code', $coupon_code));
$smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$smarty->assign('dateformat', $dateformat);

echo $this->ProcessTemplate('editcoupon.tpl');
