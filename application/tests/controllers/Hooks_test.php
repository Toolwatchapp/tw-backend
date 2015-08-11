<?php

class Hooks_test extends TestCase {

	public function test_index() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbusers'
			]
		);

		$this->assertContains('2. ', $output);
	}

	public function test_indexMeasures() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbmeasures'
			]
		);

		$this->assertContains('1. ', $output);
	}

	public function test_indexWatch() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack nbwatches'
			]
		);

		$this->assertContains('1. ', $output);
	}

	public function test_indexWhois() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack whois mathieu@gmail.com'
			]
		);

		$this->assertContains('Id ', $output);
	}

	public function test_indexHelp() {
		$output = $this->request(
			'POST',
			['Hooks', 'index'],
			[
				'token' => 'bPiAi9XNEa3p9FF1lQnZfuUY',
				'text'  => 'Jack help'
			]
		);

		$this->assertContains('Jack nbusers ; Jack nbmeasures ; Jack nbwatches; Jack whois email.', $output);
	}

}