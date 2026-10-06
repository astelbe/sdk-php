<?php

namespace AstelSDK\API;

use CakeUtility\Hash;

/**
 * Translation keys edited in the Astel CRM (GET v2_00/translation_key).
 * The API returns the default texts (partner_id 0) and the texts of the partner of the token.
 * Short cache: a text changed in the CRM is visible on the websites within 5 minutes.
 */
class TranslationKey extends APIModel {

	protected $customCacheTTL = 300; // 5 min

	protected function getAll(array $params = []) {
		$query = $this->newQuery();
		$query->setUrl('v2_00/translation_key');
		$query->addGETParams($params);

		return $query->exec();
	}

	/**
	 * Texts of a language, the text of the partner replacing the default one. Empty texts are skipped.
	 *
	 * @param string $language FR or NL
	 * @return array [domain => [slug => text]]
	 */
	public function findAllIndexed($language) {
		$results = $this->find('all', ['count' => 'max']);
		if (empty($results)) {
			return [];
		}
		$column = 'content_' . strtolower($language);
		// Default texts first, partner texts after: they replace the default ones
		usort($results, function ($a, $b) {
			return (int)Hash::get($a, 'partner_id') - (int)Hash::get($b, 'partner_id');
		});
		$indexed = [];
		foreach ($results as $item) {
			$text = isset($item[$column]) ? $item[$column] : null;
			if ($text === null || $text === '') {
				continue;
			}
			$indexed[$item['domain']][$item['slug']] = $text;
		}

		return $indexed;
	}
}
