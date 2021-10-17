<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 *
 * Code modified from the Cake\Utitlity\Text::slug method
 */

declare(strict_types=1);

namespace Application\Model;

trait SlugTrait
{
    protected $transliterator = 'Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove; Lower()';

    public function getSlug($title = null, $length = null)
    {
        if (null === $title) {
            $title = $this->getTitle();
        }

        if (null === $length) $length = 100;

        $title = preg_replace('/\%/', ' percent', $title);
        $title = preg_replace('/\@/', ' at', $title);
        $title = preg_replace('/\&/', ' and', $title);

        if (mb_strlen((string) $length) > 100) {
            $title = mb_substr($title, 0, 100, 'UTF-8');
        }

        # the code below was copied from CakePHP's Cake\Utility\Text::slug method
        $title = transliterator_transliterate($this->transliterator, $title);

        $regex = '^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}';

        $quote = preg_quote('-', '/');

        $mapper = ['/[' . $regex . ']/mu' => ' ', '/[\s]+/mu' => '-', sprintf('/^[%s]+|[%s]+$/', $quote, $quote) => ''];

        $title = preg_replace(array_keys($mapper), $mapper, $title);

        return $title;
    }

    abstract public function getTitle();
}
