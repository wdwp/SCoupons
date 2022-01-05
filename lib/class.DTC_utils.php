<?php
#-------------------------------------------------------------------------
# Module: DTCoupons - This module supports coupon maintenance and gives discount.
# Version: 0.1, Duketown
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2011 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
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

class DTC_utils
{

	/**
	 * A function to return all possible coupon types in an array
	 * @return array with posible types of coupons
	 */
	function GetCouponTypes()
	{
		$modops = cmsms()->GetModuleOperations();
		$DTI = $modops->get_module_instance('SCoupons');

		$coupontypelist = array();
		$coupontypelist[$DTI->Lang('typepercentage')] = 'P';
		$coupontypelist[$DTI->Lang('typevalue')] = 'V';

		return $coupontypelist;
	}

	/**
	 * A function to return all possible date formats in an array
	 * @return array filled with date formats
	 */
	function GetDateformats()
	{
		$dateformatlist = array();
		$dateformatlist['d-m-Y'] = 'd-m-yy';
		$dateformatlist['m-d-Y'] = 'm-d-yy';

		return $dateformatlist;
	}

	/**
	 * A function to return fill array with possible lengths of generated code
	 * @return array with posible lengths of the coupon code
	 */
	function GetListCodeLength()
	{
		$code_lengthlist = array();
		for ($i = 1; $i <= 12; $i++) {
			$code_lengthlist[$i] = $i;
		}

		return $code_lengthlist;
	}

	/**
	 * A function to return fill array with possible pagelimits
	 * @return array with posible number of coupon codes in page
	 */
	function GetListPageLimit()
	{
		$modops = cmsms()->GetModuleOperations();
		$DTI = $modops->get_module_instance('SCoupons');

		$pagelimitlist = array();
		$pagelimitlist['5'] = 5;
		$pagelimitlist['10'] = 10;
		$pagelimitlist['25'] = 25;
		$pagelimitlist['50'] = 50;
		$pagelimitlist['100'] = 100;
		$pagelimitlist[$DTI->Lang('unlimited')] = 99999999;

		return $pagelimitlist;
	}
}
