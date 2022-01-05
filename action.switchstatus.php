<?php

// No direct access
if (!isset($gCms)) {
	exit();
}
// Check permission
if (!$this->CheckPermission('ModifyCouponsCore')) {
	echo $this->ShowErrors($this->Lang('needpermission', 'Discount Coupons: Modify core tables'));
}
// User has sufficient privileges
else {
	$params['status'] = !empty($params['status']) ? $params['status'] : 0;
	switch ($params['table']) {
		case 'Coupons':
			$query = 'UPDATE ' . cms_db_prefix() . 'module_dtcoupons_coupon SET status = ?
				WHERE coupon_id = ?';
			$db->Execute($query, array($params['status'], $params['coupon_id']));
			$params = array('active_tab' => 'coupons');
			break;

		default:
			break;
	}

	// Redirect the user to the default admin screen
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
