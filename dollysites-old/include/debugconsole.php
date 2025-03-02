<?php
namespace DollySites;

class DebugConsole
{
	final public function AddLines(string ...$lines) : DebugConsole
	 {
		array_push($this->lines, ...$lines);
		return $this;
	 }

	final public function AddLine(string ...$chunks) : DebugConsole
	 {
		$this->lines[] = implode(PHP_EOL, $chunks);
		return $this;
	 }

	final public function GetLines() : array { return $this->lines; }

	private $lines = [];
}