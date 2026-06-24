<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

class SeoPageContent extends APIModel {

	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/seo_page_content');
		$query->addGETParams($params);
		return $query->exec();
	}

	protected function getFirst(array $params = []) {
		$id = Hash::get($params, 'id');
		if ($id === null || !is_numeric($id)) {
			return false;
		}
		unset($params['id']);
		$query = $this->newQuery();
		$query->setUrl('v2_00/seo_page_content/' . $id);
		$query->addGETParams($params);
		return $query->exec();
	}

	/**
	 * Returns an associative array keyed by page_key for a given partner and locale.
	 * The API returns flat _fr/_nl fields; this picks the right one based on locale.
	 * Result: [ 'comparator_index' => ['title' => '...', 'h1' => '...', 'meta_description' => '...', ...], ... ]
	 */
	public function findAllIndexed($partner_id, $locale) {
		$results = $this->find('all', [
			'partner_id' => $partner_id,
			'count'      => 'max',
		]);

		if (empty($results)) {
			return [];
		}

		$lang = (stripos($locale, 'fr') !== false) ? 'fr' : 'nl';

		$indexed = [];
		foreach ($results as $item) {
			$key = Hash::get($item, 'page_key');
			if ($key === null) {
				continue;
			}
			$indexed[$key] = [
				'title'            => Hash::get($item, 'title_' . $lang),
				'h1'               => Hash::get($item, 'h1_' . $lang),
				'meta_description' => Hash::get($item, 'meta_description_' . $lang),
				'content_top'      => Hash::get($item, 'content_top_' . $lang),
				'content_bottom'   => Hash::get($item, 'content_bottom_' . $lang),
				'answer'           => Hash::get($item, 'answer_' . $lang),
			];
		}

		return $indexed;
	}
}
