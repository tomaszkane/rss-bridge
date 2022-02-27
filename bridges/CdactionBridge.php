<?php

class CdactionBridge extends BridgeAbstract {
	const NAME = 'CD-ACTION bridge';
	const URI = 'https://cdaction.pl';
	const DESCRIPTION = 'Fetches the latest posts from given category.';
	const MAINTAINER = 'tomaszkane';
    const PARAMETERS = array( array(
        'category' => array(
            'name' => 'Kategoria',
            'type' => 'list',
            'values' => array(
                'Newsy' => 'newsy',
                'Recenzje' => 'recenzje',
                'Teksty' => [
                    'Publicystyka' => 'publicystyka',
                    'Zapowiedzi' => 'zapowiedzi',
                    'Już graliśmy' => 'juz-gralismy',
                    'Poradniki' => 'poradniki',
                ],
                'Kultura' => 'kultura',
                'Wideo' => 'wideo',
                'Czasopismo' => 'czasopismo',
                'Technologie' => [
                    'Artykuły' => 'artykuly',
                    'Testy' => 'testy',
                ],
                'Na luzie' => [
                    'Konkursy' => 'konkursy',
                    'Nadgodziny' => 'nadgodziny',
                ]
            )
        ))
    );

	public function collectData() {
		$html = getSimpleHTMLDOM($this->getURI() . '/' . $this->getInput('category'));

		$newsJson = $html->find('script#__NEXT_DATA__', 0)->innertext;
		if (!$newsJson = json_decode($newsJson)) {
			return;
		}

		foreach ($newsJson->props->pageProps->dehydratedState->queries[1]->state->data->results as $news) {
			$item = array();
			$item['uri'] = $this->getURI() . '/' . $this->getInput('category') . '/'. $news->slug;
			$item['title'] = $news->title;
			$item['timestamp'] = $news->publishedAt;
			$item['author'] = $news->editor->fullName;
			$item['content'] = $news->lead;
			$item['enclosures'][] = $news->bannerUrl;
			$item['categories'] = array_column($news->tags, 'name');
			$item['uid'] = $news->id;

			$this->items[] = $item;
		}
	}
}
