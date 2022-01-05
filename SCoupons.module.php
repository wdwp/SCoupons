<?php
#-------------------------------------------------------------------------
# Fork of Module: DTCoupons - This module supports coupon maintenance and gives discount.
# Version: 0.1, Duketown
# Forked by Yuri Haperski (wdwp@yandex.ru)
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2011 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
# This file originally created by ModuleMaker module, version 0.3.1
# Copyright (c) 2011 by Samuel Goldstein (sjg@cmsmadesimple.org)
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

class SCoupons extends CMSModule
{

	function GetName()
	{
		return 'SCoupons';
	}

	function GetFriendlyName()
	{
		// Set the version in a preference, for speed and check in various other
		// inventory management programs
		$this->SetPreference('SCoupons' . 'version', 'free');
		$fn = cms_join_path(dirname(__FILE__), 'gold');
		if (file_exists($fn)) {
			$this->SetPreference('SCoupons' . 'version', 'gold');
			return $this->Lang('friendlynamegold');
		}
		$fn = cms_join_path(dirname(__FILE__), 'silver');
		if (file_exists($fn)) {
			$this->SetPreference('SCoupons' . 'version', 'silver');
			return $this->Lang('friendlynamesilver');
		}
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '1.0';
	}

	function GetHelp()
	{
		return $this->Lang('help');
	}

	function GetAuthor()
	{
		return 'Duketown';
	}

	function GetAuthorEmail()
	{
		return '';
	}

	function GetChangeLog()
	{
		return file_get_contents(dirname(__FILE__) . '/changelog.inc');
	}

	function IsPluginModule()
	{
		return false;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetAdminSection()
	{
		return 'ecommerce';
	}

	function GetAdminDescription()
	{
		return $this->Lang('admindescription');
	}

	function VisibleToAdminUser()
	{
		return $this->CheckAccess();
	}


	/*---------------------------------------------------------
	   CheckAccess()
	   This wrapper function will check against the specified permission,
	   and display an error page if the user doesn't have adequate permissions.
	  ---------------------------------------------------------*/
	function CheckAccess($perm = 'ModifyCouponsCore')
	{
		return $this->CheckPermission($perm);
	}

	/*---------------------------------------------------------
	   DisplayErrorPage()
	   This is a simple function for generating error pages.
	  ---------------------------------------------------------*/
	function DisplayErrorPage($id, &$params, $return_id, $message = '')
	{
		$this->smarty->assign('title_error', $this->Lang('error'));
		$this->smarty->assign_by_ref('message', $message);

		// Display the populated template
		echo $this->ProcessTemplate('error.tpl');
	}



	/*---------------------------------------------------------
	   GetDependencies()
	   Your module may need another module to already be installed
	   before you can install it.
	   This method returns a list of those dependencies and
	   minimum version numbers that this module requires.

	   It should return an hash, eg.
	   return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
	  ---------------------------------------------------------*/
	function GetDependencies()
	{
		return array('SimpleCart' => '1.0');
	}

	function MinimumCMSVersion()
	{
		return '2.1.0';
	}

	function MaximumCMSVersion()
	{
		return '3.0.0';
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	function UninstallPreMessage()
	{
		return $this->Lang('really_uninstall');
	}

	/*---------------------------------------------------------
	   SetParameters()
	   Description of the available parameters for the front end
	  ---------------------------------------------------------*/
	function SetParameters()
	{
		$this->RestrictUnknownParams();

		#$this->CreateParameter('display', '', $this->Lang('helpdisplay'));
		#$this->CreateParameter('status', '', $this->Lang('helpstatus'));

		$this->SetParameterType('currentuser', CLEAN_INT);
		$this->SetParameterType('detailpage', CLEAN_STRING);

		// Form parameters
		$this->SetParameterType('submit', CLEAN_STRING);
		$this->SetParameterType('cancel', CLEAN_STRING);

		$this->mCachable = false;
	}

	/*---------------------------------------------------------
	   CalculateDiscount($totalproduct, $coupon_code, $user_id)
	   Check if a coupon code is used.
	   If so generate discount amount
	   @totalproduct: the amount that forms the basis
	   @coupon_code: as entered by user
	   @user_id: gives option to check if user is part of specific FEU group
	  ---------------------------------------------------------*/
	function CalculateDiscount($totalproduct, $coupon_code, $user_id = -1)
	{
		$discount_amount = 0;
		// Initialize the Database
		$db = cmsms()->GetDb();
		// If the shop only accepts upper case, don't make customer uppercase it
		if ($this->GetPreference('code_upper', true) == true) {
			$coupon_code = strtoupper($coupon_code);
		}

		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon
			WHERE coupon_code = ?';
		$row = $db->GetRow($query, array($coupon_code));
		if ($row) {
			// Check if maximum discounts for orders has been used
			if (
				$row['code_redemptions_max'] > 0 &&
				$row['code_redemptions_max'] > $row['code_redemptions_used'] ||
				$row['code_redemptions_max'] == 0
			) {
				if ($row['order_minimum'] < $totalproduct) {
					switch ($row['type']) {
						case 'P':
							$discount_amount = round(($totalproduct * $row['value']) / 100, 2);
							break;
						default:
							$discount_amount = $row['value'];
							break;
					}
				}
			}
		}
		return $discount_amount;
	}

	/*---------------------------------------------------------
	   CheckCouponCode()
	   Check if a coupon code passed is valid
	   If so generate one based upon options
	  ---------------------------------------------------------*/
	function CheckCouponCode($coupon_code = '')
	{
		// Initialize the Database
		$db = cmsms()->GetDb();
		// If the shop only accepts upper case, don't make customer uppercase it
		if ($this->GetPreference('code_upper', true) == true) {
			$coupon_code = strtoupper($coupon_code);
		}

		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon
			WHERE coupon_code = ?';
		$row = $db->GetRow($query, array($coupon_code));
		if ($row) {
			// Check if this is still an active coupon code
			if ($row['status'] == 0) {
				return false;
			}
			$now = trim($db->DBTimeStamp(time()), "'");
			if ($row['start_date'] < $now && $row['end_date'] > $now) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*---------------------------------------------------------
	   GenerateCouponCode()
	   Check if a coupon code should be generated.
	   If so generate one based upon options
	  ---------------------------------------------------------*/
	function GenerateCouponCode($length = -1)
	{
		if ($this->GetPreference('generate', true) == 0) return false;
		if ($length == -1) {
			$length = $this->GetPreference('code_length', 4);
		}
		// Script taken from Jon Haworth
		// Start with a blank coupon code
		$couponcode = '';
		// Define possible characters
		$possible = '0123456789BCDFGHJKMNPQRSTVWXYZ';
		if ($this->GetPreference('code_upper', true) == false) {
			$possible .= 'bcdfghjkmnpqrstvwxyz';
		}
		// Set up a counter
		$i = 0;
		// Add random characters to $couponcode until $length is reached
		while ($i < $length) {
			// Pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
			// We don't want this character if it's already in the couponcode
			if (!strstr($couponcode, $char)) {
				$couponcode .= $char;
				$i++;
			}
		}

		return $couponcode;
	}

	/*---------------------------------------------------------
	   GetDashboardOutput()
	   This function is used by Admin\dashboard.php.
	   Carefull dashboard.php is not working as of version 1.6
	   The string that is prepared in this function is displayed on the dashboard.
	  ---------------------------------------------------------*/
	function GetDashboardOutput()
	{
		#$output = 'Hello world';
		#return $output;
	}

	/*---------------------------------------------------------
	   GetHeaderHTML()
	   This function inserts javascript (and links) into header of HTML
	  ---------------------------------------------------------*/
	function GetHeaderHTML()
	{
		// Include script so sorting of tables in backend is possible
		$javascript = '<script src="/modules/SimpleShop/js/jquery.tablesorter.min.js"></script>' . "\n";
		$javascript .= '<link href="/modules/SimpleShop/css/theme.metro-dark.min.css" rel="stylesheet">' . "\n";
		$javascript .= '<script id="js">jQuery(document).ready(function()
		{
			jQuery(".tablesorter")
				.tablesorter({theme: "metro-dark"});
		}
		);
		</script>';

		return $javascript;
	}

	/* --------------------------------------------------------
		PrepareShowDate($date2convert, $dateformat = '')
		A function that returns date in format used for showing date
		--------------------------------------------------------*/
	function PrepareShowDate($date2convert, $dateformat = '')
	{
		$datesep = '-';
		// Retrieve the input date format used
		if ($dateformat == '') {
			$dateformat = $this->GetPreference('dateformat', 'd-m-yy');
		}
		$date = explode("-", $date2convert);
		$date_y = $date[0];
		if ($date['1'] < 10) {
			$date_m = substr($date['1'], 1, 1);
		} else {
			$date_m = $date[1];
		}
		$date_d = $date[2];
		switch ($dateformat) {
			case 'd-m-yy':
				return $date_d . $datesep . $date_m . $datesep . $date_y;
				break;
			case 'm-d-yy':
				return $date_m . $datesep . $date_d . $datesep . $date_y;
				break;
		}
	}

	/* --------------------------------------------------------
		PrepareSQLDate($date2convert, $dateformat = '')
		A function that returns date in format used in sql statements
		--------------------------------------------------------*/
	function PrepareSQLDate($date2convert, $dateformat = '')
	{
		$datesep = '-';
		// Retrieve the input date format used
		if ($dateformat == '') {
			$dateformat = $this->GetPreference('dateformat', 'd-m-yy');
		}
		$date = explode("-", $date2convert);
		switch ($dateformat) {
			case 'd-m-yy':
				$date_y = $date[2];
				$date_m = $date[1];
				$date_d = $date[0];
				break;
			case 'm-d-yy':
				$date_y = $date[2];
				$date_m = $date[0];
				$date_d = $date[1];
				break;
		}
		return $date_y . $datesep . $date_m . $datesep . $date_d;
	}

	/* --------------------------------------------------------
		UpdateCouponUsage($coupon_code, $order_id, $order_amount, $coupon_value)
		A function that update the statistics of the used coupons
		--------------------------------------------------------*/
	function UpdateCouponUsage($coupon_code, $order_id, $order_amount, $coupon_value)
	{
		// Initialize the Database
		$db = cmsms()->GetDb();
		// If the shop only accepts upper case, don't make customer uppercase it
		if ($this->GetPreference('code_upper', true) == true) {
			$coupon_code = strtoupper($coupon_code);
		}

		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_dtcoupons_coupon
			WHERE coupon_code = ?';
		$row = $db->GetRow($query, array($coupon_code));
		if ($row) {
			$query = 'UPDATE ' . cms_db_prefix() . 'module_dtcoupons_coupon
				SET code_redemptions_used = code_redemptions_used + 1
				WHERE coupon_code = ?';
			$db->Execute($query, array($coupon_code));
			// Add statistics line for order that used coupon
			$order_coupon_id = $db->GenID(cms_db_prefix() . 'module_dtcoupons_ordercoupon_seq');
			$query = 'INSERT INTO ' . cms_db_prefix() . 'module_dtcoupons_ordercoupon
				(order_coupon_id, order_id, coupon_id, order_amount, coupon_value)
				VALUES (?,?,?,?,?)';
			$db->Execute($query, array(
				$order_coupon_id, $order_id, $row['coupon_id'],
				$order_amount, $coupon_value
			));
			return true;
		} else {
			return false;
		}
	}
}
