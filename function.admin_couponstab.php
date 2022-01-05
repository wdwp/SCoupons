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

$themeObject = \cms_utils::get_theme_object();

$user_id = '';
if (get_userid()) {
	$user_id = get_userid();
	$language = get_preference($user_id, 'default_cms_language');
	setlocale(LC_ALL, $language);
}
$dateformat = $this->GetPreference('dateformat', 'd-m-yy');

// Retrieve the selection criteria from previous visit
if (isset($user_id)) {
	$usedcoupondescription = get_preference($user_id, 'dtc_coupondescription');
	$pagelimit = get_preference($user_id, 'pagelimit');
}

if (isset($params['coupondescription'])) {
	$usedcoupondescription = $params['coupondescription'];
}
if (isset($params['submitcouponfilter']) && $params['submitcouponfilter'] != '') {
	set_preference($user_id, 'dtc_coupondescription', $usedcoupondescription);
}

// Setup pagination
if (!isset($pagelimit) || $pagelimit == 0) {
	$pagelimit = $this->GetPreference('pagelimit', 20);
}
$pagenumber = 1;
if (isset($params['pagenumber'])) {
	$pagenumber = $params['pagenumber'];
}
$startelement = ($pagenumber - 1) * $pagelimit;


// Check if Simple Cart is installed
// Prepare settings for usage of cart
$modops = cmsms()->GetModuleOperations();
$CartMS = $modops->get_module_instance('SimpleCart');

if ($CartMS) {
	$query1 = 'SELECT * FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon';
	$query1 .= ' WHERE 1=1';
	$query2 = 'SELECT COUNT(*) AS count FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon';
	$query2 .= ' WHERE 1=1';
	if ($usedcoupondescription != '') {
		$query1 .= ' AND (description REGEXP "' . $usedcoupondescription . '")';
		$query2 .= ' AND (description REGEXP "' . $usedcoupondescription . '")';
	}
	$query1 .= ' ORDER BY end_date DESC';
	// Make sure pagination works
	$numrows = -1;
	if (isset($parms) && count($parms) > 0) {
		$dbresult = $db->SelectLimit($query1, $pagelimit, $startelement, $parms);
		$row = $db->GetRow($query2, $parms);
		$numrows = $row['count'];
	} else {
		$dbresult = $db->SelectLimit($query1, $pagelimit, $startelement);
		$row = $db->GetRow($query2);

		$numrows = $row['count'];
	}

	$pagecount = (int)($numrows / $pagelimit);

	if (($numrows % $pagelimit) != 0) $pagecount++;
	// some pagination variables to smarty.
	if ($pagenumber == 1) {
		$smarty->assign('prevpage', $this->Lang('prevpage'));
		$smarty->assign('firstpage', $this->Lang('firstpage'));
	} else {
		$smarty->assign(
			'prevpage',
			$this->CreateLink(
				$id,
				'defaultadmin',
				$returnid,
				$this->Lang('prevpage'),
				array(
					'pagenumber' => $pagenumber - 1,
					'active_tab' => 'coupons'
				)
			)
		);
		$smarty->assign(
			'firstpage',
			$this->CreateLink(
				$id,
				'defaultadmin',
				$returnid,
				$this->Lang('firstpage'),
				array(
					'pagenumber' => 1,
					'active_tab' => 'coupons'
				)
			)
		);
	}
	if ($pagenumber >= $pagecount) {
		$smarty->assign('nextpage', $this->Lang('nextpage'));
		$smarty->assign('lastpage', $this->Lang('lastpage'));
	} else {
		$smarty->assign(
			'nextpage',
			$this->CreateLink(
				$id,
				'defaultadmin',
				$returnid,
				$this->Lang('nextpage'),
				array(
					'pagenumber' => $pagenumber + 1,
					'active_tab' => 'coupons'
				)
			)
		);
		$smarty->assign(
			'lastpage',
			$this->CreateLink(
				$id,
				'defaultadmin',
				$returnid,
				$this->Lang('lastpage'),
				array(
					'pagenumber' => $pagecount,
					'active_tab' => 'coupons'
				)
			)
		);
	}
	$smarty->assign('pagenumber', $pagenumber);
	$smarty->assign('pagecount', $pagecount);
	$smarty->assign('pagename', $this->Lang('pagename'));
	$smarty->assign('oftext', $this->Lang('prompt_of'));

	$rowclass = 'row1';
	$entryarray = array();

	while ($dbresult && $row = $dbresult->FetchRow()) {
		$onerow = new stdClass();

		$onerow->id = $row['coupon_id'];
		$onerow->coupon_code = $row['coupon_code'];
		$onerow->description = $this->CreateLink($id, 'editcoupon', $returnid, $row['description'], array('coupon_id' => $row['coupon_id']));
		$onerow->start_date = $row['start_date'];
		$onerow->end_date = $row['end_date'];
		switch ($row['type']) {
			case 'P':
				$onerow->value = $row['value'] . '%';
				break;
			default:
				$onerow->value = $row['value'];
				break;
		}
		if ($row['status'] == 1) {
			$onerow->statuslink = $this->CreateLink(
				$id,
				'switchstatus',
				$returnid,
				$themeObject->DisplayImage('icons/system/true.gif', $this->Lang('setinactive'), '', '', 'systemicon'),
				array('table' => 'Coupons', 'status' => 0, 'coupon_id' => $row['coupon_id'])
			);
		} else {
			$onerow->statuslink = $this->CreateLink(
				$id,
				'switchstatus',
				$returnid,
				$themeObject->DisplayImage('icons/system/false.gif', $this->Lang('setactive'), '', '', 'systemicon'),
				array('table' => 'Coupons', 'status' => 1, 'coupon_id' => $row['coupon_id'])
			);
		}
		$onerow->editlink = $this->CreateLink($id, 'editcoupon', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'), '', '', 'systemicon'), array('coupon_id' => $row['coupon_id']));
		$onerow->deletelink = $this->CreateLink($id, 'deleterow', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('table' => 'Coupons', 'coupon_id' => $row['coupon_id']), $this->Lang('areyousure'));

		$onerow->rowclass = $rowclass;

		$entryarray[] = $onerow;

		($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
	}

	$smarty->assign_by_ref('items', $entryarray);
	$smarty->assign('itemcount', count($entryarray));

	// Setup links
	$smarty->assign('addcouponlink', $this->CreateLink($id, 'addcoupon', $returnid, $themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('addcoupon'), '', '', 'systemicon'), array(), '', false, false, '') . ' ' . $this->CreateLink($id, 'addcoupon', $returnid, $this->Lang('addcoupon'), array(), '', false, false, 'class="pageoptions"'));

	$smarty->assign('title_description', $this->Lang('title_description'));
	$smarty->assign('title_coupon_code', $this->Lang('title_coupon_code'));
	$smarty->assign('title_start_date', $this->Lang('title_start_date'));
	$smarty->assign('title_end_date', $this->Lang('title_end_date'));
	$smarty->assign('title_value', $this->Lang('title_value'));
	$smarty->assign('title_status', $this->Lang('title_status'));
} else {
	$smarty->assign('message', $this->Lang('cartmsnotinstalled'));
}
if ($dateformat == 'd-m-yy') {
	$smarty->assign('smarty_date', '%d-%m-%Y');
} else {
	$smarty->assign('smarty_date', '%m-%d-%Y');
}
$smarty->assign('formstart', $this->CreateFormStart($id, 'defaultadmin', $returnid, 'post', '', false, '', $params));
$smarty->assign('title_description', $this->Lang('title_description'));
$smarty->assign('input_description', $this->CreateInputText($id, 'coupondescription', $usedcoupondescription, 20, 80));
$smarty->assign('title_coupon_filter', $this->Lang('title_coupon_filter'));
$smarty->assign('hidden', $this->CreateInputHidden($id, 'active_tab', 'coupons'));

$smarty->assign('submitcouponfilter', $this->CreateInputSubmit($id, 'submitcouponfilter', $this->Lang('submit')));
//$smarty->assign('submitcouponreset', $this->CreateInputSubmit($id, 'submitcouponreset', $this->Lang('reset')));
$smarty->assign('formend', $this->CreateFormEnd());

// Display the options
echo $this->ProcessTemplate('listcoupons.tpl');
