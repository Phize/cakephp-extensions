<?php
App::import('Controller', 'Controller');
App::import('Component', 'Seo');

class SeoComponentTestController extends Controller {
	public $components = array('Seo');
	public $uses = null;
}

class SeoComponentTestCase extends CakeTestCase {
	public function start() {
		parent::start();
	}

	public function end() {
		parent::end();
	}

	public function startCase() {
		parent::startCase();
	}

	public function endCase() {
		parent::endCase();
	}

	public function startTest($method) {
		parent::startTest($method);
		$this->Controller =& new SeoComponentTestController();
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->Controller->beforeFilter();
		$this->Controller->Component->startup($this->Controller);
	}

	public function endTest($method) {
		parent::endTest($method);
		unset($this->Controller);
	}

	public function testTitleWithoutOptions() {
		$result = $this->Controller->Seo->title();
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], '');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], '');

		$result = $this->Controller->Seo->title('');
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], '');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], '');

		$result = $this->Controller->Seo->title(null);
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], '');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], '');

		$result = $this->Controller->Seo->title(' ページタイトル　');
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], 'ページタイトル');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], 'ページタイトル');
	}

	public function testTitleWithOptions() {
		$options = array('site_name' => 'サイト名', 'site_desc' => '説明', 'separator' => ' - ');
		$result = $this->Controller->Seo->title(null, $options);
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], '説明 - サイト名');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], 'サイト名');

		$options = array('site_name' => 'サイト名', 'site_desc' => '説明', 'separator' => ' - ');
		$result = $this->Controller->Seo->title('', $options);
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], '説明 - サイト名');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], 'サイト名');

		$options = array('site_name' => 'サイト名', 'site_desc' => '説明', 'separator' => ' - ');
		$result = $this->Controller->Seo->title(' ページタイトル　', $options);
		$this->assertIdentical($this->Controller->viewVars['title_for_layout'], 'ページタイトル - サイト名');
		$this->assertIdentical($this->Controller->viewVars['heading_for_layout'], 'ページタイトル');
	}
}
