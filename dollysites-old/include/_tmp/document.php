<?php
namespace DollySites\Docs;
use \DollySites as DS;
use \MaxieSystems as MS;
use \MaxieSystems\DB;
use \MaxieSystems\Config;
use \MaxieSystems\Document\UI;
use \MaxieSystems\HTML;
use \MaxieSystems\HTTP;
use \MaxieSystems\L10N;
use Select;
use Filter;

Config::RequireFile('tsystemupdates');

class Dashboard extends MS\Document
{
	use MS\TSystemUpdates;

	public static function GetUpdatesURL() { return 'https://update.dollysites.com/'; }
	public static function GetSiteRoot() { return MS\DOCUMENT_ROOT; }

	final public function Show()
	 {
		$orders = $this->GetOrders(true);
		$this->AddCSS('lib.table');
?><table class="mstable_light">
	<caption><?=l10n()->orders?></caption>
	<thead class="mstable_light__header">
		<tr>
			<th><?=l10n()->text?></th>
			<th><?=l10n()->date?></th>
			<th><?=l10n()->action?></th>
		</tr>
	</thead>
	<tbody><?php
		if($orders)
		 {
			foreach($orders as $key => $order)
			 {
				if(@trim($order['content']))
				 {
					$order['id'] = $key;
					$order['content'] = nl2br($order['content']);
					echo $this->MkTRow($order, l10n());
				 }
			 }
		 }
?>	</tbody>
</table><?php
	 }

	final public function Handle()
	 {
		switch($this->ActionGET())
		 {
			case 'del_order':
				$orders = $this->GetOrders();
				unset($orders[$_GET['id']]);
				$this->SaveOrders($orders);
				break;
			case 'check_for_updates':
				// $plugins = [];
				// \Atlanta\Plugin::Each(function($pl) use(&$plugins){$plugins[$pl->GetName()] = $pl->GetVersion();});
				$data = ['product_id' => 'dollysites', 'version' => \IConst::PRODUCT_VERSION, 'key' => \IConst::PRODUCT_KEY];
				// if($plugins) $data['plugins'] = $plugins;
				if($v = DS\Conf()->updates__channel_id) $data['channel_id'] = $v;
				if(!empty($_GET['lang'])) $data['lang'] = $_GET['lang'];
				// \Registry::SetValue('updates', 'last_check', time());
				DS\Conf()->updates__last_check = time();
				$this->CheckForUpdates($data, true);
				break;
		 }
		switch($this->ActionPOST())
		 {
			case 'apply_updates':
				$this->ApplyUpdates(['product_id' => 'dollysites', 'key' => \IConst::PRODUCT_KEY, 'version' => \IConst::PRODUCT_VERSION]);
				break;
			case 'reinstall_update':
				$_POST['items'] = ['core' => \IConst::PRODUCT_VERSION];
				$this->ApplyUpdates(['product_id' => 'dollysites', 'key' => \IConst::PRODUCT_KEY, 'version' => \IConst::PRODUCT_VERSION]);
				break;
		 }
	 }

	final protected function GetOrders($reverse = false)
	 {
		$orders = [];
		if(file_exists($this->fname))
		 {
			$file = file_get_contents($this->fname);
			$orders = unserialize($file);
			if(is_array($orders))
			 {
				if($reverse) $orders = array_reverse($orders, true);
			 }
			else $orders = [];
		 }
		return $orders;
	 }

	final protected function SaveOrders(array $orders)
	 {
		$orders = serialize($orders);
		file_put_contents($this->fname, $orders);
	 }

	final protected function MkTRow(array $row, L10N\Storage $l10n)
	 {
		$btn = "<a href='/system/core.php?__mssm_action=del_order&id=$row[id]'>$l10n->delete</a>";
		return "<tr class='mstable_light__tr'>
		<td class='mstable_light__td'>$row[content]</td>
		<td class='mstable_light__td'>$row[date]</td>
		<td class='mstable_light__td'>$btn</td>
	</tr>";
	 }

	private $fname = MS\DOCUMENT_ROOT.'orders.txt';
}
?>