<?php

namespace DOGNDS\Store;

interface Flushable
{
	/**
	 * Force save of any unsaved changes.
	 */
	public function flush();
}