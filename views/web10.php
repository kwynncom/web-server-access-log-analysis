<?php

require_once(__DIR__ . '/../load/dao_wsal.php');
require_once(__DIR__ . '/agentView.php');

class wsal_web_view_10 extends dao_wsal {
	
	const defaultSBack = 110000;
	
	public function __construct() {
		parent::__construct();
		$this->db10();
		$this->do10();

	}
	
	private function db10() {
		$res = $this->lcoll->createIndex(['ts'  => 1]);
		$res = $this->lcoll->createIndex(['ts' => -1]);
		$res = $this->lcoll->createIndex(['n'  =>  1]);
		return;
	}
	
	private function do10() {
		$res = $this->lcoll->find(['ts' => ['$gte' => strtotime('2021-12-31 15:00')]], ['sort' => ['n' => 1]]);
		$h = '';
		foreach($res as $a) {
			
			if ($a['httpCode'] >= 400) continue;
		
			
			$af = agent_view_10::filter($a['agent'], $a['url']);
			$l = strlen($af);
			if ($l <= 4 && $af[$l - 1] === 'B') continue;
			
			$h .= '<tr>';
			// $h .= '<td>' . $a['n'] . '</td>';
			$h .= '<td>' . date('m/d H:i:s', $a['ts']) . '</td>';
			$h .= '<td>' . $a['ip'] . '</td>';			
			// $h .= '<td>' . $a['url'] . '</td>';	
			$h .= '<td>' . $af . '</td>';	
			$h .= '</tr>' . "\n";
			
		}

		echo($h);
	}
}

if (didCLICallMe(__FILE__)) {
	new wsal_view();
}
