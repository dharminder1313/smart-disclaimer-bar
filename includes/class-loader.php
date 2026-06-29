<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EVOLNUX_Loader {

	private $actions = [];
	private $filters = [];

	public function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
		$this->actions[] = compact( 'hook', 'callback', 'priority', 'args' );
	}

	public function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {
		$this->filters[] = compact( 'hook', 'callback', 'priority', 'args' );
	}

	public function run() {
		foreach ( $this->filters as $f ) {
			add_filter( $f['hook'], $f['callback'], $f['priority'], $f['args'] );
		}
		foreach ( $this->actions as $a ) {
			add_action( $a['hook'], $a['callback'], $a['priority'], $a['args'] );
		}
	}
}
