<?php namespace ProcessWire;

class PagesUserSortable extends WireData implements Module {

	public function init() {
		// Don't do anything if the user didn't ask for custom sorting
		if (!$this->input->get("sort")) return;

		$this->selector = $this->getSelector();

		$this->pages->addHookBefore("find", $this, "usersortable");
	}



	private function getSelector() {
		// Parse and sanitize the sort data
		$sort = explode(",", $this->input->get("sort"));
		foreach ($sort as $key => $value) {
			if (substr($value, 0, 1) == "-") {
				$prefix = "-";
				$value = substr($value, 1);
			} else {
				$prefix = "";
			}

			$value = $this->sanitizer->fieldName($value);
			if ($value) {
				$sort[$key] = $prefix . $value;
			} else {
				unset($sort[$key]);
			}
		}

		// Whitelist sanitized sorting for pagination etc.
		$this->input->whitelist("sort", implode(",", $sort));

		// Return the sort selector
		return "sort=" . implode(", sort=", $sort);
	}



	public function usersortable(HookEvent $event) {
		$selector = $event->arguments("selector");
		$selector = str_replace("sort=usersortable", $this->selector, $selector);
		$event->arguments("selector", $selector);
	}

}
